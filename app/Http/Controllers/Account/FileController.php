<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\BaseCRUDController;
use App\Models\File;
use App\Http\Resources\Account\FileResource;

class FileController extends BaseCRUDController
{
    public function model(): string
    {
        return File::class;
    }

    public function resource(): string
    {
        return FileResource::class;
    }

    
}
