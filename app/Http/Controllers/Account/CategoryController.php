<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\BaseCRUDController;
use App\Models\Category;

class CategoryController extends BaseCRUDController
{
    public function model(): string
    {
        return Category::class;
    }
}
