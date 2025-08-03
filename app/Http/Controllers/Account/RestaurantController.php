<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\BaseCRUDController;
use App\Models\Restaurant;
use App\Http\Resources\RestaurantResource;
use Illuminate\Http\JsonResponse;

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
}
