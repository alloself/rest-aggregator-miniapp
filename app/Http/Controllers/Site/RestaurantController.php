<?php

namespace App\Http\Controllers\Site;

use App\Models\Restaurant;
use App\Http\Resources\RestaurantResource;
use App\Http\Controllers\Controller;

class RestaurantController extends Controller
{
    public function show(string $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->first();
        return new RestaurantResource($restaurant);
    }
}
