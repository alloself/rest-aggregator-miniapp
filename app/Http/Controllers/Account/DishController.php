<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\BaseCRUDController;
use App\Models\Dish;
use App\Http\Resources\Account\DishResource;

class DishController extends BaseCRUDController
{
    public function model(): string
    {
        return Dish::class;
    }

    public function resource(): string
    {
        return DishResource::class;
    }
}


