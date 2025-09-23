<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            // 1) query string при открытии SPA: /account?restaurant={uuid}
            if ($request->query('restaurant')) {
                $restaurantId = (string) $request->query('restaurant');
                setPermissionsTeamId($restaurantId);
                session(['active_restaurant_id' => $restaurantId]);
            }

            // 2) параметр маршрута
            if ($request->route('restaurant')) {
                setPermissionsTeamId($request->route('restaurant'));
                session(['active_restaurant_id' => $request->route('restaurant')]);
            }

            // 3) заголовок (для API вызовов из фронта)
            if ($request->header('X-Restaurant-ID')) {
                setPermissionsTeamId($request->header('X-Restaurant-ID'));
                session(['active_restaurant_id' => $request->header('X-Restaurant-ID')]);
            }

            // 4) если в этом запросе контекст ещё не установлен — берём из сессии
            if (! getPermissionsTeamId() && session('active_restaurant_id')) {
                setPermissionsTeamId(session('active_restaurant_id'));
            }

            // 5) если всё ещё нет контекста — берём первый ресторан пользователя (через pivot ресторанов)
            if (!getPermissionsTeamId()) {
                $firstRestaurantId = DB::table('restaurant_user')
                    ->where('user_id', Auth::id())
                    ->value('restaurant_id');

                if ($firstRestaurantId) {
                    setPermissionsTeamId($firstRestaurantId);
                    session(['active_restaurant_id' => $firstRestaurantId]);
                }
            }
        }

        return $next($request);
    }
}
