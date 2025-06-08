// Site (Публичный сайт) - Main TypeScript Entry Point
// Интеграция с Laravel Blade + Telegram Mini App

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import PrimeVue from 'primevue/config'
import Aura from '@primeuix/themes/aura'

// Vue Router
import router from './router'

// PrimeVue components
import Button from 'primevue/button'
import Card from 'primevue/card'
import Image from 'primevue/image'
import Avatar from 'primevue/avatar'
import Badge from 'primevue/badge'
import Tag from 'primevue/tag'
import Carousel from 'primevue/carousel'

// Site App component
import SiteApp from './SiteApp.vue'

// Telegram Web App & Laravel integration types
declare global {
  interface Window {
    TelegramWebApp: any;
    SiteConfig: {
      apiBaseUrl: string;
      environment: string;
      telegramBotUsername: string;
      features: {
        favorites: boolean;
        ratings: boolean;
        reservations: boolean;
      };
    };
    hideAppLoading: () => void;
  }
}

// Site Vue app initializing

// Create Vue app
const app = createApp(SiteApp)

// Pinia store
const pinia = createPinia()
app.use(pinia)

// Vue Router
app.use(router)

// PrimeVue configuration для Telegram Mini App
app.use(PrimeVue, {
  theme: {
    preset: Aura,
    options: {
      prefix: 'p',
      darkModeSelector: '.dark',
      cssLayer: false
    }
  }
})

// Register PrimeVue components globally
app.component('Button', Button)
app.component('Card', Card)
app.component('Image', Image)
app.component('Avatar', Avatar)
app.component('Badge', Badge)
app.component('Tag', Tag)
app.component('Carousel', Carousel)

// Global provides для доступа к Laravel config и Telegram
app.provide('siteConfig', window.SiteConfig || {
  apiBaseUrl: '/api/v1',
  environment: 'local',
  telegramBotUsername: '',
  features: {
    favorites: true,
    ratings: true,
    reservations: true
  }
})
app.provide('telegramWebApp', window.TelegramWebApp || null)

// Mount when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  const mountPoint = document.getElementById('site-app')
  if (mountPoint) {
    app.mount(mountPoint)
    
    // Уведомляем Blade template о готовности Vue app
    mountPoint.classList.add('mounted')
    if (window.hideAppLoading) {
      window.hideAppLoading()
    }
    
    // Site Vue app mounted successfully
  }
}) 