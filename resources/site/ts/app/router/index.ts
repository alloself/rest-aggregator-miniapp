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
  {
    path: '/legal/personal-data',
    name: 'site-legal-personal-data',
    component: () => import('../../pages/legal/PersonalData.vue'),
  },
  {
    path: '/legal/privacy',
    name: 'site-legal-privacy',
    component: () => import('../../pages/legal/Privacy.vue'),
  },
];

const router = createRouter({
  history: createWebHistory('/'),
  routes,
});

export default router;
