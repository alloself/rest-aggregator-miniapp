<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Restaurant extends BaseModel
{
    use SoftDeletes, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'address',
        'phone',
        'telegram_bot_token',
        'settings',
        'is_active',
        'created_by_admin_id',
    ];

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'telegram_bot_token' => 'encrypted',
            'settings' => 'json',
            'is_active' => 'boolean',
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
     * Get the admin who created this restaurant.
     */
    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }

    /**
     * Get the menus for the restaurant.
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Get the events for the restaurant.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the news authored by this restaurant.
     */
    public function news(): MorphMany
    {
        return $this->morphMany(News::class, 'author');
    }

    /**
     * Get the collection items that reference this restaurant.
     */
    public function collectionItems(): MorphMany
    {
        return $this->morphMany(CollectionItem::class, 'item');
    }

    /**
     * Scope a query to only include active restaurants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
