<template>
  <button 
    class="like-button"
    :class="{ 'like-button--active': isLiked }"
    :disabled="disabled"
    @click="$emit('click')"
  >
    <div class="like-button__icon">
      <img 
        src="/svg/repeat.svg" 
        alt="Repeat"
        class="like-button__svg"
        :class="{ 'like-button__svg--active': isLiked }"
      />
    </div>
    <span v-if="friendsCount > 0" class="like-button__count">+{{ friendsCount }}</span>
  </button>
</template>

<script setup lang="ts">
interface Props {
  isLiked?: boolean;
  friendsCount?: number;
  disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  isLiked: false,
  friendsCount: 0,
  disabled: false,
});

const emit = defineEmits<{
  click: [];
}>();
</script>

<style scoped>
.like-button {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  background: rgba(0, 0, 0, 0.2);
  border: none;
  border-radius: 20px;
  backdrop-filter: blur(4px);
  cursor: pointer;
  transition: all 0.2s ease;
  min-width: 85px;
  justify-content: flex-start;
  width: fit-content;
}

.like-button:hover:not(:disabled) {
  background: rgba(0, 0, 0, 0.3);
}

.like-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.like-button__icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  flex-shrink: 0;
}

.like-button__svg {
  width: 32px;
  height: 32px;
  transition: all 0.2s ease;
  filter: brightness(1);
}

.like-button__svg--active {
  filter: brightness(1.2) saturate(1.3);
  transform: scale(1.05);
}

.like-button__count {
  font-family: Inter, sans-serif;
  font-weight: 600;
  font-size: 14px;
  line-height: 1.35;
  letter-spacing: -0.03em;
  color: #FFFFFF;
  flex-shrink: 0;
}
</style>
