import { createApp } from 'vue';
import { createPinia } from 'pinia';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';

import SiteApp from './App.vue';
import router from './router';
import { initClient } from '../shared/api/axios';
import '@site/ts/shared/lib/dayjs';

async function initSiteApp() {
  const app = createApp(SiteApp);

  const pinia = createPinia();
  app.use(pinia);

  app.use(router);

  app.use(PrimeVue, {
    theme: {
      preset: Aura,
      options: {
        prefix: 'p',
        darkModeSelector: '.dark',
        cssLayer: {
          name: 'primevue',
          order: 'theme, base, primevue',
        },
      },
    },
  });

  await initClient();

    // Mark Telegram WebApp as ready early (works without any API changes)
    try {
      const tg = window.Telegram?.WebApp;
      tg?.ready?.();
    } catch {
      // ignore
    }

    const mountPoint = document.getElementById('site-app');
    if (mountPoint) {
      app.mount(mountPoint);
      mountPoint.classList.add('mounted');
    }
}

document.addEventListener('DOMContentLoaded', initSiteApp);
