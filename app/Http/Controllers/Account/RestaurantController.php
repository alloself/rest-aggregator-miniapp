<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\BaseCRUDController;
use App\Models\Restaurant;
use App\Http\Resources\Account\RestaurantResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends BaseCRUDController
{
    public function model(): string
    {
        return Restaurant::class;
    }

    public function resource(): string
    {
        return RestaurantResource::class;
    }

    public $actions = [
        'index',
        'show',
        'update',
        'store',
    ];

    public function index(Request $request)
    {
        $request->validate([
            'relations' => 'sometimes|array',
            'items_per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
        ]);

        $relations = $request->input('relations', []);

        $query = Restaurant::query()
            ->whereHas('users', function ($q) {
                $q->where('users.id', Auth::id());
            });

        if ($request->has(['items_per_page', 'page'])) {
            $perPage = (int) $request->input('items_per_page', 15);
            $page = (int) $request->input('page', 1);
            $data = $query->with($relations)->paginate($perPage, ['*'], 'page', $page);
        } else {
            $data = $query->with($relations)->get();
        }

        return RestaurantResource::collection($data);
    }
}
