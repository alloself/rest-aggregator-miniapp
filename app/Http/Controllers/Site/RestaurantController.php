<?php

namespace App\Http\Controllers\Site;

use App\Models\Restaurant;
use App\Models\News;
use App\Http\Resources\RestaurantResource;
use App\Http\Resources\NewsResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function show(string $slug)
    {
        try {
            $restaurant = Restaurant::with(['user', 'files', 'images'])
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
        try {

            $restaurant = Restaurant::where('slug', $slug)->firstOrFail();


            $perPage = min($request->integer('per_page', 15), 50);
            $sortBy = $request->enum('sort_by', ['order', 'created_at'], 'order');
            $sortDirection = $request->enum('sort_direction', ['asc', 'desc'], 'asc');
            $withImages = $request->boolean('with_images', false);


            $newsQuery = $restaurant->news()
                ->when($withImages, fn($query) => $query->with('images', 'files'))
                ->orderBy($sortBy, $sortDirection);


            if ($sortBy === 'order') {
                $newsQuery->orderBy('created_at', 'desc');
            }

            $news = $newsQuery->paginate($perPage);

            return NewsResource::collection($news);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Ресторан не найден',
                'error' => 'Restaurant not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Произошла ошибка при загрузке новостей',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
