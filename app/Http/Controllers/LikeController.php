<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class LikeController extends Controller
{
    /**
     * Карта алиасов для полиморфных типов
     */
    protected const TYPE_ALIASES = [
        'restaurant' => Restaurant::class,
        // Можно добавить другие модели по мере необходимости
        // 'post' => Post::class,
        // 'user' => User::class,
    ];

    /**
     * Получить полное имя класса по алиасу
     */
    protected function resolveTypeAlias(string $type): string
    {
        return self::TYPE_ALIASES[$type] ?? $type;
    }
    
    /**
     * Создать лайк для сущности (полиморфно).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'uuid', 'exists:users,id'],
            'likeable_type' => ['required', 'string'],
            'likeable_id' => ['required', 'uuid'],
        ]);

        // Резолвим алиас в полное имя класса
        $resolvedType = $this->resolveTypeAlias($validated['likeable_type']);

        // Не допускаем дубликатов (уникальный индекс также защитит)
        $exists = Like::where('user_id', $validated['user_id'])
            ->where('likeable_type', $resolvedType)
            ->where('likeable_id', $validated['likeable_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Already liked',
            ], BaseResponse::HTTP_OK);
        }

        $like = Like::create([
            'id' => (string) Str::uuid(),
            'user_id' => $validated['user_id'],
            'likeable_type' => $resolvedType,
            'likeable_id' => $validated['likeable_id'],
        ]);

        return response()->json([
            'data' => $like,
            'message' => 'Liked',
        ], BaseResponse::HTTP_CREATED);
    }

    /**
     * Удалить лайк (по composite ключам).
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'uuid', 'exists:users,id'],
            'likeable_type' => ['required', 'string'],
            'likeable_id' => ['required', 'uuid'],
        ]);

        // Резолвим алиас в полное имя класса
        $resolvedType = $this->resolveTypeAlias($validated['likeable_type']);

        $deleted = Like::where('user_id', $validated['user_id'])
            ->where('likeable_type', $resolvedType)
            ->where('likeable_id', $validated['likeable_id'])
            ->delete();

        return response()->json([
            'deleted' => (bool) $deleted,
            'message' => $deleted ? 'Unliked' : 'Nothing to delete',
        ], BaseResponse::HTTP_OK);
    }
}


