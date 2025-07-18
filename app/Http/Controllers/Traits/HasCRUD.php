<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HasCRUD
{
    /**
     * Абстрактный метод для получения класса модели
     */
    abstract public function model(): string;



    /**
     * Получение списка сущностей
     */
    public function index(Request $request)
    {
        $request->validate([
            'with' => 'sometimes|array',
            'items_per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1'
        ]);

        $with = $request->input('with', []);

        $data = $request->has(['items_per_page', 'page'])
            ? $this->model()::getPaginateList($request->all(), $with)
            : $this->model()::getList($request->all(), $with);



        return $data;
    }

    /**
     * Создание новой сущности
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $entity = $this->model()::createEntity($request->all());

            $with = $request->input('with', []);



            return  $entity;
        });
    }

    /**
     * Просмотр конкретной сущности
     */
    public function show(string $id, Request $request)
    {
        $request->validate([
            'with' => 'sometimes|array'
        ]);

        $with = $request->input('with', []);

        $entity = $this->model()::showEntity(
            $id,
            $with
        );



        return $entity;
    }

    /**
     * Обновление сущности
     */
    public function update(Request $request, string $id)
    {
        return DB::transaction(function () use ($id, $request) {
            $entity = $this->model()::findOrFail($id);

            $with = $request->input('with', []);

            $entity->updateEntity($request->all(), $with);
            return $entity;
        });
    }

    /**
     * Удаление сущности
     */
    public function destroy(string $id)
    {
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
