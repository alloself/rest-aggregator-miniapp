<template>
  <div class="friends-bottom-sheet">
    <!-- Заголовок -->
    <div class="friends-bottom-sheet__header">
      <h2 class="friends-bottom-sheet__title">Репиты</h2>
    </div>
    
    <!-- Список друзей -->
    <div class="friends-bottom-sheet__list">
      <div 
        v-for="friend in friends" 
        :key="friend.id"
        class="friends-bottom-sheet__item"
      >
        <div class="friends-bottom-sheet__avatar-container">
                      <!-- Аватар -->
            <div class="friends-bottom-sheet__avatar">
              <img 
                v-if="getAvatarUrl(friend)" 
                :src="getAvatarUrl(friend)" 
                :alt="getDisplayName(friend)"
                class="friends-bottom-sheet__avatar-img"
              />
              <span 
                v-else 
                class="friends-bottom-sheet__avatar-initials"
                :style="{ backgroundColor: generateRandomColor(getDisplayName(friend)) }"
              >
                {{ getInitials(friend) }}
              </span>
            </div>
          
          <!-- Значок R у всех (все лайкнули ресторан) -->
          <div class="friends-bottom-sheet__repeat-badge">
            R
          </div>
        </div>
        
        <!-- Ник пользователя с ссылкой на телеграм -->
        <a 
          :href="`https://t.me/${friend.username || generateUsername(friend)}`" 
          target="_blank"
          rel="noopener noreferrer"
          class="friends-bottom-sheet__username"
        >
          @{{ friend.username || generateUsername(friend) }}
        </a>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
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
  friends: UserOrFriend[];
}

const props = withDefaults(defineProps<Props>(), {
  friends: () => [],
});

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

// Генерация username если его нет
const generateUsername = (user: UserOrFriend): string => {
  // Для друзей есть поле name
  if (isFriend(user)) {
    return user.name.toLowerCase().replace(/\s+/g, '_');
  }
  
  // Для обычных пользователей
  const name = user.first_name?.toLowerCase() || 'user';
  const lastName = user.last_name?.toLowerCase() || '';
  return lastName ? `${name}_${lastName}` : name;
};

// Получение URL аватара
const getAvatarUrl = (user: UserOrFriend): string | undefined => {
  if (isFriend(user)) {
    return user.profile_photo_url || undefined;
  }
  return user.full_avatar_url || user.avatar_url || undefined;
};

// Получение отображаемого имени
const getDisplayName = (user: UserOrFriend): string => {
  if (isFriend(user)) {
    return user.name;
  }
  return `${user.first_name || ''} ${user.last_name || ''}`.trim() || 'User';
};

// Убрано - теперь у всех есть значок R
</script>

<style scoped>
.friends-bottom-sheet {
  background: #FFFEFB;
  width: 100%;
  overflow-y: auto;
  padding: 0 32px;
}

.friends-bottom-sheet__header {
  display: flex;
  justify-content: center;
  margin-bottom: 32px;
}

.friends-bottom-sheet__title {
  font-family: "SF Pro Display", sans-serif;
  font-weight: 600;
  font-size: 20px;
  line-height: 1.35;
  letter-spacing: -0.03em;
  color: #000000;
  margin: 0;
}

.friends-bottom-sheet__list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.friends-bottom-sheet__item {
  display: flex;
  align-items: center;
  gap: 10px;
}

.friends-bottom-sheet__avatar-container {
  position: relative;
  flex-shrink: 0;
}

.friends-bottom-sheet__avatar {
  width: 45px;
  height: 45px;
  border-radius: 50px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

.friends-bottom-sheet__avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.friends-bottom-sheet__avatar-initials {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: "SF Pro Display", sans-serif;
  font-weight: 600;
  font-size: 16px;
  color: #FFFFFF;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.friends-bottom-sheet__repeat-badge {
  position: absolute;
  bottom: -2px;
  right: -2px;
  width: 19px;
  height: 19px;
  background: #FFF1C3;
  border: 0.5px solid rgba(255, 255, 255, 0.73);
  border-radius: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: "SF Pro Display", sans-serif;
  font-weight: 600;
  font-size: 6px;
  line-height: 1.35;
  letter-spacing: -0.03em;
  color: rgba(0, 0, 0, 0.75);
  box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.1);
  z-index: 1;
}

.friends-bottom-sheet__username {
  font-family: "SF Pro Display", sans-serif;
  font-weight: 600;
  font-size: 16px;
  line-height: 1.35;
  letter-spacing: -0.03em;
  color: #000000;
  flex: 1;
  text-decoration: none;
  transition: color 0.2s ease;
}

.friends-bottom-sheet__username:hover {
  color: #0088cc;
  text-decoration: underline;
}
</style>
