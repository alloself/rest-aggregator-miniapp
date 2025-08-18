import { createApp } from 'vue';
import { createPinia } from 'pinia';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';

import SiteApp from './App.vue';
import router from './router';
import { initClient } from '../shared/api/axios';

async function initSiteApp() {
  try {
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
          cssLayer: false,
        },
      },
    });

    await initClient();

    const mountPoint = document.getElementById('site-app');
    if (mountPoint) {
      app.mount(mountPoint);
      mountPoint.classList.add('mounted');
    }
  } catch (error) {
    console.error('Site App: initialization failed', error);
  }
}

document.addEventListener('DOMContentLoaded', initSiteApp);
