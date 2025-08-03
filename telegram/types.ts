/**
 * Telegram Bot API 9.1 TypeScript Types
 * 
 * Полный набор типов для работы с Telegram Bot API
 * Основано на официальной документации: https://core.telegram.org/bots/api
 * 
 * @version 9.1
 * @date July 2025
 */

// =====================================
// ОСНОВНЫЕ ТИПЫ
// =====================================

/**
 * Пользователь Telegram
 */
export interface User {
  /** Уникальный идентификатор пользователя или бота */
  id: number;
  /** True, если это бот */
  is_bot: boolean;
  /** Имя пользователя */
  first_name: string;
  /** Фамилия пользователя */
  last_name?: string;
  /** Имя пользователя (@username) */
  username?: string;
  /** IETF код языка пользователя */
  language_code?: string;
  /** True, если пользователь является Telegram Premium */
  is_premium?: boolean;
  /** True, если пользователь добавил бота в меню вложений */
  added_to_attachment_menu?: boolean;
  /** True, если бот может читать все групповые сообщения */
  can_read_all_group_messages?: boolean;
  /** True, если бот поддерживает инлайн-запросы */
  supports_inline_queries?: boolean;
  /** True, если бот может подключаться к бизнес-аккаунтам */
  can_connect_to_business?: boolean;
  /** True, если у бота есть главное мини-приложение */
  has_main_web_app?: boolean;
}

/**
 * Чат в Telegram
 */
export interface Chat {
  /** Уникальный идентификатор чата */
  id: number;
  /** Тип чата: "private", "group", "supergroup", "channel" */
  type: 'private' | 'group' | 'supergroup' | 'channel';
  /** Название чата для групп, супергрупп и каналов */
  title?: string;
  /** Имя пользователя для приватных чатов и каналов */
  username?: string;
  /** Имя пользователя для приватных чатов */
  first_name?: string;
  /** Фамилия пользователя для приватных чатов */
  last_name?: string;
  /** True, если чат является форумом */
  is_forum?: boolean;
}

/**
 * Полная информация о чате
 */
export interface ChatFullInfo extends Chat {
  /** Фото чата */
  photo?: ChatPhoto;
  /** Список активных имен пользователей для приватных чатов */
  active_usernames?: string[];
  /** Дата рождения пользователя для приватных чатов */
  birthdate?: Birthdate;
  /** Бизнес-вступление для приватных чатов */
  business_intro?: BusinessIntro;
  /** Местоположение бизнеса для приватных чатов */
  business_location?: BusinessLocation;
  /** Часы работы бизнеса для приватных чатов */
  business_opening_hours?: BusinessOpeningHours;
  /** Личный чат для приватных чатов */
  personal_chat?: Chat;
  /** Принятые типы подарков */
  accepted_gift_types?: AcceptedGiftTypes;
  /** Доступные реакции в чате */
  available_reactions?: ReactionType[];
  /** Акцентный цвет фона */
  background_custom_emoji_id?: string;
  /** Цвет профиля */
  profile_accent_color_id?: number;
  /** Пользовательский эмодзи фона профиля */
  profile_background_custom_emoji_id?: string;
  /** Цвет эмодзи */
  emoji_status_custom_emoji_id?: string;
  /** Дата истечения статуса эмодзи */
  emoji_status_expiration_date?: number;
  /** Биография или описание */
  bio?: string;
  /** True, если возможность отправки подарков включена */
  can_send_gift?: boolean;
  /** True, если приватность чтения сообщений отключена */
  has_private_forwards?: boolean;
  /** True, если приватность пересылки голосовых и видео сообщений включена */
  has_restricted_voice_and_video_messages?: boolean;
  /** True, если пользователи должны присоединиться к супергруппе для отправки сообщений */
  join_to_send_messages?: boolean;
  /** True, если все пользователи должны быть одобрены администратором */
  join_by_request?: boolean;
  /** Описание групп и супергрупп */
  description?: string;
  /** Ссылка-приглашение в чат */
  invite_link?: string;
  /** Закреплённое сообщение */
  pinned_message?: Message;
  /** Разрешения участников по умолчанию */
  permissions?: ChatPermissions;
  /** Флаг для замедления режима */
  slow_mode_delay?: number;
  /** Время до автоудаления сообщений */
  message_auto_delete_time?: number;
  /** True, если агрессивная антиспам защита включена */
  has_aggressive_anti_spam_enabled?: boolean;
  /** True, если скрытые участники включены */
  has_hidden_members?: boolean;
  /** True, если защищённый контент включен */
  has_protected_content?: boolean;
  /** True, если имя видимо */
  has_visible_history?: boolean;
  /** Имя группы наклеек */
  sticker_set_name?: string;
  /** True, если бот может устанавливать наклейки */
  can_set_sticker_set?: boolean;
  /** Имя пользовательского набора эмодзи */
  custom_emoji_sticker_set_name?: string;
  /** ID связанной группы чата */
  linked_chat_id?: number;
  /** Местоположение чата */
  location?: ChatLocation;
}

/**
 * Сообщение в Telegram
 */
export interface Message {
  /** Уникальный идентификатор сообщения */
  message_id: number;
  /** ID темы сообщения для форумов */
  message_thread_id?: number;
  /** Отправитель сообщения */
  from?: User;
  /** Дата отправки сообщения */
  date: number;
  /** Чат, в котором было отправлено сообщение */
  chat: Chat;
  /** Информация о пересылке */
  forward_origin?: MessageOrigin;
  /** True, если сообщение является топиком */
  is_topic_message?: boolean;
  /** True, если сообщение является автоматической пересылкой */
  is_automatic_forward?: boolean;
  /** Сообщение, на которое отвечает данное */
  reply_to_message?: Message;
  /** Информация о внешнем ответе */
  external_reply?: ExternalReplyInfo;
  /** Информация о цитате */
  quote?: TextQuote;
  /** Информация о истории ответов */
  reply_to_story?: Story;
  /** Бот, через которого было отправлено сообщение */
  via_bot?: User;
  /** Дата последнего редактирования */
  edit_date?: number;
  /** True, если сообщение имеет защищённый контент */
  has_protected_content?: boolean;
  /** True, если это сообщение из медиагруппы */
  is_from_offline?: boolean;
  /** Уникальный идентификатор медиагруппы */
  media_group_id?: string;
  /** Подпись автора сообщения */
  author_signature?: string;
  /** Текст сообщения */
  text?: string;
  /** Сущности в тексте */
  entities?: MessageEntity[];
  /** Информация о ссылке */
  link_preview_options?: LinkPreviewOptions;
  /** ID эффекта сообщения */
  effect_id?: string;
  /** Аудио файл */
  audio?: Audio;
  /** Документ */
  document?: Document;
  /** Анимация */
  animation?: Animation;
  /** Игра */
  game?: Game;
  /** Фото */
  photo?: PhotoSize[];
  /** Стикер */
  sticker?: Sticker;
  /** Видео */
  video?: Video;
  /** Видео заметка */
  video_note?: VideoNote;
  /** Голосовое сообщение */
  voice?: Voice;
  /** Подпись к медиа */
  caption?: string;
  /** Сущности в подписи */
  caption_entities?: MessageEntity[];
  /** True, если медиа покрыто спойлером */
  show_caption_above_media?: boolean;
  /** True, если медиа имеет спойлер */
  has_media_spoiler?: boolean;
  /** Контакт */
  contact?: Contact;
  /** Игральная кость */
  dice?: Dice;
  /** Игра */
  poll?: Poll;
  /** Местоположение */
  location?: Location;
  /** Место */
  venue?: Venue;
  /** Чек-лист (новое в API 9.1) */
  checklist?: Checklist;
  /** Выполненные задачи чек-листа (новое в API 9.1) */
  checklist_tasks_done?: ChecklistTasksDone;
  /** Добавленные задачи чек-листа (новое в API 9.1) */
  checklist_tasks_added?: ChecklistTasksAdded;
  /** Изменение цены прямых сообщений (новое в API 9.1) */
  direct_message_price_changed?: DirectMessagePriceChanged;
  /** Подарок (новое в API 9.0) */
  gift?: GiftInfo;
  /** Уникальный подарок (новое в API 9.0) */
  unique_gift?: UniqueGiftInfo;
  /** Изменение цены платных сообщений (новое в API 9.0) */
  paid_message_price_changed?: PaidMessagePriceChanged;
  /** Количество звёзд, заплаченных за сообщение (новое в API 9.0) */
  paid_star_count?: number;
  /** Новые участники чата */
  new_chat_members?: User[];
  /** Покинувший участник */
  left_chat_member?: User;
  /** Новое название чата */
  new_chat_title?: string;
  /** Новое фото чата */
  new_chat_photo?: PhotoSize[];
  /** True, если фото чата было удалено */
  delete_chat_photo?: boolean;
  /** True, если группа была создана */
  group_chat_created?: boolean;
  /** True, если супергруппа была создана */
  supergroup_chat_created?: boolean;
  /** True, если канал был создан */
  channel_chat_created?: boolean;
  /** Автоудаление сообщений включено */
  message_auto_delete_timer_changed?: MessageAutoDeleteTimerChanged;
  /** Миграция в супергруппу */
  migrate_to_chat_id?: number;
  /** Миграция из группы */
  migrate_from_chat_id?: number;
  /** Закреплённое сообщение */
  pinned_message?: Message;
  /** Счёт */
  invoice?: Invoice;
  /** Успешный платёж */
  successful_payment?: SuccessfulPayment;
  /** Возврат платежа (новое в API 7.7) */
  refunded_payment?: RefundedPayment;
  /** Пользователи, поделившиеся информацией */
  users_shared?: UsersShared;
  /** Поделившийся чат */
  chat_shared?: ChatShared;
  /** Подключённый сайт */
  connected_website?: string;
  /** Passport данные */
  passport_data?: PassportData;
  /** Близость алерт */
  proximity_alert_triggered?: ProximityAlertTriggered;
  /** Бустинг чата */
  boost_added?: ChatBoostAdded;
  /** Фон чата установлен */
  chat_background_set?: ChatBackgroundSet;
  /** Изменение форума топика */
  forum_topic_created?: ForumTopicCreated;
  forum_topic_edited?: ForumTopicEdited;
  forum_topic_closed?: ForumTopicClosed;
  forum_topic_reopened?: ForumTopicReopened;
  /** Скрытие общего форума топика */
  general_forum_topic_hidden?: GeneralForumTopicHidden;
  general_forum_topic_unhidden?: GeneralForumTopicUnhidden;
  /** Разрешение на запись голосового чата */
  video_chat_scheduled?: VideoChatScheduled;
  video_chat_started?: VideoChatStarted;
  video_chat_ended?: VideoChatEnded;
  video_chat_participants_invited?: VideoChatParticipantsInvited;
  /** Запрос веб-приложения */
  web_app_data?: WebAppData;
  /** Инлайн клавиатура */
  reply_markup?: InlineKeyboardMarkup;
}

/**
 * Обновление от Telegram
 */
export interface Update {
  /** ID обновления */
  update_id: number;
  /** Новое сообщение */
  message?: Message;
  /** Отредактированное сообщение */
  edited_message?: Message;
  /** Сообщение канала */
  channel_post?: Message;
  /** Отредактированное сообщение канала */
  edited_channel_post?: Message;
  /** Бизнес-соединение (новое в API 7.2) */
  business_connection?: BusinessConnection;
  /** Бизнес-сообщение (новое в API 7.2) */
  business_message?: Message;
  /** Отредактированное бизнес-сообщение (новое в API 7.2) */
  edited_business_message?: Message;
  /** Удалённые бизнес-сообщения (новое в API 7.2) */
  deleted_business_messages?: BusinessMessagesDeleted;
  /** Реакция на сообщение */
  message_reaction?: MessageReactionUpdated;
  /** Счётчик реакций */
  message_reaction_count?: MessageReactionCountUpdated;
  /** Инлайн запрос */
  inline_query?: InlineQuery;
  /** Выбранный инлайн результат */
  chosen_inline_result?: ChosenInlineResult;
  /** Callback запрос */
  callback_query?: CallbackQuery;
  /** Запрос доставки */
  shipping_query?: ShippingQuery;
  /** Предварительный запрос оплаты */
  pre_checkout_query?: PreCheckoutQuery;
  /** Запрос на создание счёта (новое в API 7.0) */
  purchased_paid_media?: PurchasedPaidMedia;
  /** Опрос */
  poll?: Poll;
  /** Ответ на опрос */
  poll_answer?: PollAnswer;
  /** Мой статус участника чата изменился */
  my_chat_member?: ChatMemberUpdated;
  /** Участник чата изменился */
  chat_member?: ChatMemberUpdated;
  /** Запрос на присоединение к чату */
  chat_join_request?: ChatJoinRequest;
  /** Буст чата */
  chat_boost?: ChatBoostUpdated;
  /** Удалённый буст чата */
  removed_chat_boost?: ChatBoostRemoved;
}

// =====================================
// ТИПЫ МЕДИА КОНТЕНТА
// =====================================

/**
 * Фото размер
 */
export interface PhotoSize {
  /** Идентификатор файла */
  file_id: string;
  /** Уникальный идентификатор файла */
  file_unique_id: string;
  /** Ширина фото */
  width: number;
  /** Высота фото */
  height: number;
  /** Размер файла в байтах */
  file_size?: number;
}

/**
 * Аудио файл
 */
export interface Audio {
  /** Идентификатор файла */
  file_id: string;
  /** Уникальный идентификатор файла */
  file_unique_id: string;
  /** Длительность аудио в секундах */
  duration: number;
  /** Исполнитель аудио */
  performer?: string;
  /** Название аудио */
  title?: string;
  /** Название файла */
  file_name?: string;
  /** MIME тип файла */
  mime_type?: string;
  /** Размер файла в байтах */
  file_size?: number;
  /** Миниатюра аудио файла */
  thumbnail?: PhotoSize;
}

/**
 * Документ
 */
export interface Document {
  /** Идентификатор файла */
  file_id: string;
  /** Уникальный идентификатор файла */
  file_unique_id: string;
  /** Миниатюра документа */
  thumbnail?: PhotoSize;
  /** Название файла */
  file_name?: string;
  /** MIME тип файла */
  mime_type?: string;
  /** Размер файла в байтах */
  file_size?: number;
}

/**
 * Видео файл
 */
export interface Video {
  /** Идентификатор файла */
  file_id: string;
  /** Уникальный идентификатор файла */
  file_unique_id: string;
  /** Ширина видео */
  width: number;
  /** Высота видео */
  height: number;
  /** Длительность видео в секундах */
  duration: number;
  /** Миниатюра видео */
  thumbnail?: PhotoSize;
  /** Название файла */
  file_name?: string;
  /** MIME тип файла */
  mime_type?: string;
  /** Размер файла в байтах */
  file_size?: number;
  /** Обложка видео (новое в API 8.3) */
  cover?: PhotoSize;
  /** Начальная временная метка видео (новое в API 8.3) */
  start_timestamp?: number;
}

/**
 * Анимация (GIF или H.264/MPEG-4 AVC видео без звука)
 */
export interface Animation {
  /** Идентификатор файла */
  file_id: string;
  /** Уникальный идентификатор файла */
  file_unique_id: string;
  /** Ширина анимации */
  width: number;
  /** Высота анимации */
  height: number;
  /** Длительность анимации в секундах */
  duration: number;
  /** Миниатюра анимации */
  thumbnail?: PhotoSize;
  /** Название файла */
  file_name?: string;
  /** MIME тип файла */
  mime_type?: string;
  /** Размер файла в байтах */
  file_size?: number;
}

/**
 * Голосовое сообщение
 */
export interface Voice {
  /** Идентификатор файла */
  file_id: string;
  /** Уникальный идентификатор файла */
  file_unique_id: string;
  /** Длительность голосового сообщения в секундах */
  duration: number;
  /** MIME тип файла */
  mime_type?: string;
  /** Размер файла в байтах */
  file_size?: number;
}

/**
 * Видео заметка
 */
export interface VideoNote {
  /** Идентификатор файла */
  file_id: string;
  /** Уникальный идентификатор файла */
  file_unique_id: string;
  /** Диаметр видео заметки */
  length: number;
  /** Длительность видео заметки в секундах */
  duration: number;
  /** Миниатюра видео заметки */
  thumbnail?: PhotoSize;
  /** Размер файла в байтах */
  file_size?: number;
}

/**
 * Контакт
 */
export interface Contact {
  /** Номер телефона */
  phone_number: string;
  /** Имя контакта */
  first_name: string;
  /** Фамилия контакта */
  last_name?: string;
  /** ID пользователя в Telegram */
  user_id?: number;
  /** Дополнительные данные о контакте в виде vCard */
  vcard?: string;
}

/**
 * Местоположение
 */
export interface Location {
  /** Широта */
  latitude: number;
  /** Долгота */
  longitude: number;
  /** Радиус неопределённости местоположения в метрах */
  horizontal_accuracy?: number;
  /** Время относительно даты сообщения, в течение которого местоположение может быть обновлено */
  live_period?: number;
  /** Направление движения пользователя в градусах */
  heading?: number;
  /** Максимальное расстояние для уведомлений о приближении к другому участнику чата */
  proximity_alert_radius?: number;
}

/**
 * Место
 */
export interface Venue {
  /** Местоположение места */
  location: Location;
  /** Название места */
  title: string;
  /** Адрес места */
  address: string;
  /** ID места на Foursquare */
  foursquare_id?: string;
  /** Тип места на Foursquare */
  foursquare_type?: string;
  /** ID места в Google Places */
  google_place_id?: string;
  /** Тип места в Google Places */
  google_place_type?: string;
}

/**
 * Информация о файле
 */
export interface File {
  /** Идентификатор файла */
  file_id: string;
  /** Уникальный идентификатор файла */
  file_unique_id: string;
  /** Размер файла в байтах */
  file_size?: number;
  /** Путь к файлу для скачивания */
  file_path?: string;
}

// =====================================
// НОВЫЕ ТИПЫ API 9.1 - ЧЕКЛИСТЫ
// =====================================

/**
 * Задача в чек-листе (новое в API 9.1)
 */
export interface ChecklistTask {
  /** Текст задачи */
  text: string;
  /** True, если задача выполнена */
  is_done: boolean;
}

/**
 * Чек-лист (новое в API 9.1)
 */
export interface Checklist {
  /** Название чек-листа */
  title?: string;
  /** Список задач */
  tasks: ChecklistTask[];
}

/**
 * Входящая задача для чек-листа (новое в API 9.1)
 */
export interface InputChecklistTask {
  /** Текст задачи */
  text: string;
  /** True, если задача выполнена */
  is_done?: boolean;
}

/**
 * Входящий чек-лист (новое в API 9.1)
 */
export interface InputChecklist {
  /** Название чек-листа */
  title?: string;
  /** Список задач */
  tasks: InputChecklistTask[];
}

/**
 * Завершённые задачи чек-листа (новое в API 9.1)
 */
export interface ChecklistTasksDone {
  /** Количество завершённых задач */
  done_count: number;
  /** Общее количество задач */
  total_count: number;
}

/**
 * Добавленные задачи чек-листа (новое в API 9.1)
 */
export interface ChecklistTasksAdded {
  /** Количество добавленных задач */
  added_count: number;
}

// =====================================
// НОВЫЕ ТИПЫ API 9.0/9.1 - ПОДАРКИ
// =====================================

/**
 * Информация о подарке (новое в API 9.0)
 */
export interface GiftInfo {
  /** ID подарка */
  gift_id: string;
  /** Звёзды для апгрейда (новое в API 8.2) */
  upgrade_star_count?: number;
}

/**
 * Модель уникального подарка (новое в API 9.0)
 */
export interface UniqueGiftModel {
  /** Модель уникального подарка */
  model: string;
  /** Количество уникальных подарков этой модели */
  count: number;
}

/**
 * Символ уникального подарка (новое в API 9.0)
 */
export interface UniqueGiftSymbol {
  /** Символ уникального подарка */
  symbol: string;
  /** Количество символов */
  count: number;
}

/**
 * Цвета фона уникального подарка (новое в API 9.0)
 */
export interface UniqueGiftBackdropColors {
  /** Верхний цвет */
  top_color: number;
  /** Нижний цвет */
  bottom_color: number;
}

/**
 * Фон уникального подарка (новое в API 9.0)
 */
export interface UniqueGiftBackdrop {
  /** Тип фона */
  type: string;
  /** Цвета фона */
  colors?: UniqueGiftBackdropColors;
}

/**
 * Уникальный подарок (новое в API 9.0)
 */
export interface UniqueGift {
  /** ID уникального подарка */
  id: string;
  /** Модель подарка */
  model: UniqueGiftModel;
  /** Символ подарка */
  symbol: UniqueGiftSymbol;
  /** Фон подарка */
  backdrop: UniqueGiftBackdrop;
}

/**
 * Информация об уникальном подарке (новое в API 9.0)
 */
export interface UniqueGiftInfo {
  /** Уникальный подарок */
  gift: UniqueGift;
  /** Отправитель подарка */
  sender_user?: User;
  /** Получатель подарка */
  receiver_user?: User;
  /** Дата отправки */
  date: number;
  /** Происхождение подарка */
  origin: 'purchase' | 'gift' | 'resale';
  /** Дата следующего трансфера (новое в API 9.1) */
  next_transfer_date?: number;
  /** Количество звёзд последней перепродажи (новое в API 9.1) */
  last_resale_star_count?: number;
}

/**
 * Принятые типы подарков (новое в API 9.0)
 */
export interface AcceptedGiftTypes {
  /** Принимаются ли все подарки */
  all_gifts?: boolean;
  /** Принимаются ли обычные подарки */
  regular_gifts?: boolean;
  /** Принимаются ли уникальные подарки */
  unique_gifts?: boolean;
}

// =====================================
// ИЗМЕНЕНИЯ ЦЕН (новое в API 9.0/9.1)
// =====================================

/**
 * Изменение цены платных сообщений (новое в API 9.0)
 */
export interface PaidMessagePriceChanged {
  /** Новая цена за сообщение в звёздах */
  star_count: number;
}

/**
 * Изменение цены прямых сообщений (новое в API 9.1)
 */
export interface DirectMessagePriceChanged {
  /** Новая цена за прямое сообщение в звёздах */
  star_count: number;
}

// =====================================
// ТИПЫ КЛАВИАТУР
// =====================================

/**
 * Инлайн клавиатура
 */
export interface InlineKeyboardMarkup {
  /** Массив рядов кнопок */
  inline_keyboard: InlineKeyboardButton[][];
}

/**
 * Инлайн кнопка клавиатуры
 */
export interface InlineKeyboardButton {
  /** Текст кнопки */
  text: string;
  /** HTTP или tg:// URL для открытия */
  url?: string;
  /** Данные для отправки в callback_query */
  callback_data?: string;
  /** Описание Web App */
  web_app?: WebApp;
  /** Описание инлайн запроса */
  switch_inline_query?: string;
  /** Описание инлайн запроса в текущем чате */
  switch_inline_query_current_chat?: string;
  /** Описание инлайн запроса для выбранного чата */
  switch_inline_query_chosen_chat?: SwitchInlineQueryChosenChat;
  /** Описание игры */
  callback_game?: CallbackGame;
  /** True, если нужно платить */
  pay?: boolean;
}

/**
 * Обычная клавиатура
 */
export interface ReplyKeyboardMarkup {
  /** Массив рядов кнопок */
  keyboard: KeyboardButton[][];
  /** True, если клавиатура адаптивная */
  is_persistent?: boolean;
  /** True, если клавиатура изменяет размер */
  resize_keyboard?: boolean;
  /** True, если клавиатура одноразовая */
  one_time_keyboard?: boolean;
  /** Placeholder для поля ввода */
  input_field_placeholder?: string;
  /** True, если клавиатура селективная */
  selective?: boolean;
}

/**
 * Кнопка обычной клавиатуры
 */
export interface KeyboardButton {
  /** Текст кнопки */
  text: string;
  /** True, если нужно запросить контакт */
  request_contact?: boolean;
  /** True, если нужно запросить местоположение */
  request_location?: boolean;
  /** Опрос для запроса */
  request_poll?: KeyboardButtonPollType;
  /** Запрос пользователей */
  request_users?: KeyboardButtonRequestUsers;
  /** Запрос чата */
  request_chat?: KeyboardButtonRequestChat;
  /** Web App для запуска */
  web_app?: WebApp;
}

/**
 * Удаление клавиатуры
 */
export interface ReplyKeyboardRemove {
  /** True для удаления клавиатуры */
  remove_keyboard: true;
  /** True, если удаление селективное */
  selective?: boolean;
}

/**
 * Принудительный ответ
 */
export interface ForceReply {
  /** True для принудительного ответа */
  force_reply: true;
  /** Placeholder для поля ввода */
  input_field_placeholder?: string;
  /** True, если принуждение селективное */
  selective?: boolean;
}

// =====================================
// ТИПЫ ДЛЯ ИНЛАЙН РЕЖИМА
// =====================================

/**
 * Инлайн запрос
 */
export interface InlineQuery {
  /** Уникальный идентификатор запроса */
  id: string;
  /** Пользователь, отправивший запрос */
  from: User;
  /** Текст запроса */
  query: string;
  /** Смещение результатов */
  offset: string;
  /** Тип чата, из которого был отправлен запрос */
  chat_type?: 'sender' | 'private' | 'group' | 'supergroup' | 'channel';
  /** Местоположение пользователя */
  location?: Location;
}

/**
 * Выбранный инлайн результат
 */
export interface ChosenInlineResult {
  /** Идентификатор результата */
  result_id: string;
  /** Пользователь, выбравший результат */
  from: User;
  /** Местоположение пользователя */
  location?: Location;
  /** Идентификатор инлайн сообщения */
  inline_message_id?: string;
  /** Запрос, который использовался для получения результата */
  query: string;
}

// =====================================
// ТИПЫ ДЛЯ CALLBACK ЗАПРОСОВ
// =====================================

/**
 * Callback запрос
 */
export interface CallbackQuery {
  /** Уникальный идентификатор запроса */
  id: string;
  /** Пользователь, отправивший запрос */
  from: User;
  /** Сообщение с инлайн клавиатурой */
  message?: Message;
  /** Идентификатор инлайн сообщения */
  inline_message_id?: string;
  /** Глобальный идентификатор чата */
  chat_instance: string;
  /** Данные, связанные с callback кнопкой */
  data?: string;
  /** Короткое имя игры */
  game_short_name?: string;
}

// =====================================
// ТИПЫ ДЛЯ WEB APPS
// =====================================

/**
 * Web App
 */
export interface WebApp {
  /** HTTPS URL веб-приложения */
  url: string;
}

/**
 * Данные Web App
 */
export interface WebAppData {
  /** Данные */
  data: string;
  /** Текст кнопки */
  button_text: string;
}

// =====================================
// ТИПЫ ДЛЯ УЧАСТНИКОВ ЧАТА
// =====================================

/**
 * Базовый тип участника чата
 */
export interface ChatMember {
  /** Информация о пользователе */
  user: User;
  /** Статус участника в чате */
  status: 'creator' | 'administrator' | 'member' | 'restricted' | 'left' | 'kicked';
}

/**
 * Создатель чата
 */
export interface ChatMemberOwner extends ChatMember {
  status: 'creator';
  /** True, если пользователь присутствует в чате */
  is_anonymous: boolean;
}

/**
 * Администратор чата
 */
export interface ChatMemberAdministrator extends ChatMember {
  status: 'administrator';
  /** True, если администратор может быть отредактирован создателем */
  can_be_edited: boolean;
  /** True, если администратор может получить доступ к логам чата */
  can_manage_chat: boolean;
  /** True, если администратор может удалять сообщения других пользователей */
  can_delete_messages: boolean;
  /** True, если администратор может управлять видеочатами */
  can_manage_video_chats: boolean;
  /** True, если администратор может ограничивать, банить или разбанивать участников чата */
  can_restrict_members: boolean;
  /** True, если администратор может добавлять новых администраторов */
  can_promote_members: boolean;
  /** True, если администратор может изменять название чата, фото и другие настройки */
  can_change_info: boolean;
  /** True, если администратор может приглашать новых пользователей в чат */
  can_invite_users: boolean;
  /** True, если администратор может постить в канале */
  can_post_messages?: boolean;
  /** True, если администратор может редактировать сообщения других пользователей */
  can_edit_messages?: boolean;
  /** True, если администратор может закреплять сообщения */
  can_pin_messages?: boolean;
  /** True, если администратор может управлять темами */
  can_manage_topics?: boolean;
  /** Пользовательский титул администратора */
  custom_title?: string;
  /** True, если администратор присутствует в чате */
  is_anonymous?: boolean;
}

/**
 * Обычный участник чата
 */
export interface ChatMemberMember extends ChatMember {
  status: 'member';
}

/**
 * Ограниченный участник чата
 */
export interface ChatMemberRestricted extends ChatMember {
  status: 'restricted';
  /** True, если пользователь является участником чата на момент запроса */
  is_member: boolean;
  /** True, если пользователь может отправлять текстовые сообщения, контакты, местоположения и места */
  can_send_messages: boolean;
  /** True, если пользователь может отправлять аудио, документы, фото, видео, видео заметки и голосовые сообщения */
  can_send_media_messages: boolean;
  /** True, если пользователь может отправлять опросы */
  can_send_polls: boolean;
  /** True, если пользователь может отправлять анимации, игры, стикеры и использовать инлайн ботов */
  can_send_other_messages: boolean;
  /** True, если пользователь может добавлять превью веб страниц в сообщения */
  can_add_web_page_previews: boolean;
  /** True, если пользователь может изменять название чата, фото и другие настройки */
  can_change_info: boolean;
  /** True, если пользователь может приглашать новых пользователей в чат */
  can_invite_users: boolean;
  /** True, если пользователь может закреплять сообщения */
  can_pin_messages: boolean;
  /** True, если пользователь может управлять темами */
  can_manage_topics: boolean;
  /** Дата, когда ограничения будут сняты */
  until_date: number;
}

/**
 * Покинувший участник чата
 */
export interface ChatMemberLeft extends ChatMember {
  status: 'left';
}

/**
 * Забаненный участник чата
 */
export interface ChatMemberBanned extends ChatMember {
  status: 'kicked';
  /** Дата, когда пользователь будет разбанен */
  until_date: number;
}

/**
 * Обновление участника чата
 */
export interface ChatMemberUpdated {
  /** Чат, в котором изменился статус пользователя */
  chat: Chat;
  /** Исполнитель действия */
  from: User;
  /** Дата изменения */
  date: number;
  /** Предыдущая информация о статусе участника чата */
  old_chat_member: ChatMember;
  /** Новая информация о статусе участника чата */
  new_chat_member: ChatMember;
  /** Ссылка-приглашение */
  invite_link?: ChatInviteLink;
  /** True, если пользователь присоединился к чату после отправки прямого запроса на присоединение */
  via_join_request?: boolean;
  /** True, если пользователь присоединился к чату, используя ссылку-приглашение для запроса на присоединение */
  via_chat_folder_invite_link?: boolean;
}

// =====================================
// ТИПЫ ДЛЯ ПЛАТЕЖЕЙ
// =====================================

/**
 * Счёт
 */
export interface Invoice {
  /** Название продукта */
  title: string;
  /** Описание продукта */
  description: string;
  /** Уникальный параметр глубокой ссылки бота */
  start_parameter: string;
  /** Трёхбуквенный ISO 4217 код валюты */
  currency: string;
  /** Общая цена в наименьших единицах валюты */
  total_amount: number;
}

/**
 * Успешный платёж
 */
export interface SuccessfulPayment {
  /** Трёхбуквенный ISO 4217 код валюты */
  currency: string;
  /** Общая цена в наименьших единицах валюты */
  total_amount: number;
  /** Полезная нагрузка счёта, определённая ботом */
  invoice_payload: string;
  /** Идентификатор выбранного варианта доставки */
  shipping_option_id?: string;
  /** Информация о заказе, предоставленная пользователем */
  order_info?: OrderInfo;
  /** Идентификатор платежа в Telegram */
  telegram_payment_charge_id: string;
  /** Идентификатор платежа у провайдера */
  provider_payment_charge_id: string;
}

/**
 * Возврат платежа (новое в API 7.7)
 */
export interface RefundedPayment {
  /** Трёхбуквенный ISO 4217 код валюты */
  currency: string;
  /** Общая сумма возврата в наименьших единицах валюты */
  total_amount: number;
  /** Полезная нагрузка счёта, определённая ботом */
  invoice_payload: string;
  /** Идентификатор платежа в Telegram */
  telegram_payment_charge_id: string;
  /** Идентификатор платежа у провайдера */
  provider_payment_charge_id?: string;
}

/**
 * Запрос доставки
 */
export interface ShippingQuery {
  /** Уникальный идентификатор запроса */
  id: string;
  /** Пользователь, отправивший запрос */
  from: User;
  /** Полезная нагрузка счёта, определённая ботом */
  invoice_payload: string;
  /** Адрес доставки, указанный пользователем */
  shipping_address: ShippingAddress;
}

/**
 * Предварительный запрос оплаты
 */
export interface PreCheckoutQuery {
  /** Уникальный идентификатор запроса */
  id: string;
  /** Пользователь, отправивший запрос */
  from: User;
  /** Трёхбуквенный ISO 4217 код валюты */
  currency: string;
  /** Общая цена в наименьших единицах валюты */
  total_amount: number;
  /** Полезная нагрузка счёта, определённая ботом */
  invoice_payload: string;
  /** Идентификатор выбранного варианта доставки */
  shipping_option_id?: string;
  /** Информация о заказе, предоставленная пользователем */
  order_info?: OrderInfo;
}

/**
 * Адрес доставки
 */
export interface ShippingAddress {
  /** Двухбуквенный ISO 3166-1 alpha-2 код страны */
  country_code: string;
  /** Штат, если применимо */
  state: string;
  /** Город */
  city: string;
  /** Первая строка адреса */
  street_line1: string;
  /** Вторая строка адреса */
  street_line2: string;
  /** Почтовый индекс */
  post_code: string;
}

/**
 * Информация о заказе
 */
export interface OrderInfo {
  /** Имя пользователя */
  name?: string;
  /** Номер телефона пользователя */
  phone_number?: string;
  /** Email пользователя */
  email?: string;
  /** Адрес доставки пользователя */
  shipping_address?: ShippingAddress;
}

// =====================================
// ДОПОЛНИТЕЛЬНЫЕ ТИПЫ
// =====================================

/**
 * Стикер
 */
export interface Sticker {
  /** Идентификатор файла */
  file_id: string;
  /** Уникальный идентификатор файла */
  file_unique_id: string;
  /** Тип стикера */
  type: 'regular' | 'mask' | 'custom_emoji';
  /** Ширина стикера */
  width: number;
  /** Высота стикера */
  height: number;
  /** True, если стикер анимированный */
  is_animated: boolean;
  /** True, если стикер видео */
  is_video: boolean;
  /** Миниатюра стикера */
  thumbnail?: PhotoSize;
  /** Эмодзи, связанный со стикером */
  emoji?: string;
  /** Название набора стикеров */
  set_name?: string;
  /** Файл премиум анимации для премиум стикеров */
  premium_animation?: File;
  /** Информация о маске */
  mask_position?: MaskPosition;
  /** Идентификатор пользовательского эмодзи */
  custom_emoji_id?: string;
  /** True, если нужна перерисовка цвета */
  needs_repainting?: boolean;
  /** Размер файла в байтах */
  file_size?: number;
}

/**
 * Игра
 */
export interface Game {
  /** Название игры */
  title: string;
  /** Описание игры */
  description: string;
  /** Фото, которое будет отображаться в игровом сообщении в чатах */
  photo: PhotoSize[];
  /** Краткое описание игры или лучшие результаты */
  text?: string;
  /** Специальные сущности, которые появляются в тексте */
  text_entities?: MessageEntity[];
  /** Анимация, которая будет отображаться в игровом сообщении в чатах */
  animation?: Animation;
}

/**
 * Опрос
 */
export interface Poll {
  /** Уникальный идентификатор опроса */
  id: string;
  /** Вопрос опроса */
  question: string;
  /** Список вариантов ответа */
  options: PollOption[];
  /** Общее количество пользователей, проголосовавших в опросе */
  total_voter_count: number;
  /** True, если опрос закрыт */
  is_closed: boolean;
  /** True, если опрос анонимный */
  is_anonymous: boolean;
  /** Тип опроса */
  type: 'regular' | 'quiz';
  /** True, если опрос позволяет множественный выбор */
  allows_multiple_answers: boolean;
  /** Индекс правильного варианта ответа (0-based) */
  correct_option_id?: number;
  /** Текст, который показывается, когда пользователь выбирает неправильный ответ */
  explanation?: string;
  /** Специальные сущности в объяснении */
  explanation_entities?: MessageEntity[];
  /** Время в секундах, в течение которого опрос будет активен */
  open_period?: number;
  /** Дата и время, когда опрос будет автоматически закрыт */
  close_date?: number;
}

/**
 * Вариант ответа в опросе
 */
export interface PollOption {
  /** Текст варианта ответа */
  text: string;
  /** Количество пользователей, выбравших этот вариант */
  voter_count: number;
}

/**
 * Ответ в опросе
 */
export interface PollAnswer {
  /** Уникальный идентификатор опроса */
  poll_id: string;
  /** Идентификатор чата, если опрос был отправлен в чат */
  chat?: Chat;
  /** Пользователь, который изменил ответ в опросе */
  user?: User;
  /** Индексы выбранных вариантов ответа (0-based) */
  option_ids: number[];
}

/**
 * Игральная кость
 */
export interface Dice {
  /** Эмодзи, на котором базируется анимация */
  emoji: string;
  /** Значение кости */
  value: number;
}

/**
 * Сущность сообщения
 */
export interface MessageEntity {
  /** Тип сущности */
  type: 'mention' | 'hashtag' | 'cashtag' | 'bot_command' | 'url' | 'email' | 'phone_number' | 
        'bold' | 'italic' | 'underline' | 'strikethrough' | 'spoiler' | 'blockquote' | 
        'code' | 'pre' | 'text_link' | 'text_mention' | 'custom_emoji';
  /** Смещение в единицах UTF-16 кода до начала сущности */
  offset: number;
  /** Длина сущности в единицах UTF-16 кода */
  length: number;
  /** URL для "text_link" */
  url?: string;
  /** Пользователь для "text_mention" */
  user?: User;
  /** Язык программирования для "pre" */
  language?: string;
  /** Уникальный идентификатор пользовательского эмодзи */
  custom_emoji_id?: string;
}

/**
 * Фото чата
 */
export interface ChatPhoto {
  /** Идентификатор файла маленького фото чата */
  small_file_id: string;
  /** Уникальный идентификатор файла маленького фото чата */
  small_file_unique_id: string;
  /** Идентификатор файла большого фото чата */
  big_file_id: string;
  /** Уникальный идентификатор файла большого фото чата */
  big_file_unique_id: string;
}

/**
 * Права чата
 */
export interface ChatPermissions {
  /** True, если пользователь может отправлять текстовые сообщения */
  can_send_messages?: boolean;
  /** True, если пользователь может отправлять аудио, документы, фото, видео */
  can_send_media_messages?: boolean;
  /** True, если пользователь может отправлять опросы */
  can_send_polls?: boolean;
  /** True, если пользователь может отправлять анимации, игры, стикеры */
  can_send_other_messages?: boolean;
  /** True, если пользователь может добавлять превью веб страниц */
  can_add_web_page_previews?: boolean;
  /** True, если пользователь может изменять название чата, фото и другие настройки */
  can_change_info?: boolean;
  /** True, если пользователь может приглашать новых пользователей */
  can_invite_users?: boolean;
  /** True, если пользователь может закреплять сообщения */
  can_pin_messages?: boolean;
  /** True, если пользователь может управлять темами */
  can_manage_topics?: boolean;
}

/**
 * Местоположение чата
 */
export interface ChatLocation {
  /** Местоположение, к которому привязан супергруппа */
  location: Location;
  /** Адрес местоположения */
  address: string;
}

/**
 * Ссылка-приглашение в чат
 */
export interface ChatInviteLink {
  /** Ссылка-приглашение */
  invite_link: string;
  /** Создатель ссылки */
  creator: User;
  /** True, если ссылка создает запрос на присоединение */
  creates_join_request: boolean;
  /** True, если ссылка основная */
  is_primary: boolean;
  /** True, если ссылка отозвана */
  is_revoked: boolean;
  /** Название ссылки-приглашения */
  name?: string;
  /** Дата истечения ссылки */
  expire_date?: number;
  /** Максимальное количество пользователей */
  member_limit?: number;
  /** Количество ожидающих пользователей */
  pending_join_request_count?: number;
}

/**
 * Запрос на присоединение к чату
 */
export interface ChatJoinRequest {
  /** Чат, к которому пользователь хочет присоединиться */
  chat: Chat;
  /** Пользователь, который отправил запрос на присоединение */
  from: User;
  /** Идентификатор пользователя, который отправил запрос на присоединение */
  user_chat_id: number;
  /** Дата отправки запроса */
  date: number;
  /** Биография пользователя */
  bio?: string;
  /** Ссылка-приглашение в чат */
  invite_link?: ChatInviteLink;
}

// =====================================
// ТИПЫ ДЛЯ WEBHOOK
// =====================================

/**
 * Информация о webhook
 */
export interface WebhookInfo {
  /** URL webhook */
  url: string;
  /** True, если пользовательский сертификат был предоставлен для проверки webhook */
  has_custom_certificate: boolean;
  /** Количество ожидающих обновлений */
  pending_update_count: number;
  /** IP адрес, с которого будут отправляться webhook запросы */
  ip_address?: string;
  /** Дата последней ошибки */
  last_error_date?: number;
  /** Сообщение об ошибке */
  last_error_message?: string;
  /** Дата последнего синхронизированного обновления */
  last_synchronization_error_date?: number;
  /** Максимальное разрешённое количество одновременных HTTPS соединений к webhook */
  max_connections?: number;
  /** Список типов обновлений, на которые подписан бот */
  allowed_updates?: string[];
}

// =====================================
// ДОПОЛНИТЕЛЬНЫЕ ВСПОМОГАТЕЛЬНЫЕ ТИПЫ
// =====================================

/**
 * Команда бота
 */
export interface BotCommand {
  /** Текст команды */
  command: string;
  /** Описание команды */
  description: string;
}

/**
 * Область команд бота
 */
export type BotCommandScope = 
  | BotCommandScopeDefault
  | BotCommandScopeAllPrivateChats
  | BotCommandScopeAllGroupChats
  | BotCommandScopeAllChatAdministrators
  | BotCommandScopeChat
  | BotCommandScopeChatAdministrators
  | BotCommandScopeChatMember;

/**
 * Область команд по умолчанию
 */
export interface BotCommandScopeDefault {
  type: 'default';
}

/**
 * Область команд для всех приватных чатов
 */
export interface BotCommandScopeAllPrivateChats {
  type: 'all_private_chats';
}

/**
 * Область команд для всех групповых чатов
 */
export interface BotCommandScopeAllGroupChats {
  type: 'all_group_chats';
}

/**
 * Область команд для всех администраторов чатов
 */
export interface BotCommandScopeAllChatAdministrators {
  type: 'all_chat_administrators';
}

/**
 * Область команд для конкретного чата
 */
export interface BotCommandScopeChat {
  type: 'chat';
  chat_id: number | string;
}

/**
 * Область команд для администраторов конкретного чата
 */
export interface BotCommandScopeChatAdministrators {
  type: 'chat_administrators';
  chat_id: number | string;
}

/**
 * Область команд для конкретного участника чата
 */
export interface BotCommandScopeChatMember {
  type: 'chat_member';
  chat_id: number | string;
  user_id: number;
}

/**
 * Параметры ответа
 */
export interface ResponseParameters {
  /** Группа была мигрирована в супергруппу с указанным идентификатором */
  migrate_to_chat_id?: number;
  /** В случае превышения лимита, количество секунд, через которое можно повторить запрос */
  retry_after?: number;
}

/**
 * Тип кнопки меню
 */
export type MenuButton = MenuButtonCommands | MenuButtonWebApp | MenuButtonDefault;

/**
 * Кнопка меню с командами
 */
export interface MenuButtonCommands {
  type: 'commands';
}

/**
 * Кнопка меню с Web App
 */
export interface MenuButtonWebApp {
  type: 'web_app';
  text: string;
  web_app: WebApp;
}

/**
 * Кнопка меню по умолчанию
 */
export interface MenuButtonDefault {
  type: 'default';
}

// =====================================
// ТИПЫ ДЛЯ БИЗНЕС АККАУНТОВ (API 7.2+)
// =====================================

/**
 * Бизнес соединение (новое в API 7.2)
 */
export interface BusinessConnection {
  /** Уникальный идентификатор бизнес соединения */
  id: string;
  /** Бизнес аккаунт пользователя */
  user: User;
  /** Идентификатор приватного чата с бизнес аккаунтом */
  user_chat_id: number;
  /** Дата соединения */
  date: number;
  /** True, если соединение активно */
  is_enabled: boolean;
  /** True, если бизнес соединение было отключено */
  can_reply: boolean;
}

/**
 * Удалённые бизнес сообщения (новое в API 7.2)
 */
export interface BusinessMessagesDeleted {
  /** Уникальный идентификатор бизнес соединения */
  business_connection_id: string;
  /** Информация о чате в бизнес аккаунте */
  chat: Chat;
  /** Список идентификаторов удалённых сообщений */
  message_ids: number[];
}

// =====================================
// ЭКСПОРТ ВСЕХ ТИПОВ ДЛЯ УДОБСТВА
// =====================================

/**
 * Объединённый тип разметки клавиатуры
 */
export type ReplyMarkup = 
  | InlineKeyboardMarkup 
  | ReplyKeyboardMarkup 
  | ReplyKeyboardRemove 
  | ForceReply;

/**
 * Объединённый тип участника чата
 */
export type ChatMemberUnion = 
  | ChatMemberOwner 
  | ChatMemberAdministrator 
  | ChatMemberMember 
  | ChatMemberRestricted 
  | ChatMemberLeft 
  | ChatMemberBanned;

/**
 * Тип для парсинга сообщений
 */
export type ParseMode = 'HTML' | 'Markdown' | 'MarkdownV2';

/**
 * Тип реакции
 */
export type ReactionType = ReactionTypeEmoji | ReactionTypeCustomEmoji;

/**
 * Реакция эмодзи
 */
export interface ReactionTypeEmoji {
  type: 'emoji';
  emoji: string;
}

/**
 * Реакция пользовательского эмодзи
 */
export interface ReactionTypeCustomEmoji {
  type: 'custom_emoji';
  custom_emoji_id: string;
}

/**
 * Обновление реакций на сообщение
 */
export interface MessageReactionUpdated {
  /** Чат, содержащий сообщение */
  chat: Chat;
  /** Уникальный идентификатор сообщения */
  message_id: number;
  /** Пользователь, который изменил реакцию */
  user?: User;
  /** Дата изменения */
  date: number;
  /** Предыдущий список реакций */
  old_reaction: ReactionType[];
  /** Новый список реакций */
  new_reaction: ReactionType[];
}

/**
 * Обновление счётчика реакций
 */
export interface MessageReactionCountUpdated {
  /** Чат, содержащий сообщение */
  chat: Chat;
  /** Уникальный идентификатор сообщения */
  message_id: number;
  /** Дата изменения */
  date: number;
  /** Список реакций */
  reactions: ReactionCount[];
}

/**
 * Счётчик реакции
 */
export interface ReactionCount {
  /** Тип реакции */
  type: ReactionType;
  /** Количество реакций этого типа */
  total_count: number;
}

// Дополнительные типы, которые могут понадобиться
export interface MaskPosition {
  /** Часть лица, относительно которой должна располагаться маска */
  point: 'forehead' | 'eyes' | 'mouth' | 'chin';
  /** Сдвиг по X относительно точки */
  x_shift: number;
  /** Сдвиг по Y относительно точки */
  y_shift: number;
  /** Коэффициент масштабирования маски */
  scale: number;
}

export interface KeyboardButtonPollType {
  /** Тип опроса, который может создать пользователь */
  type?: 'quiz' | 'regular';
}

export interface KeyboardButtonRequestUsers {
  /** Идентификатор запроса */
  request_id: number;
  /** True, если может запросить ботов */
  user_is_bot?: boolean;
  /** True, если может запросить премиум пользователей */
  user_is_premium?: boolean;
  /** Максимальное количество пользователей для выбора */
  max_quantity?: number;
  /** True, если нужно запросить имя пользователя */
  request_name?: boolean;
  /** True, если нужно запросить имя пользователя */
  request_username?: boolean;
  /** True, если нужно запросить фото */
  request_photo?: boolean;
}

export interface KeyboardButtonRequestChat {
  /** Идентификатор запроса */
  request_id: number;
  /** True, если чат должен быть каналом */
  chat_is_channel: boolean;
  /** True, если чат должен быть форумом */
  chat_is_forum?: boolean;
  /** True, если чат должен иметь имя пользователя */
  chat_has_username?: boolean;
  /** True, если чат должен быть создан */
  chat_is_created?: boolean;
  /** Права пользователя в чате */
  user_administrator_rights?: ChatAdministratorRights;
  /** Права бота в чате */
  bot_administrator_rights?: ChatAdministratorRights;
  /** True, если пользователь должен быть участником чата */
  bot_is_member?: boolean;
  /** True, если нужно запросить название */
  request_title?: boolean;
  /** True, если нужно запросить имя пользователя */
  request_username?: boolean;
  /** True, если нужно запросить фото */
  request_photo?: boolean;
}

export interface ChatAdministratorRights {
  /** True, если администратор уникален */
  is_anonymous: boolean;
  /** True, если администратор может получить доступ к логам чата */
  can_manage_chat: boolean;
  /** True, если администратор может удалять сообщения других пользователей */
  can_delete_messages: boolean;
  /** True, если администратор может управлять видеочатами */
  can_manage_video_chats: boolean;
  /** True, если администратор может ограничивать, банить или разбанивать участников чата */
  can_restrict_members: boolean;
  /** True, если администратор может добавлять новых администраторов */
  can_promote_members: boolean;
  /** True, если администратор может изменять название чата, фото и другие настройки */
  can_change_info: boolean;
  /** True, если администратор может приглашать новых пользователей в чат */
  can_invite_users: boolean;
  /** True, если администратор может постить в канале */
  can_post_messages?: boolean;
  /** True, если администратор может редактировать сообщения других пользователей */
  can_edit_messages?: boolean;
  /** True, если администратор может закреплять сообщения */
  can_pin_messages?: boolean;
  /** True, если администратор может управлять темами */
  can_manage_topics?: boolean;
}

export interface SwitchInlineQueryChosenChat {
  /** Текст, который появится в поле ввода после выбора чата */
  query?: string;
  /** True, если разрешены приватные чаты */
  allow_user_chats?: boolean;
  /** True, если разрешены боты */
  allow_bot_chats?: boolean;
  /** True, если разрешены группы */
  allow_group_chats?: boolean;
  /** True, если разрешены каналы */
  allow_channel_chats?: boolean;
}

export interface CallbackGame {
  // Заглушка, содержит только {}
}

export interface UsersShared {
  /** Идентификатор запроса */
  request_id: number;
  /** Информация о пользователях */
  users: SharedUser[];
}

export interface SharedUser {
  /** Идентификатор пользователя */
  user_id: number;
  /** Имя пользователя */
  first_name?: string;
  /** Фамилия пользователя */
  last_name?: string;
  /** Имя пользователя */
  username?: string;
  /** Фото пользователя */
  photo?: PhotoSize[];
}

export interface ChatShared {
  /** Идентификатор запроса */
  request_id: number;
  /** Идентификатор чата */
  chat_id: number;
  /** Название чата */
  title?: string;
  /** Имя пользователя чата */
  username?: string;
  /** Фото чата */
  photo?: PhotoSize[];
}

// Добавляем недостающие типы для полноты
export interface MessageOrigin {
  /** Тип источника сообщения */
  type: 'user' | 'hidden_user' | 'chat' | 'channel';
  /** Дата отправки оригинального сообщения */
  date: number;
}

export interface ExternalReplyInfo {
  /** Источник сообщения */
  origin: MessageOrigin;
  /** Чат, в котором было отправлено оригинальное сообщение */
  chat?: Chat;
  /** Уникальный идентификатор сообщения */
  message_id?: number;
  /** Параметры превью ссылки */
  link_preview_options?: LinkPreviewOptions;
  /** Анимация, документ, аудио, фото, стикер, видео, видео заметка, голосовое сообщение, контакт, игральная кость, игра, опрос, место или чек-лист */
  animation?: Animation;
  audio?: Audio;
  document?: Document;
  photo?: PhotoSize[];
  sticker?: Sticker;
  story?: Story;
  video?: Video;
  video_note?: VideoNote;
  voice?: Voice;
  /** True, если медиа в сообщении покрыто спойлером */
  has_media_spoiler?: boolean;
  /** Контакт */
  contact?: Contact;
  /** Игральная кость */
  dice?: Dice;
  /** Игра */
  game?: Game;
  /** Опрос */
  poll?: Poll;
  /** Место */
  venue?: Venue;
  /** Местоположение */
  location?: Location;
  /** Чек-лист */
  checklist?: Checklist;
}

export interface TextQuote {
  /** Текст цитаты */
  text: string;
  /** Специальные сущности, которые появляются в цитате */
  entities?: MessageEntity[];
  /** Позиция цитаты в оригинальном сообщении */
  position: number;
  /** True, если цитата была добавлена вручную */
  is_manual?: boolean;
}

export interface Story {
  /** Уникальный идентификатор истории */
  id: number;
  /** Чат, который опубликовал историю */
  chat: Chat;
}

export interface LinkPreviewOptions {
  /** True, если превью ссылки отключено */
  is_disabled?: boolean;
  /** URL для использования в превью ссылки */
  url?: string;
  /** True, если медиа в превью должно быть меньше */
  prefer_small_media?: boolean;
  /** True, если медиа в превью должно быть больше */
  prefer_large_media?: boolean;
  /** True, если превью должно быть показано выше текста */
  show_above_text?: boolean;
}

export interface MessageAutoDeleteTimerChanged {
  /** Новое время автоудаления сообщений в секундах */
  message_auto_delete_time: number;
}

export interface PassportData {
  /** Массив с информацией о документах */
  data: EncryptedPassportElement[];
  /** Зашифрованные учётные данные */
  credentials: EncryptedCredentials;
}

export interface EncryptedPassportElement {
  /** Тип элемента */
  type: string;
  /** Данные */
  data?: string;
  /** Номер телефона */
  phone_number?: string;
  /** Email */
  email?: string;
  /** Массив зашифрованных файлов */
  files?: PassportFile[];
  /** Зашифрованный файл с лицевой стороной документа */
  front_side?: PassportFile;
  /** Зашифрованный файл с обратной стороной документа */
  reverse_side?: PassportFile;
  /** Зашифрованный файл с селфи */
  selfie?: PassportFile;
  /** Массив зашифрованных файлов с переводом */
  translation?: PassportFile[];
  /** Хеш кодированных данных */
  hash: string;
}

export interface EncryptedCredentials {
  /** Зашифрованные данные */
  data: string;
  /** Хеш */
  hash: string;
  /** Секрет */
  secret: string;
}

export interface PassportFile {
  /** Идентификатор файла */
  file_id: string;
  /** Уникальный идентификатор файла */
  file_unique_id: string;
  /** Размер файла */
  file_size: number;
  /** Дата загрузки файла */
  file_date: number;
}

export interface ProximityAlertTriggered {
  /** Пользователь, который вызвал алерт */
  traveler: User;
  /** Пользователь, который установил алерт */
  watcher: User;
  /** Дистанция между пользователями */
  distance: number;
}

export interface ChatBoostAdded {
  /** Количество бустов */
  boost_count: number;
}

export interface ChatBoostUpdated {
  /** Чат, который получил буст */
  chat: Chat;
  /** Информация о бусте */
  boost: ChatBoost;
}

export interface ChatBoostRemoved {
  /** Чат, который потерял буст */
  chat: Chat;
  /** Уникальный идентификатор буста */
  boost_id: string;
  /** Дата удаления буста */
  remove_date: number;
  /** Источник буста */
  source: ChatBoostSource;
}

export interface ChatBoost {
  /** Уникальный идентификатор буста */
  boost_id: string;
  /** Дата добавления буста */
  add_date: number;
  /** Дата истечения буста */
  expiration_date: number;
  /** Источник буста */
  source: ChatBoostSource;
}

export type ChatBoostSource = ChatBoostSourcePremium | ChatBoostSourceGiftCode | ChatBoostSourceGiveaway;

export interface ChatBoostSourcePremium {
  source: 'premium';
  /** Пользователь, который дал буст */
  user: User;
}

export interface ChatBoostSourceGiftCode {
  source: 'gift_code';
  /** Пользователь, для которого был создан гифт код */
  user: User;
}

export interface ChatBoostSourceGiveaway {
  source: 'giveaway';
  /** Идентификатор сообщения розыгрыша в чате */
  giveaway_message_id: number;
  /** Пользователь, который выиграл подарок */
  user?: User;
  /** True, если подарок был получен без публичного анонса */
  is_unclaimed?: boolean;
}

export interface ChatBackgroundSet {
  /** Тип фона чата */
  type: string;
}

export interface ForumTopicCreated {
  /** Название темы */
  name: string;
  /** Цвет иконки темы */
  icon_color: number;
  /** Уникальный идентификатор пользовательского эмодзи */
  icon_custom_emoji_id?: string;
}

export interface ForumTopicEdited {
  /** Новое название темы */
  name?: string;
  /** Новый уникальный идентификатор пользовательского эмодзи */
  icon_custom_emoji_id?: string;
}

export interface ForumTopicClosed {
  // Пустой объект
}

export interface ForumTopicReopened {
  // Пустой объект
}

export interface GeneralForumTopicHidden {
  // Пустой объект
}

export interface GeneralForumTopicUnhidden {
  // Пустой объект
}

export interface VideoChatScheduled {
  /** Дата, когда видеочат должен начаться */
  start_date: number;
}

export interface VideoChatStarted {
  // Пустой объект
}

export interface VideoChatEnded {
  /** Продолжительность видеочата в секундах */
  duration: number;
}

export interface VideoChatParticipantsInvited {
  /** Новые участники, приглашённые в видеочат */
  users: User[];
}

export interface PurchasedPaidMedia {
  /** Пользователь, который купил медиа */
  from: User;
  /** Полезная нагрузка, определённая ботом */
  paid_media_payload: string;
}

// Добавим недостающие типы для дат рождения и бизнеса
export interface Birthdate {
  /** День рождения */
  day: number;
  /** Месяц рождения */
  month: number;
  /** Год рождения */
  year?: number;
}

export interface BusinessIntro {
  /** Название интро */
  title?: string;
  /** Сообщение интро */
  message?: string;
  /** Стикер интро */
  sticker?: Sticker;
}

export interface BusinessLocation {
  /** Адрес бизнеса */
  address: string;
  /** Местоположение бизнеса */
  location?: Location;
}

export interface BusinessOpeningHours {
  /** Название часового пояса */
  time_zone_name: string;
  /** Список интервалов времени работы */
  opening_hours: BusinessOpeningHoursInterval[];
}

export interface BusinessOpeningHoursInterval {
  /** Минута открытия */
  opening_minute: number;
  /** Минута закрытия */
  closing_minute: number;
}

// Экспорт главного объединяющего типа для всех типов сообщений
export type TelegramUpdate = Update;
export type TelegramMessage = Message;
export type TelegramUser = User;
export type TelegramChat = Chat;
