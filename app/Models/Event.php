<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Event extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'max_participants',
        'price',
        'status',
    ];

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'max_participants' => 'integer',
            'price' => 'decimal:2',
        ]);
    }

    /**
     * Get the restaurant that owns the event.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get all likes for this event.
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Get the collection items that reference this event.
     */
    public function collectionItems(): MorphMany
    {
        return $this->morphMany(CollectionItem::class, 'item');
    }

    /**
     * Scope a query to only include published events.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    /**
     * Scope a query to only include events happening today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('start_date', today());
    }

    /**
     * Scope a query to only include events happening this week.
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('start_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query to filter events by date range.
     */
    public function scopeDateRange($query, $start = null, $end = null)
    {
        if ($start) {
            $query->where('start_date', '>=', $start);
        }
        if ($end) {
            $query->where('end_date', '<=', $end);
        }
        return $query;
    }

    /**
     * Get the likes count for this event.
     */
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    /**
     * Check if the event is currently happening.
     */
    public function getIsActiveAttribute(): bool
    {
        $now = now();
        return $this->start_date <= $now && $this->end_date >= $now;
    }
}
