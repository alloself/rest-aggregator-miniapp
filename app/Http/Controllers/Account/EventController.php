<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\BaseCRUDController;
use App\Models\Event;
use App\Http\Resources\Account\EventResource;

class EventController extends BaseCRUDController
{
    public function model(): string
    {   
        return Event::class;
    }

    public function resource(): string
    {
        return EventResource::class;
    }

    public $actions = [
        'index',
        'show',
        'update',
        'store',
    ];
}