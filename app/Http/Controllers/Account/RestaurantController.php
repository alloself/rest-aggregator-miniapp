<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\BaseCRUDController;
use App\Models\Restaurant;
use App\Http\Resources\RestaurantResource;
use Illuminate\Http\JsonResponse;

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

    /**
     * Настроить Telegram бота для ресторана
     */
    public function setupTelegramBot(Restaurant $restaurant): JsonResponse
    {
        $result = $restaurant->setupTelegramBot();
        
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Получить информацию о боте ресторана
     */
    public function getBotInfo(Restaurant $restaurant): JsonResponse
    {
        $botInfo = $restaurant->getBotInfo();
        
        if ($botInfo) {
            return response()->json([
                'success' => true,
                'bot_info' => $botInfo
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Не удалось получить информацию о боте'
        ], 400);
    }

    /**
     * Установить описание чата для ресторана
     */
    public function setChatDescription(Restaurant $restaurant): JsonResponse
    {
        $request = request();
        $request->validate([
            'description' => 'required|string|max:512',
            'chat_id' => 'nullable|string'
        ]);

        $success = $restaurant->setChatDescription(
            $request->input('description'),
            $request->input('chat_id')
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Описание чата успешно установлено'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Не удалось установить описание чата'
        ], 400);
    }
}
