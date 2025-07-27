<?php

namespace App\Models\Pivot;

use App\Models\File;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Fileable extends MorphPivot
{
    use HasUuids;

    protected $fillable = [
        'file_id',
        'fileable_id',
        'fileable_type'
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
