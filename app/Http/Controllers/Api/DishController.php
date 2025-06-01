<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDishRequest;
use App\Http\Requests\UpdateDishRequest;
use App\Http\Resources\DishResource;
use App\Models\Dish;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DishController extends Controller
{
    /**
     * Display a listing of dishes.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Dish::with(['menu.restaurant', 'categories']);

        // Фильтрация по ресторану
        if ($request->has('restaurant_id')) {
            $query->whereHas('menu', function ($q) use ($request) {
                $q->where('restaurant_id', $request->restaurant_id);
            });
        }

        // Фильтрация по меню
        if ($request->has('menu_id')) {
            $query->where('menu_id', $request->menu_id);
        }

        // Фильтрация по категории
        if ($request->has('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Фильтрация по доступности
        if ($request->has('available')) {
            $query->where('is_available', $request->boolean('available'));
        }

        // Фильтрация по цене
        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Поиск
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $dishes = $query->ordered()->paginate(12);

        return DishResource::collection($dishes);
    }

    /**
     * Store a newly created dish.
     */
    public function store(StoreDishRequest $request): DishResource
    {
        $dish = Dish::create($request->validated());

        // Привязываем категории
        if ($request->has('category_ids')) {
            $dish->categories()->attach($request->category_ids);
        }

        return new DishResource($dish->load(['menu', 'categories']));
    }

    /**
     * Display the specified dish.
     */
    public function show(Dish $dish): DishResource
    {
        $dish->load(['menu.restaurant', 'categories', 'likes']);

        return new DishResource($dish);
    }

    /**
     * Update the specified dish.
     */
    public function update(UpdateDishRequest $request, Dish $dish): DishResource
    {
        $dish->update($request->validated());

        // Обновляем категории
        if ($request->has('category_ids')) {
            $dish->categories()->sync($request->category_ids);
        }

        return new DishResource($dish->load(['menu', 'categories']));
    }

    /**
     * Remove the specified dish from storage.
     */
    public function destroy(Dish $dish): JsonResponse
    {
        $dish->delete();

        return response()->json(['message' => 'Dish deleted successfully']);
    }

    /**
     * Toggle dish availability.
     */
    public function toggleAvailability(Dish $dish): DishResource
    {
        $dish->update(['is_available' => !$dish->is_available]);

        return new DishResource($dish);
    }

    /**
     * Get dishes by category.
     */
    public function byCategory(string $categoryId): AnonymousResourceCollection
    {
        $dishes = Dish::whereHas('categories', function ($query) use ($categoryId) {
            $query->where('categories.id', $categoryId);
        })->with(['menu.restaurant', 'categories'])->available()->ordered()->get();

        return DishResource::collection($dishes);
    }

    /**
     * Attach categories to dish.
     */
    public function attachCategories(Dish $dish, Request $request): JsonResponse
    {
        $request->validate([
            'category_ids' => ['required', 'array'],
            'category_ids.*' => ['uuid', 'exists:categories,id']
        ]);

        $dish->categories()->syncWithoutDetaching($request->category_ids);

        return response()->json(['message' => 'Categories attached successfully']);
    }

    /**
     * Detach categories from dish.
     */
    public function detachCategories(Dish $dish, Request $request): JsonResponse
    {
        $request->validate([
            'category_ids' => ['required', 'array'],
            'category_ids.*' => ['uuid', 'exists:categories,id']
        ]);

        $dish->categories()->detach($request->category_ids);

        return response()->json(['message' => 'Categories detached successfully']);
    }
}
