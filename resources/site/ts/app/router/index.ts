import { createRouter, createWebHistory } from 'vue-router';

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('../../pages/RestaurantPage.vue'),
  },
  {
    path: '/restaurant/:slug',
    name: 'restaurant',
    component: () => import('../../pages/RestaurantPage.vue'),
    props: true,
  },

];

const router = createRouter({
  history: createWebHistory('/'),
  routes,
});

export default router;
