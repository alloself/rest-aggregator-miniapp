/**
 * Композабл для работы с Telegram Web App shareToStory API
 * Документация: https://core.telegram.org/bots/features#sharing-from-mini-apps-to-stories
 */

import { computed } from 'vue';

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
    newsId: string
  ): boolean => {
    const baseUrl = window.location.origin;
    const widgetUrl = `${baseUrl}/${restaurantSlug}?news=${newsId}`;
    
    return shareToStory(imageUrl, {
      text: `📰 ${title}`,
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
    eventId: string
  ): boolean => {
    const baseUrl = window.location.origin;
    const widgetUrl = `${baseUrl}/${restaurantSlug}?event=${eventId}`;
    
    return shareToStory(imageUrl, {
      text: `🎉 ${title}`,
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
