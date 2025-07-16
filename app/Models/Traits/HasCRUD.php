<?php

namespace App\Models\Traits;

trait HasCRUD
{
    public static function createEntity(array $data): self
    {
        $entity = static::query()->create($data);

        return $entity;
    }

    public static function showEntity($id, array $with = []): self
    {
        $entity = static::findOrFail($id);

        return $entity;
    }

    public function updateEntity(array $data, array $with = []): self
    {
        $this->update($data);
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
