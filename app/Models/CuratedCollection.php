<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuratedCollection extends BaseModel
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'image_path',
        'is_active',
        'sort_order',
        'created_by_admin_id',
    ];

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);
    }

    /**
     * Get the admin who created this collection.
     */
    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }

    /**
     * Get the collection items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CollectionItem::class, 'collection_id');
    }

    /**
     * Scope a query to only include active collections.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by collection type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
