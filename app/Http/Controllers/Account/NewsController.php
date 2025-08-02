<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\BaseCRUDController;
use App\Models\News;
use App\Http\Resources\NewsResource;

class NewsController extends BaseCRUDController
{
    public function model(): string
    {   
        return News::class;
    }

    public function resource(): string
    {
        return NewsResource::class;
    }

    public $actions = [
        'index',
        'show',
        'update',
        'store',
    ];
}
