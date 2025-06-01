<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Dish;
use App\Models\Event;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LikeController extends Controller
{
    /**
     * Toggle like for a likeable item.
     */
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'telegram_user_id' => ['required', 'string'],
            'likeable_type' => ['required', 'string', Rule::in(['dish', 'event', 'news'])],
            'likeable_id' => ['required', 'uuid'],
        ]);

        // Определяем класс модели
        $likeableClass = $this->getLikeableClass($request->likeable_type);
        
        // Проверяем существование объекта
        $likeable = $likeableClass::findOrFail($request->likeable_id);

        // Ищем существующий лайк
        $existingLike = Like::where([
            'telegram_user_id' => $request->telegram_user_id,
            'likeable_type' => $likeableClass,
            'likeable_id' => $request->likeable_id,
        ])->first();

        if ($existingLike) {
            // Убираем лайк
            $existingLike->delete();
            $liked = false;
        } else {
            // Добавляем лайк
            Like::create([
                'telegram_user_id' => $request->telegram_user_id,
                'likeable_type' => $likeableClass,
                'likeable_id' => $request->likeable_id,
            ]);
            $liked = true;
        }

        // Получаем актуальное количество лайков
        $likesCount = Like::where([
            'likeable_type' => $likeableClass,
            'likeable_id' => $request->likeable_id,
        ])->count();

        return response()->json([
            'liked' => $liked,
            'likes_count' => $likesCount,
            'message' => $liked ? 'Лайк добавлен' : 'Лайк удален'
        ]);
    }

    /**
     * Get likes for a specific item.
     */
    public function getLikes(Request $request): JsonResponse
    {
        $request->validate([
            'likeable_type' => ['required', 'string', Rule::in(['dish', 'event', 'news'])],
            'likeable_id' => ['required', 'uuid'],
        ]);

        $likeableClass = $this->getLikeableClass($request->likeable_type);

        $likesCount = Like::where([
            'likeable_type' => $likeableClass,
            'likeable_id' => $request->likeable_id,
        ])->count();

        return response()->json(['likes_count' => $likesCount]);
    }

    /**
     * Get user's likes.
     */
    public function getUserLikes(Request $request): JsonResponse
    {
        $request->validate([
            'telegram_user_id' => ['required', 'string'],
            'likeable_type' => ['nullable', 'string', Rule::in(['dish', 'event', 'news'])],
        ]);

        $query = Like::where('telegram_user_id', $request->telegram_user_id);

        if ($request->has('likeable_type')) {
            $likeableClass = $this->getLikeableClass($request->likeable_type);
            $query->where('likeable_type', $likeableClass);
        }

        $likes = $query->with('likeable')->get();

        return response()->json(['likes' => $likes]);
    }

    /**
     * Check if user liked specific items.
     */
    public function checkLikes(Request $request): JsonResponse
    {
        $request->validate([
            'telegram_user_id' => ['required', 'string'],
            'items' => ['required', 'array'],
            'items.*.likeable_type' => ['required', 'string', Rule::in(['dish', 'event', 'news'])],
            'items.*.likeable_id' => ['required', 'uuid'],
        ]);

        $results = [];

        foreach ($request->items as $item) {
            $likeableClass = $this->getLikeableClass($item['likeable_type']);
            
            $liked = Like::where([
                'telegram_user_id' => $request->telegram_user_id,
                'likeable_type' => $likeableClass,
                'likeable_id' => $item['likeable_id'],
            ])->exists();

            $results[] = [
                'likeable_type' => $item['likeable_type'],
                'likeable_id' => $item['likeable_id'],
                'liked' => $liked
            ];
        }

        return response()->json(['results' => $results]);
    }

    /**
     * Get most liked items.
     */
    public function mostLiked(Request $request): JsonResponse
    {
        $request->validate([
            'likeable_type' => ['nullable', 'string', Rule::in(['dish', 'event', 'news'])],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = Like::selectRaw('likeable_type, likeable_id, COUNT(*) as likes_count')
                    ->groupBy('likeable_type', 'likeable_id')
                    ->orderBy('likes_count', 'desc');

        if ($request->has('likeable_type')) {
            $likeableClass = $this->getLikeableClass($request->likeable_type);
            $query->where('likeable_type', $likeableClass);
        }

        $limit = $request->input('limit', 10);
        $mostLiked = $query->limit($limit)->get();

        // Загружаем связанные объекты
        $mostLiked->load('likeable');

        return response()->json(['most_liked' => $mostLiked]);
    }

    /**
     * Get likeable class by type.
     */
    private function getLikeableClass(string $type): string
    {
        return match ($type) {
            'dish' => Dish::class,
            'event' => Event::class,
            'news' => News::class,
            default => throw new \InvalidArgumentException("Invalid likeable type: {$type}")
        };
    }
}
