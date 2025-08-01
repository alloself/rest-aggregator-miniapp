<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Traits\HasCRUD;
use App\Models\Traits\HasList;
use Illuminate\Support\Str;

abstract class BaseModel extends Model
{
    use HasUuids, HasCRUD, HasList;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function syncRelations($relationData)
    {
        foreach ($relationData as $relation => $data) {

            $method = 'sync' . Str::studly($relation);

            if (method_exists($this, $method)) {
                $this->$method($data);
            }
        }
    }
}
