<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\BaseCRUDController;
use App\Models\Restaurant;
use App\Http\Resources\Account\RestaurantResource;
use App\Services\TelegramBotSetupService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
}
