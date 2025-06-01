// Admin (Административная панель) - Main TypeScript Entry Point

// Vue 3 setup for Admin
import { createApp } from 'vue';

console.log('Admin Vue app initializing...');

// Admin-specific initialization
const app = createApp({
  // Admin Vue app configuration
});

// Mount when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  const mountPoint = document.getElementById('admin-app');
  if (mountPoint) {
    app.mount(mountPoint);
    console.log('Admin Vue app mounted');
  }
}); 