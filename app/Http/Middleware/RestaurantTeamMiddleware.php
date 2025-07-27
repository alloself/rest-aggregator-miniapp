<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            // Устанавливаем активный ресторан из сессии
            $restaurantId = session('active_restaurant_id');
            
            if ($restaurantId) {
                setPermissionsTeamId($restaurantId);
            }
            
            // Альтернативно, можно получить из параметра маршрута
            if ($request->route('restaurant')) {
                setPermissionsTeamId($request->route('restaurant'));
            }
            
            // Или из заголовка для API
            if ($request->header('X-Restaurant-ID')) {
                setPermissionsTeamId($request->header('X-Restaurant-ID'));
            }
        }

        return $next($request);
    }
}
