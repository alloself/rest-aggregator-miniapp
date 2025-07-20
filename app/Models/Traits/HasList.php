<?php

namespace App\Models\Traits;


use Illuminate\Support\Arr;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;

trait HasList
{

    public static function getPaginateList(array $params = [], array $with = []): LengthAwarePaginator
    {
        try {

            $perPage = Arr::get($params, 'items_per_page', 15);
            $page = Arr::get($params, 'page', 1);

            return self::with($with)->paginate($perPage, ['*'], 'page', $page);
        } catch (QueryException $e) {
            throw new \RuntimeException('Data retrieval error' . $e->getMessage());
        }
    }

    public static function getList(array $params = [], array $with = [])
    {
        try {
            return self::with($with)->get();
        } catch (QueryException $e) {
            throw new \RuntimeException('Data retrieval error' . $e->getMessage());
        }
    }
}
