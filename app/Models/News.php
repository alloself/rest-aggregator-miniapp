<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class News extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'image_path',
        'author_id',
        'author_type',
        'status',
        'is_featured',
        'published_at',
    ];

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ]);
    }

    /**
     * Get the author model (restaurant or admin user).
     */
    public function author(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get all likes for this news.
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Get the collection items that reference this news.
     */
    public function collectionItems(): MorphMany
    {
        return $this->morphMany(CollectionItem::class, 'item');
    }

    /**
     * Scope a query to only include published news.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include featured news.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by author type.
     */
    public function scopeByAuthorType($query, $type)
    {
        return $query->where('author_type', $type);
    }

    /**
     * Scope a query to order by publication date.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    /**
     * Get the likes count for this news.
     */
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }
}
