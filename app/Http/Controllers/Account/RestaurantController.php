<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\BaseCRUDController;
use App\Models\Restaurant;
use App\Models\User;
use App\Http\Resources\Account\RestaurantResource;
use App\Support\RestaurantLegalDocument;
use App\Services\TelegramBotSetupService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RestaurantController extends BaseCRUDController
{
    public function model(): string
    {
        return Restaurant::class;
    }

    public function resource(): string
    {
        return RestaurantResource::class;
    }

    public $actions = [
        'index',
        'show',
        'update',
        'store',
    ];

    public function store(Request $request)
    {
        if (!$this->hasAction('store')) {
            return response()->json(['message' => 'Store action is not allowed'], 405);
        }

        return DB::transaction(function () use ($request) {
            $relations = $request->input('relations', []);
            $payload = $this->sanitizeRestaurantPayload(
                $request->all(),
                null,
                $request->user(),
            );
            $entity = $this->model()::createEntity($payload, $relations);

            return new RestaurantResource($entity);
        });
    }

    public function update(Request $request, string $id)
    {
        if (!$this->hasAction('update')) {
            return response()->json(['message' => 'Update action is not allowed'], 405);
        }

        return DB::transaction(function () use ($id, $request) {
            /** @var Restaurant $entity */
            $entity = $this->model()::findOrFail($id);
            $relations = $request->input('relations', []);
            $payload = $this->sanitizeRestaurantPayload(
                $request->all(),
                $entity,
                $request->user(),
            );

            $entity->updateEntity($payload, $relations);

            return new RestaurantResource($entity);
        });
    }

    public function index(Request $request)
    {
        $request->validate([
            'relations' => 'sometimes|array',
            'items_per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
        ]);

        $relations = $request->input('relations', []);

        $query = Restaurant::query()
            ->whereHas('users', function ($q) {
                $q->where('users.id', Auth::id());
            });

        if ($request->has(['items_per_page', 'page'])) {
            $perPage = (int) $request->input('items_per_page', 15);
            $page = (int) $request->input('page', 1);
            $data = $query->with($relations)->paginate($perPage, ['*'], 'page', $page);
        } else {
            $data = $query->with($relations)->get();
        }

        return RestaurantResource::collection($data);
    }

    /**
     * Синхронизировать настройки Telegram-бота с данными ресторана
     */
    public function syncBotSettings(Restaurant $restaurant): JsonResponse
    {
        $this->authorizeRestaurantAccess($restaurant);

        try {
            app(TelegramBotSetupService::class)->setupBot($restaurant);
            return response()->json(['message' => 'Настройки бота успешно обновлены']);
        } catch (\RuntimeException $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                422
            );
        } catch (\Throwable $e) {
            report($e);
            return response()->json(
                ['message' => 'Ошибка при обновлении настроек бота'],
                500
            );
        }
    }

    private function authorizeRestaurantAccess(Restaurant $restaurant): void
    {
        $hasAccess = $restaurant->users()->where('users.id', Auth::id())->exists();
        if (!$hasAccess) {
            abort(403, 'Forbidden');
        }
    }

    /**
     * Ограничивает изменение юридических PDF только для глобального root
     * и сохраняет уже привязанные документы при обычном обновлении ресторана.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function sanitizeRestaurantPayload(array $payload, ?Restaurant $restaurant, ?User $user): array
    {
        if (!array_key_exists('files', $payload) || !is_array($payload['files'])) {
            return $payload;
        }

        $files = array_values(array_filter(
            $payload['files'],
            static fn (mixed $file): bool => is_array($file) && isset($file['id']),
        ));

        if ($user?->isRoot()) {
            $this->validateLegalFiles($files);
            $payload['files'] = $files;

            return $payload;
        }

        $payload['files'] = array_merge(
            $this->removeLegalFiles($files),
            $this->getExistingLegalFiles($restaurant),
        );

        return $payload;
    }

    /**
     * @param  array<int, array<string, mixed>>  $files
     */
    private function validateLegalFiles(array $files): void
    {
        $usedKeys = [];

        foreach ($files as $file) {
            $key = $this->getFilePivotKey($file);

            if (!RestaurantLegalDocument::isLegalFileKey($key)) {
                continue;
            }

            if (isset($usedKeys[$key])) {
                throw ValidationException::withMessages([
                    'files' => ['Для каждого юридического документа можно выбрать только один PDF-файл.'],
                ]);
            }

            $extension = strtolower((string) ($file['extension'] ?? ''));
            if ($extension !== 'pdf') {
                throw ValidationException::withMessages([
                    'files' => ['Юридические документы ресторана можно загружать только в формате PDF.'],
                ]);
            }

            $usedKeys[$key] = true;
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $files
     * @return array<int, array<string, mixed>>
     */
    private function removeLegalFiles(array $files): array
    {
        return array_values(array_filter($files, function (array $file): bool {
            return !RestaurantLegalDocument::isLegalFileKey($this->getFilePivotKey($file));
        }));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getExistingLegalFiles(?Restaurant $restaurant): array
    {
        if (!$restaurant) {
            return [];
        }

        return $restaurant->files()
            ->wherePivotIn('key', RestaurantLegalDocument::keys())
            ->get()
            ->map(static function ($file): array {
                return [
                    'id' => $file->id,
                    'name' => $file->name,
                    'url' => $file->url,
                    'extension' => $file->extension,
                    'pivot' => [
                        'key' => $file->pivot?->key,
                        'order' => $file->pivot?->order ?? 0,
                        'type' => 'file',
                    ],
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $file
     */
    private function getFilePivotKey(array $file): ?string
    {
        $pivot = $file['pivot'] ?? null;
        if (!is_array($pivot)) {
            return null;
        }

        $key = $pivot['key'] ?? null;

        return is_string($key) ? $key : null;
    }
}
