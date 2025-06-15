<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends BaseModel
{
    protected $fillable = [
        'name', 
        'path',
        'mime_type',
        'size',
    ];
}
