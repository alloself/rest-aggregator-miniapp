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
                // Пользовательский метод синхронизации в модели имеет приоритет
                $this->$method($data);
                continue;
            }

            // Универсальная синхронизация для morphToMany/belongsToMany
            if (!method_exists($this, $relation)) {
                continue;
            }

            $relationInstance = $this->$relation();
            $relationClass = get_class($relationInstance);

            // belongsToMany or morphToMany support: ->sync([$id => ['pivot_col' => value]])
            if (strpos($relationClass, 'BelongsToMany') !== false || strpos($relationClass, 'MorphToMany') !== false) {
                // Поддерживаем форматы элементов: { id } | { <relation>_id } | { pivot: {..} }
                if (!is_array($data)) {
                    continue;
                }

                $sync = [];
                foreach ($data as $row) {
                    $id = $row['id'] ?? ($row[$relation . '_id'] ?? ($row['category_id'] ?? null));
                    if (!$id) {
                        continue;
                    }
                    // sync pivot только если явно передан pivot
                    $pivot = is_array($row['pivot'] ?? null) ? $row['pivot'] : null;
                    // Копируем только скаляры (key, order, и т.п.)
                    if ($pivot !== null) {
                        $pivotData = [];
                        foreach ($pivot as $k => $v) {
                            if (is_scalar($v) || is_null($v)) {
                                $pivotData[$k] = $v;
                            }
                        }
                        $sync[$id] = $pivotData;
                    }
                }

                if (!empty($sync)) {
                    $relationInstance->sync($sync);
                }
            }
        }
    }
}
