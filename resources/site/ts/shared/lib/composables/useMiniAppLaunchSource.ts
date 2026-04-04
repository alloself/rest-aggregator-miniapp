import { computed, onMounted, ref } from 'vue';

const DIRECT_LINK_CHAT_TYPES = new Set([
  'sender',
  'private',
  'group',
  'supergroup',
  'channel',
]);

export function useMiniAppLaunchSource() {
  const isLaunchSourceResolved = ref(false);
  const isOutsideTelegram = ref(false);
  const isTelegramDirectLinkLaunch = ref(false);

  onMounted(() => {
    const telegramWebApp = window.Telegram?.WebApp;
    const initData = telegramWebApp?.initData ?? '';
    const initDataUnsafe = telegramWebApp?.initDataUnsafe;
    const chatType = initDataUnsafe?.chat_type;
    const chatInstance = initDataUnsafe?.chat_instance;

    isOutsideTelegram.value = initData.length === 0;
    isTelegramDirectLinkLaunch.value = initData.length > 0 && (
      (typeof chatType === 'string' && DIRECT_LINK_CHAT_TYPES.has(chatType))
      || typeof chatInstance === 'string'
    );
    isLaunchSourceResolved.value = true;
  });

  const shouldShowDirectLinkBanner = computed(() => (
    isLaunchSourceResolved.value
    && (isOutsideTelegram.value || isTelegramDirectLinkLaunch.value)
  ));

  return {
    isLaunchSourceResolved,
    isOutsideTelegram,
    isTelegramDirectLinkLaunch,
    shouldShowDirectLinkBanner,
  };
}
