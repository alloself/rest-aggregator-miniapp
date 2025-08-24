<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Categorizable extends MorphPivot
{
    use HasUuids;

    protected $table = 'categorizables';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'category_id',
        'categorizable_type',
        'categorizable_id',
        'key',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}


