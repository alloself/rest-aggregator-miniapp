import type { File } from './models';

/**
 * Интерфейс для MediaViewer компонента
 */
export interface MediaViewerProps {
  /** Массив медиа файлов для просмотра */
  mediaFiles: File[];
  /** Начальный индекс для отображения */
  initialIndex?: number;
}

/**
 * События MediaViewer компонента
 */
export interface MediaViewerEvents {
  /** Когда изменился текущий файл */
  change: [media: File, index: number];
}

/**
 * Методы MediaViewer компонента
 */
export interface MediaViewerMethods {
  setCurrentIndex: (index: number) => void;
  goToPrevious: () => void;
  goToNext: () => void;
  currentIndex: () => number;
  currentMedia: () => File | null;
}

/**
 * Поддерживаемые типы медиа файлов
 */
export type MediaType = 'image' | 'pdf' | 'document' | 'archive' | 'other';

/**
 * Конфигурация иконок для типов файлов
 */
export interface FileIconConfig {
  [extension: string]: string;
}

/**
 * Утилиты для работы с медиа файлами
 */
export interface MediaUtils {
  isImage: (file: File) => boolean;
  isPdf: (file: File) => boolean;
  isIframeCompatible: (file: File) => boolean;
  getMediaType: (file: File) => MediaType;
  getFileIcon: (file: File) => string;
}
