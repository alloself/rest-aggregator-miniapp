<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Like extends BaseModel
{
    /**
     * Пользователь, поставивший лайк.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Лайкаемый объект (полиморфная связь).
     */
    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }
}


