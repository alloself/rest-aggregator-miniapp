<?php

namespace App\Models\Pivot;

use App\Models\File;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Fileable extends MorphPivot
{
    use HasUuids;

    protected $table = 'fileables';

    protected $fillable = [
        'file_id',
        'fileable_id',
        'fileable_type',
        'key',
        'order',
        'type'
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
