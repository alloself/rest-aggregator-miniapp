/**
 * Композабл для работы с Telegram Web App shareToStory API
 * Документация: https://core.telegram.org/bots/features#sharing-from-mini-apps-to-stories
 */

import { computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useRestaurantStore } from '@site/ts/entities/restaurant';

interface ShareToStoryOptions {
  /** Текст, который будет добавлен в сторис */
  text?: string;
  /** Ссылка виджета для сторис */
  widgetLink?: {
    url: string;
    name?: string;
  };
}

export function useTelegramStoryShare() {
  // Проверяем доступность Telegram WebApp
  const isAvailable = computed(() => {
    const tg = window.Telegram?.WebApp;
    return Boolean(tg && typeof tg.shareToStory === 'function');
  });

  // Проверяем версию (функция shareToStory доступна с версии 7.8)
  const isVersionSupported = computed(() => {
    const tg = window.Telegram?.WebApp;
    return Boolean(tg?.isVersionAtLeast?.('7.8'));
  });

  // Основная функция поделиться в сторис
  const shareToStory = (mediaUrl: string, options?: ShareToStoryOptions): boolean => {
    if (!isAvailable.value || !isVersionSupported.value) {
      console.warn('Telegram WebApp shareToStory is not available');
      return false;
    }

    const tg = window.Telegram?.WebApp;
    if (!tg?.shareToStory) {
      return false;
    }

    try {
      const params: StoryShareParams = {};
      
      if (options?.text) {
        params.text = options.text;
      }
      
      if (options?.widgetLink) {
        params.widget_link = {
          url: options.widgetLink.url,
          name: options.widgetLink.name || 'Открыть',
        };
      }

      tg.shareToStory(mediaUrl, params);
      return true;
    } catch (error) {
      console.error('Error sharing to story:', error);
      return false;
    }
  };

  // Функция для шаринга новости в сторис
  const shareNewsToStory = (
    imageUrl: string,
    title: string,
    restaurantSlug: string,
    newsSlug: string
  ): boolean => {
    const restaurantStore = useRestaurantStore();
    const { restaurant } = storeToRefs(restaurantStore);
    const botUsername = restaurant.value?.bot_username;

    // Формируем deep link Mini App через startapp payload
    // стартовый параметр должен содержать только [A-Za-z0-9_-], максимум 64 символа
    // используем компактную схему: n_<newsSlug>
    const payload = `n_${newsSlug}`;
    const widgetUrl = botUsername
      ? `https://t.me/${botUsername}?startapp=${payload}`
      : `${window.location.origin}/restaurant/${restaurantSlug}`;

    return shareToStory(imageUrl, {
      text: `${title}`,
      widgetLink: {
        url: widgetUrl,
        name: 'Читать новость',
      },
    });
  };

  // Функция для шаринга события в сторис
  const shareEventToStory = (
    imageUrl: string,
    title: string,
    restaurantSlug: string,
    eventSlug: string
  ): boolean => {
    const restaurantStore = useRestaurantStore();
    const { restaurant } = storeToRefs(restaurantStore);
    const botUsername = restaurant.value?.bot_username;

    // компактная схема: e_<eventSlug>
    const payload = `e_${eventSlug}`;
    const widgetUrl = botUsername
      ? `https://t.me/${botUsername}?startapp=${payload}`
      : `${window.location.origin}/restaurant/${restaurantSlug}`;

    return shareToStory(imageUrl, {
      text: `${title}`,
      widgetLink: {
        url: widgetUrl,
        name: 'Подробнее',
      },
    });
  };

  return {
    /** Доступна ли функция shareToStory */
    isAvailable,
    /** Поддерживается ли версия Telegram */
    isVersionSupported,
    /** Основная функция для шаринга в сторис */
    shareToStory,
    /** Функция для шаринга новости в сторис */
    shareNewsToStory,
    /** Функция для шаринга события в сторис */
    shareEventToStory,
  };
}
