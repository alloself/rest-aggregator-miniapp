<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Traits\HasCRUD;
use App\Models\Traits\HasList;
use App\Models\Traits\HasImages;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends BaseModel
{
    use HasCRUD, HasList, HasSlug, SoftDeletes, HasImages;

    protected $fillable = [
        'slug',
        'title',
        'description',
        'restaurant_id',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->allowDuplicateSlugs();
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
