<template>
  <Teleport to="body">
    <div v-if="isVisible" class="direct-link-banner">
      <div class="direct-link-banner__content">
        <div class="direct-link-banner__badge">R</div>

        <div class="direct-link-banner__body">
          <div class="direct-link-banner__text">
            <strong class="direct-link-banner__title"> Вы поставили Repeat! </strong>

            <div class="direct-link-banner__description">
              <p class="direct-link-banner__description-strong">Значит это место, куда вам хочется вернуться!</p>
              <p class="direct-link-banner__description-text">
                Пригласите друзей, чтобы делиться рекомендациями и видеть Репиты друг друга во всех приложениях,
                подключённых к платформе Eat.Drink.Repeat.
              </p>
            </div>
          </div>

          <div class="direct-link-banner__actions">
            <a
              v-if="isBotLinkAvailable"
              :href="botLink"
              class="direct-link-banner__button direct-link-banner__button--primary"
            >
              Перейти в бот
            </a>
            <button
              v-else
              type="button"
              class="direct-link-banner__button direct-link-banner__button--primary direct-link-banner__button--disabled"
              disabled
            >
              Перейти в бот
            </button>
            <button
              type="button"
              class="direct-link-banner__button direct-link-banner__button--secondary"
              @click="handleDismissClick"
            >
              Позже
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
interface Props {
  isVisible?: boolean;
  botLink?: string;
  isBotLinkAvailable?: boolean;
}

const { isVisible = false, botLink = '', isBotLinkAvailable = false } = defineProps<Props>();

const emit = defineEmits<{
  close: [];
}>();

const handleDismissClick = () => {
  emit('close');
};
</script>

<style scoped>
.direct-link-banner {
  position: fixed;
  top: 16px;
  right: 16px;
  left: 16px;
  z-index: 70;
  display: flex;
  justify-content: center;
  pointer-events: none;
}

.direct-link-banner__content {
  width: 100%;
  display: flex;
  gap: 14px;
  align-items: center;
  padding: 14px 16px;
  border: 1px solid rgb(0 0 0 / 10%);
  border-radius: 14px;
  background: #fffefb;
  box-shadow: 0 8px 24px rgb(0 0 0 / 8%);
  pointer-events: auto;
}

.direct-link-banner__badge {
  width: 44px;
  height: 44px;
  flex-shrink: 0;
  align-self: center;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgb(255 251 239 / 70%);
  border-radius: 26px;
  background: #fff1c3;
  color: rgb(0 0 0 / 75%);
  font-size: 14px;
  font-weight: 600;
  line-height: 1.35;
}

.direct-link-banner__body {
  display: flex;
  flex: 1;
  flex-direction: column;
  gap: 10px;
}

.direct-link-banner__text {
  display: flex;
  flex-direction: column;
  gap: 11px;
}

.direct-link-banner__title,
.direct-link-banner__description-strong,
.direct-link-banner__description-text {
  margin: 0;
  color: #2b3134;
}

.direct-link-banner__title {
  font-size: 16px;
  font-weight: 500;
  line-height: 20px;
}

.direct-link-banner__title strong {
  font-weight: 700;
}

.direct-link-banner__description {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.direct-link-banner__description-strong,
.direct-link-banner__description-text {
  font-size: 14px;
  line-height: 1.2;
  letter-spacing: -0.28px;
}

.direct-link-banner__description-strong,
.direct-link-banner__description-text strong {
  font-weight: 600;
}

.direct-link-banner__actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.direct-link-banner__button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 8px 12px;
  border: 0;
  border-radius: 20px;
  color: #2b3134;
  font-size: 14px;
  font-weight: 500;
  line-height: 20px;
  text-decoration: none;
  cursor: pointer;
}

.direct-link-banner__button--primary {
  min-width: 134px;
  background: #eee;
}

.direct-link-banner__button--secondary {
  min-width: 59px;
  background: transparent;
}

.direct-link-banner__button--disabled {
  opacity: 0.55;
  cursor: default;
}

@media (max-width: 560px) {
  .direct-link-banner__content {
    align-items: flex-start;
  }

  .direct-link-banner__actions {
    flex-wrap: wrap;
  }
}
</style>
