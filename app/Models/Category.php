<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends BaseModel
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'color',
        'icon',
        'parent_id',
    ];

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'type' => 'string',
        ]);
    }

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
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

    /**
     * Scope a query to only include root categories (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
