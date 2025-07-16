<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasCRUD;

abstract class BaseCRUDController extends Controller
{
    use HasCRUD;

    abstract public function model(): string;
}
