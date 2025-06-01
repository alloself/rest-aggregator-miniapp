// Site (Публичный сайт) - Main TypeScript Entry Point

// Vue 3 setup for Site
import { createApp } from 'vue';

console.log('Site Vue app initializing...');

// Site-specific initialization
const app = createApp({
  // Site Vue app configuration
});

// Mount when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  const mountPoint = document.getElementById('site-app');
  if (mountPoint) {
    app.mount(mountPoint);
    console.log('Site Vue app mounted');
  }
}); 