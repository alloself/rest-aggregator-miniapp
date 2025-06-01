// Account (ЛК ресторанов) - Main TypeScript Entry Point
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { useAuthStore } from '../../shared/stores/auth';

// Import shared composables for easy access
import { useAuth, useRestaurant } from '../../shared/composables';

// Создаем базовый компонент приложения
const App = {
  setup() {
    const authStore = useAuthStore();
    
    // Инициализируем аутентификацию при запуске
    authStore.initAuth();
    
    return {
      user: authStore.user,
      isAuthenticated: authStore.isAuthenticated,
      isLoading: authStore.isLoading,
    };
  },
  
  template: `
    <div id="account-app" class="min-h-screen bg-gray-50">
      <div v-if="isLoading" class="flex items-center justify-center min-h-screen">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>
      
      <div v-else-if="!isAuthenticated" class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full">
          <h1 class="text-2xl font-bold text-center mb-6">Вход в личный кабинет</h1>
          <login-form />
        </div>
      </div>
      
      <div v-else>
        <header class="bg-white shadow-sm border-b">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
              <h1 class="text-xl font-semibold text-gray-900">
                Личный кабинет - {{ user?.restaurant?.name || user?.name }}
              </h1>
              <user-menu />
            </div>
          </div>
        </header>
        
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
          <div class="px-4 py-6 sm:px-0">
            <dashboard-content />
          </div>
        </main>
      </div>
    </div>
  `,
};

// Создаем приложение
const app = createApp(App);
const pinia = createPinia();

app.use(pinia);

console.log('Account Vue app initializing...');

// Mount when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  const mountPoint = document.getElementById('account-app');
  if (mountPoint) {
    app.mount(mountPoint);
    console.log('Account Vue app mounted');
  }
}); 