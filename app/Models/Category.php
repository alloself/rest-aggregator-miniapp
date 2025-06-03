<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasNestedSet;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends BaseModel
{
    use HasNestedSet, HasSlug;

    protected $hidden = [
        '_lft',
        '_rgt', 
    ];

    protected $fillable = [
        'name',
        'slug',
        'type',
        'color',
        'icon',
        'parent_id',
        '_lft',
        '_rgt',
        'depth',
    ];

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'type' => 'string',
        ]);
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get all dishes in this category.
     */
    public function dishes(): BelongsToMany
    {
        return $this->belongsToMany(Dish::class, 'dish_categories');
    }

    /**
     * Scope a query to only include seasonal categories.
     */
    public function scopeSeasonal($query)
    {
        return $query->where('type', 'seasonal');
    }

    /**
     * Scope a query to only include cuisine categories.
     */
    public function scopeCuisine($query)
    {
        return $query->where('type', 'cuisine');
    }

    /**
     * Scope a query to only include custom categories.
     */
    public function scopeCustom($query)
    {
        return $query->where('type', 'custom');
    }
}
