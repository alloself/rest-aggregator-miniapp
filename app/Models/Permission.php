<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasUuids;
    
    // UUID primary key используется в стандартной колонке 'id', не нужно переопределять $primaryKey
    public $incrementing = false;
    protected $keyType = 'string';
}
