<?php

namespace App\Models\Traits;

trait HasCRUD
{
    public static function createEntity(array $data, array $relations = []): self
    {

        $entity = static::query()->create($data);

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

        $this->update($data);

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
