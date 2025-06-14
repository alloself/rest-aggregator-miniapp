<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and create API token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Неверные учетные данные.'],
            ]);
        }

        // Определяем тип токена по роли пользователя
        $tokenName = $this->getTokenName($user, $request->device_name);

        // Создаем токен с соответствующими способностями
        $token = $user->createToken($tokenName, $this->getTokenAbilities($user));

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'),
                'restaurant_id' => $user->restaurant_id,
                'restaurant' => $user->restaurant ? [
                    'id' => $user->restaurant->id,
                    'name' => $user->restaurant->name,
                ] : null,
            ],
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->accessToken->expires_at,
        ]);
    }

    /**
     * Create a new restaurant owner account (admin only).
     */
    public function createRestaurantOwner(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'restaurant_id' => ['required', 'uuid', 'exists:restaurants,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'restaurant_id' => $request->restaurant_id,
        ]);

        $user->assignRole('restaurant_owner');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'restaurant_id' => $user->restaurant_id,
            ],
            'message' => 'Владелец ресторана создан успешно',
        ], 201);
    }

    /**
     * Create a new admin account (super admin only).
     */
    public function createAdmin(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('admin');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'message' => 'Администратор создан успешно',
        ], 201);
    }

    /**
     * Get authenticated user information.
     */
    public function user(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        $user->load(['roles', 'restaurant']);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name'),
            'restaurant_id' => $user->restaurant_id,
            'restaurant' => $user->restaurant ? [
                'id' => $user->restaurant->id,
                'name' => $user->restaurant->name,
                'slug' => $user->restaurant->slug,
            ] : null,
        ]);
    }

    /**
     * Logout user (revoke current token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Успешный выход из системы',
        ]);
    }

    /**
     * Logout from all devices (revoke all tokens).
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Выход из всех устройств выполнен',
        ]);
    }

    /**
     * Get current user's tokens.
     */
    public function tokens(Request $request): JsonResponse
    {
        $tokens = $request->user()->tokens()->select(['id', 'name', 'abilities', 'last_used_at', 'created_at'])->get();

        return response()->json(['tokens' => $tokens]);
    }

    /**
     * Revoke specific token.
     */
    public function revokeToken(Request $request): JsonResponse
    {
        $request->validate([
            'token_id' => ['required', 'integer'],
        ]);

        $deleted = $request->user()->tokens()->where('id', $request->token_id)->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Токен не найден'], 404);
        }

        return response()->json(['message' => 'Токен отозван']);
    }

    /**
     * Get token name based on user role and device.
     */
    private function getTokenName(User $user, ?string $deviceName): string
    {
        $role = $user->roles->first()?->name ?? 'user';
        $device = $deviceName ?? 'unknown-device';
        
        return "{$role}-{$device}";
    }

    /**
     * Get token abilities based on user role.
     */
    private function getTokenAbilities(User $user): array
    {
        if ($user->hasRole('admin')) {
            return ['*']; // Все возможности
        }

        if ($user->hasRole('restaurant_owner')) {
            return [
                'restaurant:read',
                'restaurant:update',
                'menu:create',
                'menu:read',
                'menu:update',
                'menu:delete',
                'dish:create',
                'dish:read',
                'dish:update',
                'dish:delete',
                'event:create',
                'event:read',
                'event:update',
                'event:delete',
                'news:create',
                'news:read',
                'news:update',
                'news:delete',
            ];
        }

        return ['read']; // Базовые возможности чтения
    }
}
