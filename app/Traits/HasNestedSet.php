<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kalnoy\Nestedset\NodeTrait;

trait HasNestedSet
{
    use NodeTrait;

    public function getParentIdAttribute($value): ?string
    {
        return $value;
    }
} 