<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Http\Resources\RestaurantResource;
use App\Http\Resources\DishResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\NewsResource;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\Dish;
use App\Models\Event;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class RestaurantController extends Controller
{
    /**
     * Получить список ресторанов с фильтрацией и пагинацией
     */
    public function index(Request $request): JsonResponse
    {
        $query = Restaurant::query()
            ->where('is_active', true);

        // Фильтрация по категориям (временно отключена до добавления отношений)
        // if ($request->has('category')) {
        //     $query->whereHas('categories', function ($q) use ($request) {
        //         $q->where('slug', $request->category);
        //     });
        // }

        // Поиск по названию или описанию
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Фильтрация по рейтингу (временно отключена - нет поля rating)
        // if ($request->has('min_rating')) {
        //     $query->where('rating', '>=', $request->min_rating);
        // }

        // Сортировка
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        switch ($sortBy) {
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortOrder);
                break;
            default:
                $query->orderBy('name', 'asc');
        }

        $restaurants = $query->paginate($request->get('per_page', 12));

        return response()->json([
            'success' => true,
            'data' => RestaurantResource::collection($restaurants),
            'pagination' => [
                'current_page' => $restaurants->currentPage(),
                'last_page' => $restaurants->lastPage(),
                'per_page' => $restaurants->perPage(),
                'total' => $restaurants->total(),
            ],
            'filters' => [
                'category' => $request->category,
                'search' => $request->search,
                'min_rating' => $request->min_rating,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ]
        ]);
    }

    /**
     * Получить детальную информацию о ресторане по slug
     */
    public function show(string $slug): JsonResponse
    {
        $cacheKey = "restaurant.slug.{$slug}";
        
        $restaurant = Cache::remember($cacheKey, 3600, function () use ($slug) {
            return Restaurant::with([
                'menus',
                'events'
            ])->where('slug', $slug)->firstOrFail();
        });

        return response()->json([
            'success' => true,
            'data' => new RestaurantResource($restaurant)
        ]);
    }

    /**
     * Получить меню ресторана с категориями
     */
    public function menu(string $slug, Request $request): JsonResponse
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        $query = Dish::whereHas('menu', function ($q) use ($restaurant) {
                $q->where('restaurant_id', $restaurant->id);
            })
            ->with(['categories'])
            ->where('is_available', true);

        // Фильтрация по категории блюд
        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Фильтрация по диетическим ограничениям
        if ($request->has('dietary')) {
            $dietary = explode(',', $request->dietary);
            $query->where(function ($q) use ($dietary) {
                foreach ($dietary as $diet) {
                    $q->where('dietary_info->' . $diet, true);
                }
            });
        }

        // Сортировка блюд
        $sortBy = $request->get('sort_by', 'sort_order');
        switch ($sortBy) {
            case 'name':
                $query->orderBy('name');
                break;
            case 'price':
                $query->orderBy('price');
                break;
            default:
                $query->orderBy('sort_order')->orderBy('name');
        }

        $dishes = $query->get();
        
        // Группировка по категориям
        $groupedDishes = $dishes->groupBy(function ($dish) {
            return $dish->categories->first()?->name ?? 'Другие';
        });

        return response()->json([
            'success' => true,
            'data' => [
                'restaurant' => new RestaurantResource($restaurant),
                'menu' => $groupedDishes->map(function ($dishes, $categoryName) {
                    return [
                        'category' => $categoryName,
                        'dishes' => DishResource::collection($dishes)
                    ];
                })->values()
            ]
        ]);
    }

    /**
     * Получить события ресторана
     */
    public function events(string $slug, Request $request): JsonResponse
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        $query = Event::where('restaurant_id', $restaurant->id)
            ->where('status', 'published')
            ->where('start_date', '>=', now());

        // Фильтрация по типу события
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Фильтрация по датам
        if ($request->has('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->where('start_date', '<=', $request->date_to);
        }

        $events = $query->orderBy('start_date')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => [
                'restaurant' => new RestaurantResource($restaurant),
                'events' => EventResource::collection($events)
            ],
            'pagination' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'total' => $events->total(),
            ]
        ]);
    }

    /**
     * Получить новости ресторана
     */
    public function news(string $slug, Request $request): JsonResponse
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        $query = News::where('author_id', $restaurant->id)
            ->where('author_type', Restaurant::class)
            ->where('status', 'published');

        $news = $query->orderBy('published_at', 'desc')
            ->paginate($request->get('per_page', 6));

        return response()->json([
            'success' => true,
            'data' => [
                'restaurant' => new RestaurantResource($restaurant),
                'news' => NewsResource::collection($news)
            ],
            'pagination' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'total' => $news->total(),
            ]
        ]);
    }

    /**
     * Добавить/удалить ресторан из избранного
     */
    public function toggleFavorite(string $slug): JsonResponse
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Временная заглушка - функция избранного будет реализована позже
        return response()->json([
            'success' => true,
            'message' => 'Функция избранного временно недоступна',
            'is_favorite' => false
        ]);
    }

    /**
     * Поставить/убрать лайк блюду
     */
    public function toggleDishLike(string $restaurantSlug, string $dishId): JsonResponse
    {
        $restaurant = Restaurant::where('slug', $restaurantSlug)->firstOrFail();
        $dish = Dish::whereHas('menu', function ($q) use ($restaurant) {
            $q->where('restaurant_id', $restaurant->id);
        })->findOrFail($dishId);
        // Для Telegram Mini App используем telegram_user_id из запроса
        $telegramUserId = request()->input('telegram_user_id');

        if (!$telegramUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Telegram User ID требуется для лайков'
            ], 400);
        }

        $isLiked = $dish->likes()->where('telegram_user_id', $telegramUserId)->exists();

        if ($isLiked) {
            $dish->likes()->where('telegram_user_id', $telegramUserId)->delete();
            $message = 'Лайк убран';
        } else {
            $dish->likes()->create(['telegram_user_id' => $telegramUserId]);
            $message = 'Лайк поставлен';
        }

        $likesCount = $dish->likes()->count();

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_liked' => !$isLiked,
            'likes_count' => $likesCount
        ]);
    }

    /**
     * Получить рекомендации шеф-повара
     */
    public function chefRecommendations(string $slug): JsonResponse
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Получаем блюда через меню ресторана (временно берем первые 6 блюд как рекомендации)
        $recommendations = Dish::whereHas('menu', function ($q) use ($restaurant) {
                $q->where('restaurant_id', $restaurant->id);
            })
            ->where('is_available', true)
            ->with(['categories'])
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'restaurant' => new RestaurantResource($restaurant),
                'recommendations' => DishResource::collection($recommendations)
            ]
        ]);
    }
}
