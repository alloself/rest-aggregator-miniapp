<?php

namespace App\Http\Controllers\Site;

use App\Models\Restaurant;
use App\Http\Resources\RestaurantResource;
use App\Http\Controllers\Controller;

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
}
