import { createApp } from 'vue';

import App from './app/App.vue';

import { setupPlugins } from './shared/plugins';
import { useAuthStore } from '@/shared';
import { initClient } from './shared/api/axios';
import router from './app/router';
import { storeToRefs } from 'pinia';

async function initAccountApp() {
  try {
    const app = createApp(App);

    setupPlugins(app);

    await initClient();

    const currentPath = window.location.pathname;
    const loginUrl = router.resolve({ name: 'login' }).href;
    const isLoginPage = currentPath === loginUrl;
    const isLegalPage = currentPath.startsWith('/account/legal/');
    const isPublicPage = isLoginPage || isLegalPage;

    const authStore = useAuthStore();

    const { user } = storeToRefs(authStore);
    if (!isPublicPage) {
      try {
        await authStore.fetchUser();
      } catch (error) {
        window.location.href = loginUrl;
      }
    }

    if (!user.value && !isPublicPage) {
      window.location.href = loginUrl;
      return;
    }

    app.mount('#account-app');
  } catch (error) {
    console.error('Account App: initialization failed', error);
  }
}
document.addEventListener('DOMContentLoaded', initAccountApp);
