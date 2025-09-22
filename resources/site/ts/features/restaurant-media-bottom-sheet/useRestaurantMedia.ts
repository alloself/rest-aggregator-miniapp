import { useBottomSheet } from '../../shared/lib/composables/useBottomSheet';
import RestaurantMediaBottomSheet from './RestaurantMediaBottomSheet.vue';
import type { File } from '@/shared';

/**
 * Композабл для удобного открытия bottomsheet с медиа контентом ресторана
 */
export const useRestaurantMedia = () => {
  const { open } = useBottomSheet();

  /**
   * Открыть галерею фотографий ресторана
   */
  const openPhotos = (
    restaurantName: string,
    photos: File[],
    initialIndex: number = 0
  ): string => {
    return open(
      RestaurantMediaBottomSheet,
      {
        type: 'photos',
        restaurantName,
        files: photos,
        initialIndex,
        showFileInfo: false,
      },
      {
        height: 90,
        class: 'restaurant-media-sheet',
        showHandle: true,
        closableByBackdrop: true,
        closableBySwipe: true,
        bottomGap: true,
      }
    );
  };

  /**
   * Открыть меню ресторана
   */
  const openMenu = (
    restaurantName: string,
    menuFiles: File[],
    initialIndex: number = 0
  ): string => {
    return open(
      RestaurantMediaBottomSheet,
      {
        type: 'menu',
        restaurantName,
        files: menuFiles,
        initialIndex,
        showFileInfo: true,
      },
      {
        height: 90,
        class: 'restaurant-media-sheet',
        showHandle: true,
        closableByBackdrop: true,
        closableBySwipe: true,
      }
    );
  };

  /**
   * Открыть карту бара ресторана
   */
  const openBar = (
    restaurantName: string,
    barFiles: File[],
    initialIndex: number = 0
  ): string => {
    return open(
      RestaurantMediaBottomSheet,
      {
        type: 'bar',
        restaurantName,
        files: barFiles,
        initialIndex,
        showFileInfo: true,
      },
      {
        height: 90,
        class: 'restaurant-media-sheet',
        showHandle: true,
        closableByBackdrop: true,
        closableBySwipe: true,
      }
    );
  };

  /**
   * Универсальный метод для открытия любого типа медиа
   */
  const openMedia = (
    type: 'photos' | 'menu' | 'bar',
    restaurantName: string,
    files: File[],
    initialIndex: number = 0,
    showFileInfo: boolean = false
  ): string => {
    return open(
      RestaurantMediaBottomSheet,
      {
        type,
        restaurantName,
        files,
        initialIndex,
        showFileInfo,
      },
      {
        height: 90,
        class: 'restaurant-media-sheet',
        showHandle: true,
        closableByBackdrop: true,
        closableBySwipe: true,
      }
    );
  };

  return {
    openPhotos,
    openMenu,
    openBar,
    openMedia,
  };
};
