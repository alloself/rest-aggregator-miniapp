<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RestaurantTeamMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if ($request->route('restaurant')) {
                $candidate = (string) $request->route('restaurant');
                $restaurantId = $this->normalizeRestaurantId($candidate);
                if ($restaurantId) {
                    setPermissionsTeamId($restaurantId);
                    session(['active_restaurant_id' => $restaurantId]);
                }
            }
            if (!getPermissionsTeamId() && session('active_restaurant_id')) {
                setPermissionsTeamId(session('active_restaurant_id'));
            }


            if (!getPermissionsTeamId()) {
                $restaurants = DB::table('restaurant_user')
                    ->where('user_id', Auth::id())
                    ->pluck('restaurant_id');

                if ($restaurants->count() === 1) {
                    $onlyRestaurantId = (string) $restaurants->first();
                    setPermissionsTeamId($onlyRestaurantId);
                    session(['active_restaurant_id' => $onlyRestaurantId]);
                }
            }

            if (!getPermissionsTeamId()) {
                abort(428, 'Active restaurant context is required');
            }
        }

        return $next($request);
    }

    /**
     * Преобразует входящее значение (UUID либо slug ресторана) в UUID ресторана.
     * Возвращает UUID либо null, если ресторан не найден.
     */
    private function normalizeRestaurantId(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        if (Str::isUuid($value)) {
            return $value;
        }

        $id = DB::table('restaurants')->where('slug', $value)->value('id');
        return $id ? (string) $id : null;
    }
}
