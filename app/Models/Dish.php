<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Traits\HasImages;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Dish extends BaseModel
{
    use HasSlug, HasImages;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'price',
        'category_id',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
        'price' => 'decimal:2',
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
