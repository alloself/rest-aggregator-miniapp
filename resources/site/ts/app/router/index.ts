import { createRouter, createWebHistory } from 'vue-router';

// Simple routes for site
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
    path: '/restaurant/:slug/event/:eventSlug',
    name: 'event',
    component: () => import('../../pages/EventPage.vue'),
    props: true,
  },
];

const router = createRouter({
  history: createWebHistory('/'),
  routes,
});

export default router;
