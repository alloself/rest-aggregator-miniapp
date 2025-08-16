<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AccountPermissionMiddleware
{
    /**
     * Проверяет права на доступ к /api/account/* по действию и контроллеру с учётом team-контекста.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            abort(401);
        }

        // Доступ к /api/account/* только для владельца или менеджера текущего ресторана (team)
        // root всегда разрешён (суперпользователь)
        if (! $request->user()->hasAnyRole(['restaurant_owner', 'restaurant_manager', 'root'])) {
            abort(403, 'Forbidden');
        }

        $controllerFqcn = (string) ($request->route()?->getControllerClass() ?? '');
        $action = (string) ($request->route()?->getActionMethod() ?? '');

        $requiredPermission = $this->resolveRequiredPermission($controllerFqcn, $action);

        if ($requiredPermission === null) {
            return $next($request); // Нет явного маппинга — пропускаем
        }

        if (! $request->user()->can($requiredPermission)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }

    /**
     * Возвращает название требуемого permission для заданного контроллера и экшена.
     */
    private function resolveRequiredPermission(string $controllerFqcn, string $action): ?string
    {
        $controller = class_exists($controllerFqcn)
            ? (new \ReflectionClass($controllerFqcn))->getShortName()
            : '';

        // Маппинг прав по ресурсам
        return match ($controller) {
            'CategoryController' => $this->mapCrudToPermission($action, [
                'index' => 'menu.view',
                'show' => 'menu.view',
                'store' => 'menu.create',
                'update' => 'menu.edit',
                'destroy' => 'menu.delete',
                'deleteMany' => 'menu.delete',
            ]),

            'RestaurantController' => $this->mapCrudToPermission($action, [
                'index' => 'restaurant.manage',
                'show' => 'restaurant.manage',
                'store' => 'restaurant.manage',
                'update' => 'restaurant.edit',
                'destroy' => 'restaurant.delete',
                'deleteMany' => 'restaurant.delete',
            ]),

            'FileController' => $this->mapCrudToPermission($action, [
                'index' => 'restaurant.manage',
                'show' => 'restaurant.manage',
                'store' => 'restaurant.manage',
                'update' => 'restaurant.manage',
                'destroy' => 'restaurant.manage',
                'deleteMany' => 'restaurant.manage',
            ]),

            'NewsController' => $this->mapCrudToPermission($action, [
                'index' => 'restaurant.manage',
                'show' => 'restaurant.manage',
                'store' => 'restaurant.manage',
                'update' => 'restaurant.manage',
                'destroy' => 'restaurant.manage',
                'deleteMany' => 'restaurant.manage',
            ]),

            'EventController' => $this->mapCrudToPermission($action, [
                'index' => 'restaurant.manage',
                'show' => 'restaurant.manage',
                'store' => 'restaurant.manage',
                'update' => 'restaurant.manage',
                'destroy' => 'restaurant.manage',
                'deleteMany' => 'restaurant.manage',
            ]),

            default => null,
        };
    }

    /**
     * Берёт permission по действию CRUD из таблицы соответствий.
     */
    private function mapCrudToPermission(string $action, array $map): ?string
    {
        return $map[$action] ?? null;
    }
}


