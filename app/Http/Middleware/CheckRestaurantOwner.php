<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRestaurantOwner
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Проверяем аутентификацию
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Админы могут все
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Проверяем, что это владелец ресторана
        if (!$user->hasRole('restaurant_owner') || !$user->restaurant_id) {
            return response()->json([
                'message' => 'Доступ запрещен. Требуются права владельца ресторана.'
            ], 403);
        }

        // Если в маршруте есть параметр ресторана, проверяем совпадение
        $restaurantId = $request->route('restaurant')?->id ?? $request->route('restaurant');
        
        if ($restaurantId && $user->restaurant_id !== $restaurantId) {
            return response()->json([
                'message' => 'Доступ запрещен. Вы можете управлять только своим рестораном.'
            ], 403);
        }

        // Автоматически фильтруем контент по ресторану пользователя
        $request->merge(['restaurant_id' => $user->restaurant_id]);

        return $next($request);
    }
}
