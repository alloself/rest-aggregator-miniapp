<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait HasCRUD
{
    /**
     * Абстрактный метод для получения класса модели
     */
    abstract public function model(): string;

    /**
     * Абстрактный метод для получения класса ресурса
     */
    abstract public function resource(): string;

    /**
     * Проверка доступности действия
     */
    public function hasAction(string $action): bool
    {
        return in_array($action, $this->actions ?? []);
    }

    /**
     * Получение списка доступных действий
     */
    public function getActions(): array
    {
        return $this->actions ?? [];
    }


    /**
     * Получение списка сущностей
     */
    public function index(Request $request)
    {
        if (!$this->hasAction('index')) {
            return response()->json(['message' => 'Index action is not allowed'], 405);
        }

        $request->validate([
            'relations' => 'sometimes|array',
            'items_per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1'
        ]);

        $relations = $request->input('relations', []);

        $data = $request->has(['items_per_page', 'page'])
            ? $this->model()::getPaginateList($request->all(), $relations)
            : $this->model()::getList($request->all(), $relations);

        $resourceClass = $this->resource();
        return $resourceClass::collection($data);
    }

    /**
     * Создание новой сущности
     */
    public function store(Request $request)
    {
        if (!$this->hasAction('store')) {
            return response()->json(['message' => 'Store action is not allowed'], 405);
        }

        return DB::transaction(function () use ($request) {
            $relations = $request->input('relations', []);
            $entity = $this->model()::createEntity($request->all(), $relations);

            $resourceClass = $this->resource();
            return new $resourceClass($entity);
        });
    }

    /**
     * Просмотр конкретной сущности
     */
    public function show(string $id, Request $request)
    {
        if (!$this->hasAction('show')) {
            return response()->json(['message' => 'Show action is not allowed'], 405);
        }

        $request->validate([
            'relations' => 'sometimes|array'
        ]);

        $relations = $request->input('relations', []);

        $entity = $this->model()::showEntity(
            $id,
            $relations
        );

        $resourceClass = $this->resource();
        return new $resourceClass($entity);
    }

    /**
     * Обновление сущности
     */
    public function update(Request $request, string $id)
    {
        if (!$this->hasAction('update')) {
            return response()->json(['message' => 'Update action is not allowed'], 405);
        }

        return DB::transaction(function () use ($id, $request) {
            $entity = $this->model()::findOrFail($id);

            $relations = $request->input('relations', []);

            $entity->updateEntity($request->all(), $relations);
            $resourceClass = $this->resource();
            return new $resourceClass($entity);
        });
    }

    /**
     * Удаление сущности
     */
    public function destroy(string $id)
    {
        if (!$this->hasAction('destroy')) {
            return response()->json(['message' => 'Destroy action is not allowed'], 405);
        }

        return DB::transaction(function () use ($id) {
            $entity = $this->model()::findOrFail($id);
            $entity->deleteEntity();
            return response()->noContent();
        });
    }

    /**
     * Массовое удаление сущностей
     */
    public function deleteMany(Request $request)
    {
        if (!$this->hasAction('deleteMany')) {
            return response()->json(['message' => 'Destroy action is not allowed'], 405);
        }

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'string'
        ]);

        return DB::transaction(function () use ($validated) {
            $this->model()::deleteMany($validated['ids']);
            return response()->noContent();
        });
    }
}
