<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Dish extends BaseModel
{
    use SoftDeletes, HasSlug;

    /**
     * Custom interfaces for ModelTyper - исправляем типы
     *
     * @var array
     */
    public array $interfaces = [
        'likes_count' => [
            'type' => 'number',
        ],
    ];

    protected $fillable = [
        'menu_id',
        'name',
        'slug',
        'description',
        'price',
        'image_path',
        'is_available',
        'allergens',
        'sort_order',
    ];

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
            'allergens' => 'json',
            'sort_order' => 'integer',
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
     * Get the menu that owns the dish.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Get the restaurant through the menu.
     */
    public function restaurant()
    {
        return $this->hasOneThrough(Restaurant::class, Menu::class, 'id', 'id', 'menu_id', 'restaurant_id');
    }

    /**
     * Get all categories for this dish.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'dish_categories');
    }

    /**
     * Get all likes for this dish.
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Get the collection items that reference this dish.
     */
    public function collectionItems(): MorphMany
    {
        return $this->morphMany(CollectionItem::class, 'item');
    }

    /**
     * Scope a query to only include available dishes.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopePriceRange($query, $min = null, $max = null)
    {
        if ($min !== null) {
            $query->where('price', '>=', $min);
        }
        if ($max !== null) {
            $query->where('price', '<=', $max);
        }
        return $query;
    }

    /**
     * Get the likes count for this dish.
     */
    public function getLikesCountAttribute(): float
    {
        return (float) $this->likes()->count();
    }
}
