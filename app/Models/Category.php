<?php

namespace App\Models;

use App\Models\Traits\HasCRUD;
use App\Models\Traits\HasList;
use Kalnoy\Nestedset\NodeTrait;

class Category extends BaseModel
{
    use NodeTrait, HasCRUD, HasList;


    protected $fillable = [
        'name',
        'slug',
        'order',
        'restaurant_id',
        'parent_id',
    ];


    protected $casts = [
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function getParentIdAttribute($value): string | null
    {
        return $value;
    }
}
