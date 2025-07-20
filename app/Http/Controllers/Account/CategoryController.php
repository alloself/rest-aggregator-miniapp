<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\BaseCRUDController;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

class CategoryController extends BaseCRUDController
{
    public function model(): string
    {
        return Category::class;
    }

    public function resource(): string
    {
        return CategoryResource::class;
    }

    
}
