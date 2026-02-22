<template>
  <div class="share-bottom-sheet">
    <div class="share-bottom-sheet__actions">
      <button 
        class="share-bottom-sheet__action"
        :class="{ 'share-bottom-sheet__action--disabled': !canAddToStory }"
        :title="addToStoryTitle"
        @click="handleAddToStory"
      >
        <div class="share-bottom-sheet__action-icon">
          <Icon name="streamline-sharp:story-post" class="share-bottom-sheet__icon" />
        </div>
        <span class="share-bottom-sheet__action-text">Добавить в историю</span>
      </button>

      <button 
        class="share-bottom-sheet__action"
        @click="handleShare"
      >
        <div class="share-bottom-sheet__action-icon">
          <Icon name="humbleicons:share" class="share-bottom-sheet__icon" />
        </div>
        <span class="share-bottom-sheet__action-text">Поделиться</span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Icon from '@shared/ui/Icon.vue';
import { useTelegramStoryShare } from '../../shared/composables/useTelegramStoryShare';
import type { Event, News } from '@/shared/types/models';
import { storeToRefs } from 'pinia';
import { useRestaurantStore } from '@site/ts/entities/restaurant';

type Props = {
  type: 'event' | 'news';
  item: Event | News;
  slug: string;
  itemSlug: string;
};

const props = defineProps<Props>();

const emit = defineEmits<{
  close: [];
}>();

const { shareEventToStory, shareNewsToStory, isAvailable, isVersionSupported } = useTelegramStoryShare();

const restaurantStore = useRestaurantStore();
const { restaurant } = storeToRefs(restaurantStore);

const canAddToStory = computed(() => {
  const hasImages = props.item?.images && props.item.images.length > 0;
  return isAvailable.value && isVersionSupported.value && hasImages;
});

const addToStoryTitle = computed(() => {
  if (!isAvailable.value) return 'Функция недоступна в этом браузере';
  if (!isVersionSupported.value) return 'Требуется обновление Telegram';
  const hasImages = props.item?.images && props.item.images.length > 0;
  if (!hasImages) return 'Нет изображений для публикации';
  return 'Добавить в историю';
});

const handleAddToStory = () => {
  if (!props.item || !canAddToStory.value) {
    return;
  }
  
  let success = false;
  
  if (props.type === 'event') {
    const event = props.item as Event;
    success = shareEventToStory(
      event.images?.[0]?.url || '',
      event.title,
      props.slug,
      props.itemSlug
    );
    console.log('Событие добавлено в историю:', event.title);
  } else if (props.type === 'news') {
    const news = props.item as News;
    success = shareNewsToStory(
      news.images?.[0]?.url || '',
      news.title,
      props.slug,
      props.itemSlug
    );
    console.log('Новость добавлена в историю:', news.title);
  }
  
  if (success) {
    console.log('Успешно добавлено в историю');
  }
  
  emit('close');
};

const generateShareUrl = (): string => {
  const baseUrl = window.location.origin;
  const botUsername = restaurant.value?.bot_username || '';

  if (props.type === 'event') {
    const payload = `e_${props.itemSlug}`;
    return botUsername
      ? `https://t.me/${botUsername}?startapp=${payload}`
      : `${baseUrl}/restaurants/${props.slug}/events/${props.itemSlug}`;
  }

  const payload = `n_${props.itemSlug}`;
  return botUsername
    ? `https://t.me/${botUsername}?startapp=${payload}`
    : `${baseUrl}/restaurants/${props.slug}/news/${props.itemSlug}`;
};

const generateShareText = (): string => {
  if (props.type === 'event') {
    return `🎉 ${props.item.title}\n\nПосмотрите это событие!`;
  } else {
    return `📰 ${props.item.title}\n\nИнтересная новость!`;
  }
};

const copyToClipboard = async (text: string): Promise<boolean> => {
  try {
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(text);
      return true;
    } else {
      // Fallback для старых браузеров
      const textArea = document.createElement('textarea');
      textArea.value = text;
      textArea.style.position = 'fixed';
      textArea.style.left = '-999999px';
      textArea.style.top = '-999999px';
      document.body.appendChild(textArea);
      textArea.focus();
      textArea.select();
      const result = document.execCommand('copy');
      document.body.removeChild(textArea);
      return result;
    }
  } catch (error) {
    console.error('Ошибка копирования:', error);
    return false;
  }
};


const handleShare = async () => {
  if (!props.item) return;
  
  const shareUrl = generateShareUrl();
  const shareText = generateShareText();
  const shareData = {
    title: props.item.title,
    text: shareText,
    url: shareUrl
  };
  
  try {
    // Попытка использовать Web Share API
    if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
      await navigator.share(shareData);
      
      // Haptic feedback для Telegram WebApp
      const tg = window.Telegram?.WebApp;
      if (tg && 'HapticFeedback' in tg) {
        (tg as any).HapticFeedback?.impactOccurred?.('medium');
      }
      
      console.log('Успешно поделились через Web Share API');
    } else {
      // Fallback: копирование ссылки в буфер обмена
      const success = await copyToClipboard(shareUrl);
      
      if (success) {
        console.log('Ссылка скопирована:', shareUrl);
      } else {
        console.error('Не удалось скопировать ссылку');
      }
    }
  } catch (error) {
    console.error('Ошибка при поделиться:', error);
    // Fallback на копирование, если Web Share API дал ошибку
    const success = await copyToClipboard(shareUrl);
    if (success) {
      console.log('Ссылка скопирована в буфер обмена');
    } else {
      console.error('Не удалось поделиться');
    }
  }
  
  emit('close');
};
</script>

<style scoped>
.share-bottom-sheet {
  background: var(--color-bg-primary);
  padding: 32px 59px;
}

.share-bottom-sheet__actions {
  display: flex;
  gap: 0;
  justify-content: center;
  align-items: center;
}

.share-bottom-sheet__action {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  width: 161px;
  transition: opacity 0.2s ease;
}

.share-bottom-sheet__action:hover {
  opacity: 0.8;
}

.share-bottom-sheet__action:active {
  opacity: 0.6;
}

.share-bottom-sheet__action--disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.share-bottom-sheet__action--disabled:hover {
  opacity: 0.5;
}

.share-bottom-sheet__action--disabled:active {
  opacity: 0.5;
}

.share-bottom-sheet__action-icon {
  width: 70px;
  height: 70px;
  background: #F3F4F6;
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.share-bottom-sheet__icon {
  width: 31px;
  height: 31px;
  color: #000000;
}

.share-bottom-sheet__action-text {
  font-family: "SF Pro Display", sans-serif;
  font-weight: 500;
  font-size: 12px;
  line-height: 1.1;
  letter-spacing: -0.02em;
  color: #000000;
  text-align: center;
}
</style>