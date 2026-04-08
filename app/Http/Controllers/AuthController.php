<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function me(Request $request)
    {
        $user = $request->user();

        if ($user) {
            return response()->json(array_merge($user->toArray(), [
                'roles' => $user->getRoleNames()->values()->all(),
                'is_root' => $user->isRoot(),
            ]));
        }

        return response()->json(['error' => 'not auth'], 401);
    }
}
