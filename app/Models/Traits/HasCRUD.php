<?php

namespace App\Models\Traits;

trait HasCRUD
{
    public static function createEntity(array $data, array $relations = []): self
    {

        $entity = static::query()->create($data);

        $entity->syncRelations($relations);

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
        foreach ($relations as $relation) {
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
