<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

class Like extends BaseModel
{
    protected $fillable = [
        'telegram_user_id',
        'likeable_id',
        'likeable_type',
    ];

    /**
     * Get the likeable model (dish, event, news).
     */
    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to filter by telegram user.
     */
    public function scopeByTelegramUser($query, $telegramUserId)
    {
        return $query->where('telegram_user_id', $telegramUserId);
    }

    /**
     * Scope a query to filter by likeable type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('likeable_type', $type);
    }
}
