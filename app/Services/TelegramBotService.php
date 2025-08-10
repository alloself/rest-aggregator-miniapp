<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

/**
 * Сервис для работы с Telegram Bot API версии 9.1
 * Поддерживает все методы API включая Mini Apps, Business Bot, Affiliate Program
 * 
 * @author AI Assistant
 * @version 2.0
 * @see https://core.telegram.org/bots/api
 */
class TelegramBotService
{
    private const API_BASE_URL = 'https://api.telegram.org/bot';
    private const FILE_API_BASE_URL = 'https://api.telegram.org/file/bot';
    
    private readonly Client $httpClient;
    private readonly string $botToken;
    private readonly string $apiUrl;

    public function __construct(string $botToken)
    {
        $this->botToken = $botToken;
        $this->apiUrl = self::API_BASE_URL . $botToken . '/';
        $this->httpClient = new Client([
            'timeout' => 30,
            'connect_timeout' => 10,
        ]);
    }

    // ========== ОСНОВНЫЕ МЕТОДЫ API ==========

    /**
     * Получить информацию о боте
     */
    public function getMe(): array
    {
        return $this->makeRequest('getMe');
    }

    /**
     * Выйти из аккаунта бота
     */
    public function logOut(): bool
    {
        $response = $this->makeRequest('logOut');
        return $response['result'] ?? false;
    }

    /**
     * Закрыть бота
     */
    public function close(): bool
    {
        $response = $this->makeRequest('close');
        return $response['result'] ?? false;
    }

    // ========== МЕТОДЫ ОТПРАВКИ СООБЩЕНИЙ ==========

    /**
     * Отправить текстовое сообщение
     */
    public function sendMessage(array $params): array
    {
        $requiredParams = ['chat_id', 'text'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendMessage', $params);
    }

    /**
     * Переслать сообщение
     */
    public function forwardMessage(array $params): array
    {
        $requiredParams = ['chat_id', 'from_chat_id', 'message_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('forwardMessage', $params);
    }

    /**
     * Переслать несколько сообщений
     */
    public function forwardMessages(array $params): array
    {
        $requiredParams = ['chat_id', 'from_chat_id', 'message_ids'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('forwardMessages', $params);
    }

    /**
     * Скопировать сообщение
     */
    public function copyMessage(array $params): array
    {
        $requiredParams = ['chat_id', 'from_chat_id', 'message_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('copyMessage', $params);
    }

    /**
     * Скопировать несколько сообщений
     */
    public function copyMessages(array $params): array
    {
        $requiredParams = ['chat_id', 'from_chat_id', 'message_ids'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('copyMessages', $params);
    }

    /**
     * Отправить фото
     */
    public function sendPhoto(array $params): array
    {
        $requiredParams = ['chat_id', 'photo'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendPhoto', $params);
    }

    /**
     * Отправить аудио
     */
    public function sendAudio(array $params): array
    {
        $requiredParams = ['chat_id', 'audio'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendAudio', $params);
    }

    /**
     * Отправить документ
     */
    public function sendDocument(array $params): array
    {
        $requiredParams = ['chat_id', 'document'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendDocument', $params);
    }

    /**
     * Отправить видео
     */
    public function sendVideo(array $params): array
    {
        $requiredParams = ['chat_id', 'video'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendVideo', $params);
    }

    /**
     * Отправить анимацию (GIF)
     */
    public function sendAnimation(array $params): array
    {
        $requiredParams = ['chat_id', 'animation'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendAnimation', $params);
    }

    /**
     * Отправить голосовое сообщение
     */
    public function sendVoice(array $params): array
    {
        $requiredParams = ['chat_id', 'voice'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendVoice', $params);
    }

    /**
     * Отправить видеосообщение
     */
    public function sendVideoNote(array $params): array
    {
        $requiredParams = ['chat_id', 'video_note'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendVideoNote', $params);
    }

    /**
     * Отправить медиагруппу
     */
    public function sendMediaGroup(array $params): array
    {
        $requiredParams = ['chat_id', 'media'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendMediaGroup', $params);
    }

    /**
     * Отправить локацию
     */
    public function sendLocation(array $params): array
    {
        $requiredParams = ['chat_id', 'latitude', 'longitude'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendLocation', $params);
    }

    /**
     * Отправить место
     */
    public function sendVenue(array $params): array
    {
        $requiredParams = ['chat_id', 'latitude', 'longitude', 'title', 'address'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendVenue', $params);
    }

    /**
     * Отправить контакт
     */
    public function sendContact(array $params): array
    {
        $requiredParams = ['chat_id', 'phone_number', 'first_name'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendContact', $params);
    }

    /**
     * Отправить опрос
     */
    public function sendPoll(array $params): array
    {
        $requiredParams = ['chat_id', 'question', 'options'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendPoll', $params);
    }

    /**
     * Отправить кубик (dice)
     */
    public function sendDice(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendDice', $params);
    }

    /**
     * Отправить действие чата (typing, upload_photo, etc.)
     */
    public function sendChatAction(array $params): array
    {
        $requiredParams = ['chat_id', 'action'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendChatAction', $params);
    }

    /**
     * Установить реакцию на сообщение
     */
    public function setMessageReaction(array $params): array
    {
        $requiredParams = ['chat_id', 'message_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setMessageReaction', $params);
    }

    // ========== МЕТОДЫ УПРАВЛЕНИЯ СООБЩЕНИЯМИ ==========

    /**
     * Получить информацию о пользователе
     */
    public function getUserProfilePhotos(array $params): array
    {
        $requiredParams = ['user_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('getUserProfilePhotos', $params);
    }

    /**
     * Получить файл
     */
    public function getFile(array $params): array
    {
        $requiredParams = ['file_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('getFile', $params);
    }

    /**
     * Заблокировать пользователя в чате
     */
    public function banChatMember(array $params): array
    {
        $requiredParams = ['chat_id', 'user_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('banChatMember', $params);
    }

    /**
     * Разблокировать пользователя в чате
     */
    public function unbanChatMember(array $params): array
    {
        $requiredParams = ['chat_id', 'user_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('unbanChatMember', $params);
    }

    /**
     * Ограничить пользователя в чате
     */
    public function restrictChatMember(array $params): array
    {
        $requiredParams = ['chat_id', 'user_id', 'permissions'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('restrictChatMember', $params);
    }

    /**
     * Повысить пользователя до администратора
     */
    public function promoteChatMember(array $params): array
    {
        $requiredParams = ['chat_id', 'user_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('promoteChatMember', $params);
    }

    /**
     * Установить права администратора
     */
    public function setChatAdministratorCustomTitle(array $params): array
    {
        $requiredParams = ['chat_id', 'user_id', 'custom_title'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setChatAdministratorCustomTitle', $params);
    }

    /**
     * Заблокировать пользователя отправлять сообщения в канале
     */
    public function banChatSenderChat(array $params): array
    {
        $requiredParams = ['chat_id', 'sender_chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('banChatSenderChat', $params);
    }

    /**
     * Разблокировать пользователя отправлять сообщения в канале
     */
    public function unbanChatSenderChat(array $params): array
    {
        $requiredParams = ['chat_id', 'sender_chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('unbanChatSenderChat', $params);
    }

    /**
     * Установить права чата по умолчанию
     */
    public function setChatPermissions(array $params): array
    {
        $requiredParams = ['chat_id', 'permissions'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setChatPermissions', $params);
    }

    /**
     * Экспортировать ссылку-приглашение в чат
     */
    public function exportChatInviteLink(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('exportChatInviteLink', $params);
    }

    /**
     * Создать ссылку-приглашение в чат
     */
    public function createChatInviteLink(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('createChatInviteLink', $params);
    }

    /**
     * Редактировать ссылку-приглашение в чат
     */
    public function editChatInviteLink(array $params): array
    {
        $requiredParams = ['chat_id', 'invite_link'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('editChatInviteLink', $params);
    }

    /**
     * Отозвать ссылку-приглашение в чат
     */
    public function revokeChatInviteLink(array $params): array
    {
        $requiredParams = ['chat_id', 'invite_link'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('revokeChatInviteLink', $params);
    }

    /**
     * Одобрить заявку на вступление в чат
     */
    public function approveChatJoinRequest(array $params): array
    {
        $requiredParams = ['chat_id', 'user_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('approveChatJoinRequest', $params);
    }

    /**
     * Отклонить заявку на вступление в чат
     */
    public function declineChatJoinRequest(array $params): array
    {
        $requiredParams = ['chat_id', 'user_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('declineChatJoinRequest', $params);
    }

    // ========== МЕТОДЫ УПРАВЛЕНИЯ ЧАТОМ ==========

    /**
     * Установить фото чата
     */
    public function setChatPhoto(array $params): array
    {
        $requiredParams = ['chat_id', 'photo'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setChatPhoto', $params);
    }

    /**
     * Удалить фото чата
     */
    public function deleteChatPhoto(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('deleteChatPhoto', $params);
    }

    /**
     * Установить название чата
     */
    public function setChatTitle(array $params): array
    {
        $requiredParams = ['chat_id', 'title'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setChatTitle', $params);
    }

    /**
     * Установить описание чата
     */
    public function setChatDescription(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setChatDescription', $params);
    }

    /**
     * Закрепить сообщение в чате
     */
    public function pinChatMessage(array $params): array
    {
        $requiredParams = ['chat_id', 'message_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('pinChatMessage', $params);
    }

    /**
     * Открепить сообщение в чате
     */
    public function unpinChatMessage(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('unpinChatMessage', $params);
    }

    /**
     * Открепить все сообщения в чате
     */
    public function unpinAllChatMessages(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('unpinAllChatMessages', $params);
    }

    /**
     * Покинуть чат
     */
    public function leaveChat(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('leaveChat', $params);
    }

    /**
     * Получить информацию о чате
     */
    public function getChat(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('getChat', $params);
    }

    /**
     * Получить администраторов чата
     */
    public function getChatAdministrators(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('getChatAdministrators', $params);
    }

    /**
     * Получить количество участников чата
     */
    public function getChatMemberCount(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('getChatMemberCount', $params);
    }

    /**
     * Получить информацию об участнике чата
     */
    public function getChatMember(array $params): array
    {
        $requiredParams = ['chat_id', 'user_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('getChatMember', $params);
    }

    /**
     * Установить стикерпак для чата
     */
    public function setChatStickerSet(array $params): array
    {
        $requiredParams = ['chat_id', 'sticker_set_name'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setChatStickerSet', $params);
    }

    /**
     * Удалить стикерпак из чата
     */
    public function deleteChatStickerSet(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('deleteChatStickerSet', $params);
    }

    /**
     * Получить топик-темы форума
     */
    public function getForumTopicIconStickers(): array
    {
        return $this->makeRequest('getForumTopicIconStickers');
    }

    /**
     * Создать тему форума
     */
    public function createForumTopic(array $params): array
    {
        $requiredParams = ['chat_id', 'name'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('createForumTopic', $params);
    }

    /**
     * Редактировать тему форума
     */
    public function editForumTopic(array $params): array
    {
        $requiredParams = ['chat_id', 'message_thread_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('editForumTopic', $params);
    }

    /**
     * Закрыть тему форума
     */
    public function closeForumTopic(array $params): array
    {
        $requiredParams = ['chat_id', 'message_thread_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('closeForumTopic', $params);
    }

    /**
     * Открыть тему форума
     */
    public function reopenForumTopic(array $params): array
    {
        $requiredParams = ['chat_id', 'message_thread_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('reopenForumTopic', $params);
    }

    /**
     * Удалить тему форума
     */
    public function deleteForumTopic(array $params): array
    {
        $requiredParams = ['chat_id', 'message_thread_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('deleteForumTopic', $params);
    }

    /**
     * Открепить все сообщения в теме форума
     */
    public function unpinAllForumTopicMessages(array $params): array
    {
        $requiredParams = ['chat_id', 'message_thread_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('unpinAllForumTopicMessages', $params);
    }

    /**
     * Редактировать название общей темы форума
     */
    public function editGeneralForumTopic(array $params): array
    {
        $requiredParams = ['chat_id', 'name'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('editGeneralForumTopic', $params);
    }

    /**
     * Закрыть общую тему форума
     */
    public function closeGeneralForumTopic(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('closeGeneralForumTopic', $params);
    }

    /**
     * Открыть общую тему форума
     */
    public function reopenGeneralForumTopic(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('reopenGeneralForumTopic', $params);
    }

    /**
     * Скрыть общую тему форума
     */
    public function hideGeneralForumTopic(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('hideGeneralForumTopic', $params);
    }

    /**
     * Показать общую тему форума
     */
    public function unhideGeneralForumTopic(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('unhideGeneralForumTopic', $params);
    }

    /**
     * Открепить все общие темы форума
     */
    public function unpinAllGeneralForumTopicMessages(array $params): array
    {
        $requiredParams = ['chat_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('unpinAllGeneralForumTopicMessages', $params);
    }

    // ========== МЕТОДЫ CALLBACK QUERY ==========

    /**
     * Ответить на callback запрос
     */
    public function answerCallbackQuery(array $params): array
    {
        $requiredParams = ['callback_query_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('answerCallbackQuery', $params);
    }

    // ========== МЕТОДЫ КОМАНД БОТА ==========

    /**
     * Получить команды бота
     */
    public function getMyCommands(array $params = []): array
    {
        return $this->makeRequest('getMyCommands', $params);
    }

    /**
     * Установить команды бота
     */
    public function setMyCommands(array $params): array
    {
        $requiredParams = ['commands'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setMyCommands', $params);
    }

    /**
     * Удалить команды бота
     */
    public function deleteMyCommands(array $params = []): array
    {
        return $this->makeRequest('deleteMyCommands', $params);
    }

    // ========== МЕТОДЫ РЕДАКТИРОВАНИЯ СООБЩЕНИЙ ==========

    /**
     * Редактировать текст сообщения
     */
    public function editMessageText(array $params): array
    {
        $requiredParams = ['text'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('editMessageText', $params);
    }

    /**
     * Редактировать подпись сообщения
     */
    public function editMessageCaption(array $params): array
    {
        return $this->makeRequest('editMessageCaption', $params);
    }

    /**
     * Редактировать медиа в сообщении
     */
    public function editMessageMedia(array $params): array
    {
        $requiredParams = ['media'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('editMessageMedia', $params);
    }

    /**
     * Редактировать live локацию
     */
    public function editMessageLiveLocation(array $params): array
    {
        $requiredParams = ['latitude', 'longitude'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('editMessageLiveLocation', $params);
    }

    /**
     * Остановить live локацию
     */
    public function stopMessageLiveLocation(array $params): array
    {
        return $this->makeRequest('stopMessageLiveLocation', $params);
    }

    /**
     * Редактировать клавиатуру сообщения
     */
    public function editMessageReplyMarkup(array $params): array
    {
        return $this->makeRequest('editMessageReplyMarkup', $params);
    }

    /**
     * Остановить опрос
     */
    public function stopPoll(array $params): array
    {
        $requiredParams = ['chat_id', 'message_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('stopPoll', $params);
    }

    /**
     * Удалить сообщение
     */
    public function deleteMessage(array $params): array
    {
        $requiredParams = ['chat_id', 'message_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('deleteMessage', $params);
    }

    /**
     * Удалить несколько сообщений
     */
    public function deleteMessages(array $params): array
    {
        $requiredParams = ['chat_id', 'message_ids'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('deleteMessages', $params);
    }

    // ========== МЕТОДЫ СТИКЕРОВ ==========

    /**
     * Отправить стикер
     */
    public function sendSticker(array $params): array
    {
        $requiredParams = ['chat_id', 'sticker'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendSticker', $params);
    }

    /**
     * Получить набор стикеров
     */
    public function getStickerSet(array $params): array
    {
        $requiredParams = ['name'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('getStickerSet', $params);
    }

    /**
     * Получить пользовательские эмодзи стикеры
     */
    public function getCustomEmojiStickers(array $params): array
    {
        $requiredParams = ['custom_emoji_ids'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('getCustomEmojiStickers', $params);
    }

    /**
     * Загрузить файл стикера
     */
    public function uploadStickerFile(array $params): array
    {
        $requiredParams = ['user_id', 'sticker', 'sticker_format'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('uploadStickerFile', $params);
    }

    /**
     * Создать новый набор стикеров
     */
    public function createNewStickerSet(array $params): array
    {
        $requiredParams = ['user_id', 'name', 'title', 'stickers', 'sticker_format'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('createNewStickerSet', $params);
    }

    /**
     * Добавить стикер в набор
     */
    public function addStickerToSet(array $params): array
    {
        $requiredParams = ['user_id', 'name', 'sticker'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('addStickerToSet', $params);
    }

    /**
     * Установить позицию стикера в наборе
     */
    public function setStickerPositionInSet(array $params): array
    {
        $requiredParams = ['sticker', 'position'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setStickerPositionInSet', $params);
    }

    /**
     * Удалить стикер из набора
     */
    public function deleteStickerFromSet(array $params): array
    {
        $requiredParams = ['sticker'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('deleteStickerFromSet', $params);
    }

    /**
     * Заменить стикер в наборе
     */
    public function replaceStickerInSet(array $params): array
    {
        $requiredParams = ['user_id', 'name', 'old_sticker', 'sticker'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('replaceStickerInSet', $params);
    }

    /**
     * Установить эмодзи список для стикера
     */
    public function setStickerEmojiList(array $params): array
    {
        $requiredParams = ['sticker', 'emoji_list'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setStickerEmojiList', $params);
    }

    /**
     * Установить ключевые слова для стикера
     */
    public function setStickerKeywords(array $params): array
    {
        $requiredParams = ['sticker'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setStickerKeywords', $params);
    }

    /**
     * Установить маску позицию для стикера
     */
    public function setStickerMaskPosition(array $params): array
    {
        $requiredParams = ['sticker'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setStickerMaskPosition', $params);
    }

    /**
     * Установить название набора стикеров
     */
    public function setStickerSetTitle(array $params): array
    {
        $requiredParams = ['name', 'title'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setStickerSetTitle', $params);
    }

    /**
     * Установить миниатюру набора стикеров
     */
    public function setStickerSetThumbnail(array $params): array
    {
        $requiredParams = ['name', 'user_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setStickerSetThumbnail', $params);
    }

    /**
     * Удалить набор стикеров
     */
    public function deleteStickerSet(array $params): array
    {
        $requiredParams = ['name'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('deleteStickerSet', $params);
    }

    // ========== INLINE РЕЖИМ ==========

    /**
     * Ответить на inline запрос
     */
    public function answerInlineQuery(array $params): array
    {
        $requiredParams = ['inline_query_id', 'results'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('answerInlineQuery', $params);
    }

    /**
     * Ответить на web app запрос
     */
    public function answerWebAppQuery(array $params): array
    {
        $requiredParams = ['web_app_query_id', 'result'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('answerWebAppQuery', $params);
    }

    // ========== МЕТОДЫ ПЛАТЕЖЕЙ ==========

    /**
     * Отправить счет
     */
    public function sendInvoice(array $params): array
    {
        $requiredParams = [
            'chat_id', 'title', 'description', 'payload',
            'provider_token', 'currency', 'prices'
        ];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendInvoice', $params);
    }

    /**
     * Создать ссылку на счет
     */
    public function createInvoiceLink(array $params): array
    {
        $requiredParams = [
            'title', 'description', 'payload',
            'provider_token', 'currency', 'prices'
        ];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('createInvoiceLink', $params);
    }

    /**
     * Ответить на предварительный запрос оплаты
     */
    public function answerPreCheckoutQuery(array $params): array
    {
        $requiredParams = ['pre_checkout_query_id', 'ok'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('answerPreCheckoutQuery', $params);
    }

    /**
     * Ответить на запрос доставки
     */
    public function answerShippingQuery(array $params): array
    {
        $requiredParams = ['shipping_query_id', 'ok'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('answerShippingQuery', $params);
    }

    // ========== ЗВЕЗДНЫЕ ТРАНЗАКЦИИ ==========

    /**
     * Получить звездные транзакции
     */
    public function getStarTransactions(array $params = []): array
    {
        return $this->makeRequest('getStarTransactions', $params);
    }

    /**
     * Вернуть звездную оплату
     */
    public function refundStarPayment(array $params): array
    {
        $requiredParams = ['user_id', 'telegram_payment_charge_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('refundStarPayment', $params);
    }

    // ========== ИГРЫ ==========

    /**
     * Отправить игру
     */
    public function sendGame(array $params): array
    {
        $requiredParams = ['chat_id', 'game_short_name'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendGame', $params);
    }

    /**
     * Установить счет в игре
     */
    public function setGameScore(array $params): array
    {
        $requiredParams = ['user_id', 'score'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setGameScore', $params);
    }

    /**
     * Получить лучшие результаты в игре
     */
    public function getGameHighScores(array $params): array
    {
        $requiredParams = ['user_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('getGameHighScores', $params);
    }

    // ========== МЕТОДЫ НАСТРОЕК БОТА ==========

    /**
     * Установить название бота
     */
    public function setMyName(array $params): array
    {
        return $this->makeRequest('setMyName', $params);
    }

    /**
     * Получить название бота
     */
    public function getMyName(array $params = []): array
    {
        return $this->makeRequest('getMyName', $params);
    }

    /**
     * Установить описание бота
     */
    public function setMyDescription(array $params): array
    {
        return $this->makeRequest('setMyDescription', $params);
    }

    /**
     * Получить описание бота
     */
    public function getMyDescription(array $params = []): array
    {
        return $this->makeRequest('getMyDescription', $params);
    }

    /**
     * Установить краткое описание бота
     */
    public function setMyShortDescription(array $params): array
    {
        return $this->makeRequest('setMyShortDescription', $params);
    }

    /**
     * Получить краткое описание бота
     */
    public function getMyShortDescription(array $params = []): array
    {
        return $this->makeRequest('getMyShortDescription', $params);
    }

    /**
     * Установить фото профиля чат-бота
     */
    public function setChatMenuButton(array $params): array
    {
        return $this->makeRequest('setChatMenuButton', $params);
    }

    /**
     * Получить кнопку меню чата
     */
    public function getChatMenuButton(array $params = []): array
    {
        return $this->makeRequest('getChatMenuButton', $params);
    }

    /**
     * Установить права администратора по умолчанию
     */
    public function setMyDefaultAdministratorRights(array $params): array
    {
        return $this->makeRequest('setMyDefaultAdministratorRights', $params);
    }

    /**
     * Получить права администратора по умолчанию
     */
    public function getMyDefaultAdministratorRights(array $params = []): array
    {
        return $this->makeRequest('getMyDefaultAdministratorRights', $params);
    }

    // ========== ОБНОВЛЕНИЯ ==========

    /**
     * Получить обновления
     */
    public function getUpdates(array $params = []): array
    {
        return $this->makeRequest('getUpdates', $params);
    }

    /**
     * Установить webhook
     */
    public function setWebhook(array $params): array
    {
        $requiredParams = ['url'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setWebhook', $params);
    }

    /**
     * Удалить webhook
     */
    public function deleteWebhook(array $params = []): array
    {
        return $this->makeRequest('deleteWebhook', $params);
    }

    /**
     * Получить информацию о webhook
     */
    public function getWebhookInfo(): array
    {
        return $this->makeRequest('getWebhookInfo');
    }

    // ========== MINI APPS API 9.1 ==========



    /**
     * Получить информацию о Mini App бота
     */
    public function getBotApp(array $params): array
    {
        $requiredParams = ['app'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('getBotApp', $params);
    }

    /**
     * Установить короткое имя для Mini App
     */
    public function setMiniAppShortName(array $params): array
    {
        $requiredParams = ['short_name'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('setMiniAppShortName', $params);
    }

    /**
     * Получить популярные Mini App боты
     */
    public function getPopularAppBots(array $params = []): array
    {
        return $this->makeRequest('getPopularAppBots', $params);
    }

    /**
     * Добавить медиа для предпросмотра Mini App
     */
    public function addPreviewMedia(array $params): array
    {
        $requiredParams = ['bot', 'lang_code', 'media'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('addPreviewMedia', $params);
    }

    /**
     * Редактировать медиа предпросмотра Mini App
     */
    public function editPreviewMedia(array $params): array
    {
        $requiredParams = ['bot', 'lang_code', 'media', 'new_media'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('editPreviewMedia', $params);
    }

    /**
     * Удалить медиа предпросмотра Mini App
     */
    public function deletePreviewMedia(array $params): array
    {
        $requiredParams = ['bot', 'lang_code', 'media'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('deletePreviewMedia', $params);
    }

    /**
     * Изменить порядок медиа предпросмотра Mini App
     */
    public function reorderPreviewMedias(array $params): array
    {
        $requiredParams = ['bot', 'lang_code', 'order'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('reorderPreviewMedias', $params);
    }

    /**
     * Получить информацию о предпросмотре Mini App
     */
    public function getPreviewInfo(array $params): array
    {
        $requiredParams = ['bot'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('getPreviewInfo', $params);
    }

    /**
     * Получить медиа предпросмотра Mini App
     */
    public function getPreviewMedias(array $params): array
    {
        $requiredParams = ['bot'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('getPreviewMedias', $params);
    }

    /**
     * Вызвать пользовательский метод Web App
     */
    public function invokeWebViewCustomMethod(array $params): array
    {
        $requiredParams = ['bot', 'custom_method', 'parameters'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('invokeWebViewCustomMethod', $params);
    }

    /**
     * Проверить параметры скачивания файла
     */
    public function checkDownloadFileParams(array $params): array
    {
        $requiredParams = ['url', 'filename'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('checkDownloadFileParams', $params);
    }

    // ========== BUSINESS BOT API 9.1 ==========

    /**
     * Подключить бизнес-бота
     */
    public function connectBusinessBot(array $params): array
    {
        $requiredParams = ['bot', 'connection_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('connectBusinessBot', $params);
    }

    /**
     * Обновить настройки подключенного бота
     */
    public function updateConnectedBot(array $params): array
    {
        $requiredParams = ['bot', 'recipients'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('updateConnectedBot', $params);
    }

    /**
     * Получить подключенных ботов
     */
    public function getConnectedBots(): array
    {
        return $this->makeRequest('getConnectedBots');
    }

    /**
     * Переключить паузу подключенного бота для конкретного чата
     */
    public function toggleConnectedBotPaused(array $params): array
    {
        $requiredParams = ['peer', 'paused'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('toggleConnectedBotPaused', $params);
    }

    /**
     * Отключить бота для конкретного чата
     */
    public function disablePeerConnectedBot(array $params): array
    {
        $requiredParams = ['peer'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('disablePeerConnectedBot', $params);
    }

    /**
     * Получить бизнес-соединение бота
     */
    public function getBotBusinessConnection(array $params): array
    {
        $requiredParams = ['connection_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('getBotBusinessConnection', $params);
    }

    // ========== AFFILIATE PROGRAM API 9.1 ==========

    /**
     * Получить ботов под управлением
     */
    public function getAdminedBots(): array
    {
        return $this->makeRequest('getAdminedBots');
    }

    /**
     * Обновить программу реферальной системы звезд
     */
    public function updateStarRefProgram(array $params): array
    {
        $requiredParams = ['bot', 'commission_permille'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('updateStarRefProgram', $params);
    }

    /**
     * Получить подключенных реферальных ботов
     */
    public function getConnectedStarRefBots(array $params): array
    {
        $requiredParams = ['peer'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('getConnectedStarRefBots', $params);
    }

    /**
     * Получить информацию о подключенном реферальном боте
     */
    public function getConnectedStarRefBot(array $params): array
    {
        $requiredParams = ['peer', 'bot'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('getConnectedStarRefBot', $params);
    }

    /**
     * Получить предлагаемых реферальных ботов
     */
    public function getSuggestedStarRefBots(array $params = []): array
    {
        return $this->makeRequest('getSuggestedStarRefBots', $params);
    }

    /**
     * Подключить реферального бота
     */
    public function connectStarRefBot(array $params): array
    {
        $requiredParams = ['bot', 'peer'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('connectStarRefBot', $params);
    }

    /**
     * Редактировать подключение реферального бота
     */
    public function editConnectedStarRefBot(array $params): array
    {
        $requiredParams = ['peer', 'bot', 'url'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('editConnectedStarRefBot', $params);
    }

    // ========== РАСШИРЕННЫЕ ВОЗМОЖНОСТИ API 9.1 ==========

    /**
     * Поиск стикеров с AI
     */
    public function searchStickers(array $params): array
    {
        $requiredParams = ['query'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('searchStickers', $params);
    }

    /**
     * Установить эмодзи статус
     */
    public function setMyEmojiStatus(array $params): array
    {
        return $this->makeRequest('setMyEmojiStatus', $params);
    }

    /**
     * Получить эмодзи статус
     */
    public function getMyEmojiStatus(): array
    {
        return $this->makeRequest('getMyEmojiStatus');
    }

    /**
     * Создать подготовленное сообщение
     */
    public function prepareSendMessage(array $params): array
    {
        $requiredParams = ['chat_id', 'text'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('prepareSendMessage', $params);
    }

    /**
     * Отправить подготовленное сообщение
     */
    public function sendPreparedMessage(array $params): array
    {
        $requiredParams = ['prepared_message_id'];
        $this->validateRequiredParams($params, $requiredParams);
        
        return $this->makeRequest('sendPreparedMessage', $params);
    }

    /**
     * Получить доступные эффекты сообщений
     */
    public function getAvailableMessageEffects(): array
    {
        return $this->makeRequest('getAvailableMessageEffects');
    }

    /**
     * Установить фоновый аккентный цвет
     */
    public function setMyBackgroundAccentColor(array $params): array
    {
        return $this->makeRequest('setMyBackgroundAccentColor', $params);
    }

    /**
     * Получить фоновый аккентный цвет
     */
    public function getMyBackgroundAccentColor(): array
    {
        return $this->makeRequest('getMyBackgroundAccentColor');
    }

    // ========== ВСПОМОГАТЕЛЬНЫЕ МЕТОДЫ ==========

    /**
     * Валидация обязательных параметров
     */
    private function validateRequiredParams(array $params, array $requiredParams): void
    {
        foreach ($requiredParams as $param) {
            if (!isset($params[$param])) {
                throw new Exception("Обязательный параметр '{$param}' отсутствует");
            }
        }
    }

    /**
     * Выполнение HTTP запроса к API
     */
    private function makeRequest(string $method, array $params = []): array
    {
        try {
            $url = $this->apiUrl . $method;
            
            $options = [
                'json' => $params,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ];

            $response = $this->httpClient->post($url, $options);
            $responseData = $this->parseResponse($response);

            if (!$responseData['ok']) {
                $errorMessage = $responseData['description'] ?? 'Неизвестная ошибка';
                throw new Exception("Ошибка API Telegram: {$errorMessage}");
            }

            return $responseData;

        } catch (GuzzleException $e) {
            Log::error('Ошибка HTTP запроса к Telegram API', [
                'method' => $method,
                'params' => $params,
                'error' => $e->getMessage(),
            ]);
            throw new Exception("Ошибка соединения с Telegram API: {$e->getMessage()}");
        }
    }

    /**
     * Парсинг ответа от API
     */
    private function parseResponse(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();
        $decodedResponse = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Ошибка декодирования JSON ответа от API');
        }

        return $decodedResponse;
    }

    /**
     * Получить URL для скачивания файла
     */
    public function getFileUrl(string $filePath): string
    {
        return self::FILE_API_BASE_URL . $this->botToken . '/' . $filePath;
    }

    /**
     * Построить ссылку на Mini App
     */
    public function buildMiniAppLink(string $botUsername, ?string $startParam = null, ?string $mode = null): string
    {
        $url = "https://t.me/{$botUsername}?startapp";
        
        if ($startParam) {
            $url .= "={$startParam}";
        }
        
        if ($mode && in_array($mode, ['compact', 'fullscreen'], true)) {
            $url .= "&mode={$mode}";
        }
        
        return $url;
    }

    /**
     * Построить прямую ссылку на Mini App
     */
    public function buildDirectMiniAppLink(string $botUsername, string $shortName, ?string $startParam = null, ?string $mode = null): string
    {
        $url = "https://t.me/{$botUsername}/{$shortName}";
        
        $queryParams = [];
        
        if ($startParam) {
            $queryParams['startapp'] = $startParam;
        }
        
        if ($mode && in_array($mode, ['compact', 'fullscreen'], true)) {
            $queryParams['mode'] = $mode;
        }
        
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }
        
        return $url;
    }

    /**
     * Построить ссылку для открытия Mini App в конкретном чате
     */
    public function buildChatMiniAppLink(string $chatUsername, string $botUsername, ?string $startParam = null): string
    {
        $url = "https://t.me/{$chatUsername}?attach={$botUsername}";
        
        if ($startParam) {
            $url .= "&startattach={$startParam}";
        }
        
        return $url;
    }

    /**
     * Построить ссылку для выбора чата для Mini App
     */
    public function buildChooseChatMiniAppLink(string $botUsername, ?string $startParam = null, array $chatTypes = []): string
    {
        $url = "https://t.me/{$botUsername}?startattach";
        
        if ($startParam) {
            $url .= "={$startParam}";
        }
        
        if (!empty($chatTypes)) {
            $validTypes = array_intersect($chatTypes, ['users', 'bots', 'groups', 'channels']);
            if (!empty($validTypes)) {
                $url .= "&choose=" . implode('+', $validTypes);
            }
        }
        
        return $url;
    }

    /**
     * Валидация webhook URL
     */
    public function validateWebhookUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $parsedUrl = parse_url($url);
        
        // Проверяем, что используется HTTPS
        if ($parsedUrl['scheme'] !== 'https') {
            return false;
        }

        // Проверяем, что порт допустимый (443, 80, 88, 8443)
        $allowedPorts = [443, 80, 88, 8443];
        $port = $parsedUrl['port'] ?? 443;
        
        return in_array($port, $allowedPorts, true);
    }

    /**
     * Создать inline клавиатуру
     */
    public function createInlineKeyboard(array $buttons): array
    {
        return [
            'inline_keyboard' => $buttons
        ];
    }

    /**
     * Создать обычную клавиатуру
     */
    public function createReplyKeyboard(array $buttons, bool $resize = true, bool $oneTime = false): array
    {
        return [
            'keyboard' => $buttons,
            'resize_keyboard' => $resize,
            'one_time_keyboard' => $oneTime,
        ];
    }

    /**
     * Удалить клавиатуру
     */
    public function removeKeyboard(bool $selective = false): array
    {
        return [
            'remove_keyboard' => true,
            'selective' => $selective,
        ];
    }

    /**
     * Принудительный ответ
     */
    public function forceReply(bool $selective = false): array
    {
        return [
            'force_reply' => true,
            'selective' => $selective,
        ];
    }

    /**
     * Логирование активности бота
     */
    private function logActivity(string $method, array $params, array $response): void
    {
        Log::info('Telegram Bot API вызов', [
            'method' => $method,
            'params_count' => count($params),
            'success' => $response['ok'] ?? false,
            'response_size' => strlen(json_encode($response)),
        ]);
    }
}
