import { defineStore } from 'pinia';
import { ref } from 'vue';
import { client } from '../api/axios';
import type { User } from '../../../../shared/types/models';

export const useMiniAppStore = defineStore('miniapp', () => {
  const me = ref<User | null>(null);
  const friends = ref<User[]>([]);
  const likedByMe = ref(false);
  const isLiking = ref(false);

  async function authWithInitData(slug: string): Promise<void> {
    const tg = window.Telegram?.WebApp;
    tg?.ready?.();
    const initData = tg?.initData ?? '';
    if (!initData) return;
    const { data } = await client.post<User & { liked_by_me?: boolean }>(`/api/site/miniapp/restaurants/${slug}/auth`, { init_data: initData });
    me.value = data;
    friends.value = Array.isArray(data.friends) ? data.friends : [];
    likedByMe.value = Boolean(data.liked_by_me);
  }

  async function toggleRestaurantLike(restaurantId: string): Promise<void> {
    if (!me.value) return;
    if (isLiking.value) return;
    isLiking.value = true;
    try {
      const wasLiked = likedByMe.value;
      likedByMe.value = !wasLiked; // optimistic

      if (wasLiked) {
        await client.delete('/api/site/likes', {
          params: {
            user_id: me.value.id,
            likeable_type: 'restaurant',
            likeable_id: restaurantId,
          },
        });
      } else {
        await client.post('/api/site/likes', {
          user_id: me.value.id,
          likeable_type: 'restaurant',
          likeable_id: restaurantId,
        });
      }
    } finally {
      isLiking.value = false;
    }
  }

  return {
    me,
    friends,
    likedByMe,
    isLiking,
    authWithInitData,
    toggleRestaurantLike,
  };
});


