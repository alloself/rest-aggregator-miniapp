<?php

namespace App\Models;

use App\Models\Traits\HasCRUD;
use App\Models\Traits\HasList;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Restaurant extends BaseModel
{
    use HasCRUD, HasList, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'working_hours',
        'user_id',
    ];


    protected $casts = [
        'order' => 'integer',
        'working_hours' => 'array',
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
}
