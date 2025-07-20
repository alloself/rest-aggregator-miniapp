<?php

namespace App\Models;

use App\Models\Traits\HasCRUD;
use App\Models\Traits\HasList;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends BaseModel
{
    use NodeTrait, HasCRUD, HasList, HasSlug;

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

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->allowDuplicateSlugs();
    }


    public function getParentIdAttribute($value): string | null
    {
        return $value;
    }
}
