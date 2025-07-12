<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use HasUuids, NodeTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'order',
        'restaurant_id',
        'parent_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent key name for nested sets.
     *
     * @return string
     */
    public function getParentIdName(): string
    {
        return 'parent_id';
    }

    /**
     * Get the "left" key name for nested sets.
     *
     * @return string
     */
    public function getLftName(): string
    {
        return '_lft';
    }

    /**
     * Get the "right" key name for nested sets.
     *
     * @return string
     */
    public function getRgtName(): string
    {
        return '_rgt';
    }

    /**
     * Связь с рестораном
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Связь с блюдами
     */
    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class);
    }

    /**
     * Scope для фильтрации по ресторану
     */
    public function scopeByRestaurant($query, $restaurantId)
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    /**
     * Scope для корневых категорий (без родителей)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope для сортировки по order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Получить полный путь категории (breadcrumb)
     */
    public function getFullPathAttribute(): string
    {
        return $this->ancestors()->pluck('name')->push($this->name)->implode(' → ');
    }

    /**
     * Проверить можно ли удалить категорию (нет блюд)
     */
    public function canBeDeleted(): bool
    {
        return $this->dishes()->count() === 0 && $this->children()->count() === 0;
    }
    
    public function getParentIdAttribute($value): string | null
    {
      return $value;
    }

    /**
     * Автоматическая генерация slug перед сохранением
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            if (empty($category->slug)) {
                $category->slug = str($category->name)->slug();
            }
        });
    }
}
