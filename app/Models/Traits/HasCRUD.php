<?php

namespace App\Models\Traits;

trait HasCRUD
{
    public static function createEntity(array $data, array $relations = []): self
    {
        // Нормализуем имена связей для синхронизации: categories.descendants -> categories
        $relationsForSync = array_values(array_unique(array_map(function ($rel) {
            return explode('.', $rel)[0] ?? $rel;
        }, $relations)));

        // Выделяем данные для связей из входного payload
        $relationData = [];
        foreach ($relationsForSync as $relation) {
            if (isset($data[$relation])) {
                $relationData[$relation] = $data[$relation];
                unset($data[$relation]);
            }
        }
        
        $entity = static::query()->create($data);

        if (!empty($relationData)) {
            $entity->syncRelations($relationData);
        }

        if (!empty($relations)) {
            $entity->load($relations);
        }

        return $entity;
    }

    public static function showEntity($id, array $relations = []): self
    {
        $entity = static::findOrFail($id);

        if (!empty($relations)) {
            $entity->load($relations);
        }

        return $entity;
    }

    public function updateEntity(array $data, array $relations = []): self
    {
        $relationData = [];

        // Нормализуем имена связей для синхронизации: categories.descendants -> categories
        $relationsForSync = array_values(array_unique(array_map(function ($rel) {
            return explode('.', $rel)[0] ?? $rel;
        }, $relations)));

        foreach ($relationsForSync as $relation) {
            if (isset($data[$relation])) {
                $relationData[$relation] = $data[$relation];
                unset($data[$relation]);
            }
        }

        $this->update($data);
        
        $this->syncRelations($relationData);

        if (!empty($relations)) {
            $this->load($relations);
        }

        return $this;
    }

    public function deleteEntity(): bool
    {

        return $this->delete();
    }

    public static function deleteMany(array $ids): int
    {

        return static::whereIn('id', $ids)->delete();
    }
}
