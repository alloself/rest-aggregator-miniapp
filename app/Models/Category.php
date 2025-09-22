<?php

namespace App\Models;

use App\Models\Traits\HasCRUD;
use App\Models\Traits\HasList;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use App\Models\Restaurant;
use App\Models\Pivot\Categorizable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends BaseModel
{
    use NodeTrait, HasCRUD, HasList, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'order',
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

    /**
     * Рестораны, к которым привязана категория (полиморфная связь)
     */
    public function restaurants(): MorphToMany
    {
        return $this->morphedByMany(Restaurant::class, 'categorizable')
            ->using(Categorizable::class)
            ->withPivot(['id', 'key', 'order'])
            ->withTimestamps();
    }

    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class);
    }

    /**
     * Синхронизировать рестораны для категории через полиморфный pivot
     * Ожидает массив элементов с ключами restaurant_id или id, а также key, order
     */
    public function syncRestaurants(array $items): void
    {
        $syncData = [];
        foreach ($items as $row) {
            $rid = $row['restaurant_id'] ?? $row['id'] ?? null;
            if (!$rid) {
                continue;
            }
            $syncData[$rid] = [
                'key' => $row['key'] ?? null,
                'order' => (int)($row['order'] ?? 0),
            ];
        }
        if (!empty($syncData)) {
            $this->restaurants()->syncWithoutDetaching($syncData);
        }
    }
}
