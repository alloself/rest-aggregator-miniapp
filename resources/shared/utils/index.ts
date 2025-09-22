import { filter, groupBy, meanBy, orderBy } from 'lodash';
import type { Dish, Event } from '../types';

export const groupDishesByCategory = (dishes: Dish[]) => groupBy(dishes, 'category.name');
export const sortEventsByDate = (events: Event[]) => orderBy(events, ['start_date'], ['asc']);
export const getAvailableDishes = (dishes: Dish[]) => filter(dishes, { is_available: true });
export const calculateAveragePrice = (dishes: Dish[]) => meanBy(dishes, 'price');

// Медиа утилиты
export * from './media';
