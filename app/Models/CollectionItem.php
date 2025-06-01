<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CollectionItem extends BaseModel
{
    protected $fillable = [
        'collection_id',
        'item_id',
        'item_type',
        'sort_order',
    ];

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'sort_order' => 'integer',
        ]);
    }

    /**
     * Get the collection that owns this item.
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(CuratedCollection::class, 'collection_id');
    }

    /**
     * Get the polymorphic item (dish, event, news, restaurant).
     */
    public function item(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Scope a query to filter by item type.
     */
    public function scopeByItemType($query, $type)
    {
        return $query->where('item_type', $type);
    }
}
