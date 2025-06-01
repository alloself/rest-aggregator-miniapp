<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RestaurantController extends Controller
{
    /**
     * Display a listing of restaurants.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Restaurant::with(['menus', 'events' => function ($query) {
            $query->where('status', 'published')->upcoming();
        }]);

        // Фильтрация по активности
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Поиск по названию
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $restaurants = $query->orderBy('name')->paginate(10);

        return RestaurantResource::collection($restaurants);
    }

    /**
     * Store a newly created restaurant.
     */
    public function store(StoreRestaurantRequest $request): RestaurantResource
    {
        $restaurant = Restaurant::create($request->validated());

        return new RestaurantResource($restaurant->load(['menus', 'events']));
    }

    /**
     * Display the specified restaurant.
     */
    public function show(Restaurant $restaurant): RestaurantResource
    {
        $restaurant->load([
            'menus.dishes.categories',
            'events' => function ($query) {
                $query->where('status', 'published');
            },
            'news' => function ($query) {
                $query->where('status', 'published')->latest();
            }
        ]);

        return new RestaurantResource($restaurant);
    }

    /**
     * Update the specified restaurant.
     */
    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant): RestaurantResource
    {
        $restaurant->update($request->validated());

        return new RestaurantResource($restaurant->load(['menus', 'events']));
    }

    /**
     * Remove the specified restaurant from storage.
     */
    public function destroy(Restaurant $restaurant): JsonResponse
    {
        $restaurant->delete();

        return response()->json(['message' => 'Restaurant deleted successfully']);
    }

    /**
     * Get restaurant menu with categories.
     */
    public function menu(Restaurant $restaurant): JsonResponse
    {
        $restaurant->load([
            'menus' => function ($query) {
                $query->where('is_active', true)->ordered();
            },
            'menus.dishes' => function ($query) {
                $query->where('is_available', true)->ordered();
            },
            'menus.dishes.categories'
        ]);

        return response()->json([
            'restaurant' => new RestaurantResource($restaurant),
            'menus' => $restaurant->menus
        ]);
    }

    /**
     * Get restaurant events.
     */
    public function events(Restaurant $restaurant): JsonResponse
    {
        $events = $restaurant->events()
            ->where('status', 'published')
            ->upcoming()
            ->orderBy('start_date')
            ->get();

        return response()->json(['events' => $events]);
    }

    /**
     * Toggle restaurant active status.
     */
    public function toggleActive(Restaurant $restaurant): RestaurantResource
    {
        $restaurant->update(['is_active' => !$restaurant->is_active]);

        return new RestaurantResource($restaurant);
    }
}
