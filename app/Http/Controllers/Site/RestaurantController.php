<?php

namespace App\Http\Controllers\Site;

use App\Models\Restaurant;
use App\Http\Resources\Site\RestaurantResource;
use App\Http\Resources\Account\NewsResource;
use App\Http\Resources\Account\EventResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function show(string $slug)
    {
        try {
            $restaurant = Restaurant::with(['files', 'images','categories' => [
                'dishes' => [
                    'files',
                    'images'
                ],
            ]])
                ->where('slug', $slug)
                ->first();

            if (!$restaurant) {
                return response()->json([
                    'message' => 'Ресторан не найден',
                    'error' => 'Restaurant not found'
                ], 404);
            }

            return new RestaurantResource($restaurant);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Произошла ошибка при загрузке ресторана',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function news(Request $request, string $slug)
    {
        return $this->getRestaurantRelatedItems($request, $slug, [
            'relation' => 'news',
            'resource' => NewsResource::class,
            'sort_fields' => ['order', 'created_at'],
            'default_sort' => 'order',
            'fallback_sort' => ['created_at', 'desc'],
            'error_message' => 'Произошла ошибка при загрузке новостей',
        ]);
    }

    public function events(Request $request, string $slug)
    {
        return $this->getRestaurantRelatedItems($request, $slug, [
            'relation' => 'events',
            'resource' => EventResource::class,
            'sort_fields' => ['order', 'start_at', 'created_at'],
            'default_sort' => 'start_at',
            'fallback_sort' => ['start_at', 'asc'],
            'error_message' => 'Произошла ошибка при загрузке событий',
        ]);
    }

    public function event(string $slug, string $eventSlug)
    {
        try {
            $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
            
            $event = $restaurant->events()
                ->with(['images', 'files'])
                ->where('slug', $eventSlug)
                ->firstOrFail();

            return new EventResource($event);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Событие не найдено',
                'error' => 'Event not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Произошла ошибка при загрузке события',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить связанные элементы ресторана (новости, события и т.д.)
     */
    private function getRestaurantRelatedItems(Request $request, string $slug, array $config)
    {
        try {
            $restaurant = Restaurant::where('slug', $slug)->firstOrFail();

            $perPage = min($request->integer('per_page', 15), 50);
            $sortBy = $request->enum('sort_by', $config['sort_fields'], $config['default_sort']);
            $sortDirection = $request->enum('sort_direction', ['asc', 'desc'], 'asc');
            $withImages = $request->boolean('with_images', true);

            $query = $restaurant->{$config['relation']}()
                ->when($withImages, fn($query) => $query->with('images', 'files'))
                ->orderBy($sortBy, $sortDirection);

            if ($sortBy === 'order') {
                $query->orderBy($config['fallback_sort'][0], $config['fallback_sort'][1]);
            }

            $items = $query->paginate($perPage);

            return $config['resource']::collection($items);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Ресторан не найден',
                'error' => 'Restaurant not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $config['error_message'],
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
