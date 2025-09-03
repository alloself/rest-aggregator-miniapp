<template>
  <div class="repeat-button-container">
    <!-- Тоггл кнопка -->
    <button 
      class="repeat-button__toggle"
      :class="{ 'repeat-button__toggle--active': isRepeated }"
      :disabled="disabled"
      @click="handleToggle"
    >
      <div 
        class="repeat-button__toggle-indicator"
        :class="{ 'repeat-button__toggle-indicator--active': isRepeated }"
      >
        R
      </div>
      
      <!-- Лейбл внутри тоггла -->
      <span class="repeat-button__label">Repeat</span>
    </button>
    
    <!-- Аватары друзей и счетчик -->
    <div 
      v-if="(props.friends || []).length > 0" 
      class="repeat-button__friends"
      @click="handleFriendsClick"
    >
      <div class="repeat-button__avatars">
        <div 
          v-for="(friend, index) in visibleFriends" 
          :key="friend.id"
          class="repeat-button__avatar"
          :style="{ 
            zIndex: 3 - index,
            backgroundColor: getAvatarUrl(friend) ? 'transparent' : generateRandomColor(getDisplayName(friend))
          }"
        >
          <!-- Аватар изображение -->
          <img 
            v-if="getAvatarUrl(friend)" 
            :src="getAvatarUrl(friend)" 
            :alt="getDisplayName(friend)"
            class="repeat-button__avatar-img"
          />
          <!-- Инициалы -->
          <span 
            v-else 
            class="repeat-button__avatar-initials"
          >
            {{ getInitials(friend) }}
          </span>
        </div>
      </div>
      <span v-if="additionalFriendsCount > 0" class="repeat-button__count">+{{ additionalFriendsCount }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { User } from '@/shared/types';

// Структура друга из API
interface Friend {
  id: string;
  name: string;
  username: string;
  profile_photo_url?: string | null;
}

// Union type для всех кто лайкнул ресторан
type UserOrFriend = User | Friend;

interface Props {
  isRepeated?: boolean;
  friendsCount?: number;
  friends?: UserOrFriend[];
  disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  isRepeated: false,
  friendsCount: 0,
  friends: () => [],
  disabled: false,
});

const emit = defineEmits<{
  toggle: [isRepeated: boolean];
  friendsClick: [friends: UserOrFriend[]];
}>();

// Обработка клика по тогглу
const handleToggle = (): void => {
  const newState = !props.isRepeated;
  emit('toggle', newState);
};

// Обработка клика по секции друзей
const handleFriendsClick = (): void => {
  emit('friendsClick', props.friends || []);
};

// Генерация рандомного цвета для инициалов
const generateRandomColor = (seed: string): string => {
  const colors = [
    '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FECA57',
    '#FF9FF3', '#54A0FF', '#5F27CD', '#00D2D3', '#FF9F43',
    '#EE5A24', '#0ABDE3', '#10AC84', '#F79F1F', '#A3CB38'
  ];
  
  let hash = 0;
  for (let i = 0; i < seed.length; i++) {
    hash = seed.charCodeAt(i) + ((hash << 5) - hash);
  }
  
  return colors[Math.abs(hash) % colors.length];
};

// Проверка является ли объект Friend или User
const isFriend = (user: UserOrFriend): user is Friend => {
  return 'name' in user;
};

// Получение URL аватара
const getAvatarUrl = (user: UserOrFriend): string | undefined => {
  if (isFriend(user)) {
    return user.profile_photo_url || undefined;
  }
  return user.full_avatar_url || user.avatar_url || undefined;
};

// Получение отображаемого имени для цвета
const getDisplayName = (user: UserOrFriend): string => {
  if (isFriend(user)) {
    return user.name;
  }
  return `${user.first_name || ''} ${user.last_name || ''}`.trim() || 'User';
};

// Получение инициалов из имени пользователя
const getInitials = (user: UserOrFriend): string => {
  // Для друзей есть поле name
  if (isFriend(user)) {
    const parts = user.name.split(' ');
    const firstInitial = parts[0]?.charAt(0) || '';
    const lastInitial = parts[1]?.charAt(0) || '';
    return (firstInitial + lastInitial).toUpperCase() || firstInitial.toUpperCase() || 'U';
  }
  
  // Для обычных пользователей
  const firstInitial = user.first_name?.charAt(0) || '';
  const lastInitial = user.last_name?.charAt(0) || '';
  return (firstInitial + lastInitial).toUpperCase() || user.first_name?.charAt(0)?.toUpperCase() || 'U';
};

// Получение видимых друзей (максимум 3)
const visibleFriends = computed(() => {
  return (props.friends || []).slice(0, 3);
});

// Количество дополнительных друзей (которые не помещаются в кружки)
const additionalFriendsCount = computed(() => {
  const totalFriends = (props.friends || []).length;
  return totalFriends > 3 ? totalFriends - 3 : 0;
});
</script>

<style scoped>
.repeat-button-container {
  display: flex;
  align-items: center;
  gap: 6px;
}

.repeat-button__toggle {
  position: relative;
  display: flex;
  align-items: center;
  width: 85px;
  height: 36px;
  border-radius: 20px;
  background: rgba(209, 213, 219, 0.46);
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  padding: 2px;
  backdrop-filter: blur(4px);
}

.repeat-button__toggle--active {
  background: rgba(93, 92, 92, 0.7);
}

.repeat-button__toggle:hover:not(:disabled) {
  background: rgba(209, 213, 219, 0.6);
}

.repeat-button__toggle--active:hover:not(:disabled) {
  background: rgba(93, 92, 92, 0.8);
}

.repeat-button__toggle:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.repeat-button__toggle-indicator {
  position: absolute;
  top: 2px;
  left: 2px;
  width: 32px;
  height: 32px;
  border-radius: 18px;
  background: #FFF1C3;
  border: 1px solid rgba(255, 251, 239, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: Inter, sans-serif;
  font-weight: 600;
  font-size: 10px;
  line-height: 1.35;
  letter-spacing: -0.03em;
  color: rgba(0, 0, 0, 0.75);
  transition: all 0.2s ease;
  transform: translateX(0);
  z-index: 1;
}

.repeat-button__toggle-indicator--active {
  transform: translateX(49px);
}

.repeat-button__label {
  position: absolute;
  left: 40px;
  top: 50%;
  transform: translateY(-50%);
  font-family: Inter, sans-serif;
  font-weight: 500;
  font-size: 10px;
  line-height: 1.35;
  letter-spacing: -0.03em;
  color: #000000;
  user-select: none;
  transition: all 0.2s ease;
  z-index: 2;
}

.repeat-button__toggle--active .repeat-button__label {
  color: #FFFFFF;
  left: 13px;
}

.repeat-button__friends {
  display: flex;
  align-items: center;
  gap: 4px;
  background: rgba(0, 0, 0, 0.3);
  border-radius: 20px;
  padding: 4px 8px 4px 4px;
  backdrop-filter: blur(4px);
  cursor: pointer;
  transition: background 0.2s ease;
}

.repeat-button__friends:hover {
  background: rgba(0, 0, 0, 0.4);
}

.repeat-button__avatars {
  display: flex;
  position: relative;
  width: 57px;
  height: 32px;
}

.repeat-button__avatar {
  position: absolute;
  width: 32px;
  height: 32px;
  border-radius: 20px;
  border: 1px solid rgba(255, 251, 239, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.repeat-button__avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 19px;
}

.repeat-button__avatar-initials {
  font-family: Inter, sans-serif;
  font-weight: 600;
  font-size: 12px;
  line-height: 1;
  color: #FFFFFF;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.repeat-button__avatar:nth-child(1) {
  left: 0;
}

.repeat-button__avatar:nth-child(2) {
  left: 12px;
}

.repeat-button__avatar:nth-child(3) {
  left: 25px;
}

.repeat-button__count {
  font-family: Inter, sans-serif;
  font-weight: 600;
  font-size: 14px;
  line-height: 1.35;
  letter-spacing: -0.03em;
  color: #FFFFFF;
  flex-shrink: 0;
}
</style>
