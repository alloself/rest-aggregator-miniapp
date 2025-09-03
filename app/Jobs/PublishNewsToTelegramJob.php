<?php

namespace App\Jobs;

use App\Models\News;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\RestaurantChat;
use App\Services\TelegramBotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Публикует новость в Telegram-боте ресторана с вложенными изображениями.
 *
 * Правила отправки:
 * - Если у новости есть изображения: отправляем медиагруппу (InputMediaPhoto[]) с подписью у первого элемента
 * - Если изображений нет: отправляем обычное текстовое сообщение
 * - Отправляем всем пользователям ресторана, у которых есть chat_id (owner + users M2M)
 */
class PublishNewsToTelegramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private string $news_id;

    /**
     * @param string $newsId Идентификатор новости
     */
    public function __construct(string $newsId)
    {
        $this->news_id = $newsId;
    }

    /**
     * Выполнить публикацию.
     */
    public function handle(): void
    {
        Log::info('PublishNewsToTelegramJob: старт', [
            'news_id' => $this->news_id,
        ]);
        /** @var News|null $news */
        $news = News::query()
            ->with(['restaurant', 'images'])
            ->find($this->news_id);

        if (!$news) {
            Log::warning('PublishNewsToTelegramJob: новость не найдена', [
                'news_id' => $this->news_id,
            ]);
            return;
        }

        /** @var Restaurant|null $restaurant */
        $restaurant = $news->restaurant;
        if (!$restaurant || empty($restaurant->telegram_bot_token)) {
            // Нет контекста ресторана или токена — публиковать некуда
            Log::warning('PublishNewsToTelegramJob: отсутствует ресторан или его токен', [
                'news_id' => $news->getKey(),
                'has_restaurant' => (bool) $restaurant,
            ]);
            return;
        }

        $service = new TelegramBotService($restaurant->telegram_bot_token);

        $recipients = $this->collectRecipients($restaurant);
        if ($recipients->isEmpty()) {
            Log::info('PublishNewsToTelegramJob: список получателей пуст', [
                'news_id' => $news->getKey(),
            ]);
            return;
        }

        $text = $this->buildText($news);

        $imageUrls = $news->images->map(function ($file) {
            /** @var \App\Models\File $file */
            return (string) $file->url;
        })->filter(fn ($url) => $url !== '')->values();

        foreach ($recipients as $chatId) {
            try {
                if ($imageUrls->isNotEmpty()) {
                    $this->sendMediaGroup($service, $chatId, $imageUrls, $text);
                } else {
                    $service->sendMessage([
                        'chat_id' => $chatId,
                        'text' => $text,
                        'parse_mode' => 'HTML',
                    ]);
                }
                Log::info('PublishNewsToTelegramJob: отправлено сообщение', [
                    'chat_id' => $chatId,
                    'news_id' => $news->getKey(),
                    'with_images' => $imageUrls->isNotEmpty(),
                ]);
            } catch (\Throwable $e) {
                Log::warning('Не удалось отправить новость в Telegram', [
                    'news_id' => $news->getKey(),
                    'restaurant_id' => $restaurant->getKey(),
                    'chat_id' => $chatId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Собрать список chat_id для рассылки: владелец + прикреплённые пользователи ресторана.
     *
     * @return Collection<int,int|string>
     */
    private function collectRecipients(Restaurant $restaurant): Collection
    {
        $chatIds = collect();

        $users = $restaurant->users()->get();
        foreach ($users as $user) {
            $pivotChatId = $user->pivot?->chat_id;
            if (!empty($pivotChatId)) {
                $chatIds->push($pivotChatId);
            }
        }

        // Все пользователи с ролью telegram_user в контексте ресторана (teams)
        $currentTeam = getPermissionsTeamId();
        setPermissionsTeamId($restaurant->getKey());
        /** @var \Illuminate\Database\Eloquent\Collection<int,User> $usersWithRole */
        $usersWithRole = User::role('telegram_user')->get();
        setPermissionsTeamId($currentTeam);

        foreach ($usersWithRole as $user) {
            $pivotChatId = $restaurant->users()->where('users.id', $user->id)->first()?->pivot?->chat_id;
            if (!empty($pivotChatId)) {
                $chatIds->push($pivotChatId);
            }
        }

        return $chatIds->unique()->values();
    }

    /**
     * Построить текст сообщения на основе новости.
     */
    private function buildText(News $news): string
    {
        $title = trim((string) $news->title);
        $description = $this->cleanForTelegram((string) $news->description);

        $header = $title !== '' ? $title : 'Новость';
        $body = $description !== '' ? "\n\n{$description}" : '';

        return $header . $body;
    }

    /**
     * Отправить медиагруппу (до 10 фото). Подпись — только у первого элемента.
     * Telegram требует массив объектов InputMediaPhoto, поле media — HTTP URL.
     *
     * @param Collection<int,string> $imageUrls
     */
    private function sendMediaGroup(TelegramBotService $service, int|string $chatId, Collection $imageUrls, string $caption): void
    {
        // Ограничим до 10 фото согласно Bot API
        $limited = $imageUrls->take(10)->values();

        $media = [];
        foreach ($limited as $index => $url) {
            $item = [
                'type' => 'photo',
                'media' => $url,
            ];

            if ($index === 0) {
                // Безопасная длина для caption (фото: ~1024 символа)
                $item['caption'] = mb_substr($caption, 0, 1000);
                $item['parse_mode'] = 'HTML';
            }

            $media[] = $item;
        }

        $service->sendMediaGroup([
            'chat_id' => $chatId,
            'media' => $media,
        ]);
    }

    /**
     * Очистить HTML до простого текста, нормализовать пробелы.
     */
    private function cleanForTelegram(string $text): string
    {
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace(["\xC2\xA0", "&nbsp;"], ' ', $text);
        $text = (string) preg_replace('/\s+/u', ' ', $text);
        return trim($text);
    }
}


