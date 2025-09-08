import type { File, MediaType, FileIconConfig } from '@/shared';

/**
 * Конфигурация иконок для различных типов файлов
 */
export const fileIconConfig: FileIconConfig = {
  // Документы
  pdf: 'mdi:file-pdf-box',
  doc: 'mdi:file-document-outline',
  docx: 'mdi:file-document-outline',
  odt: 'mdi:file-document-outline',
  rtf: 'mdi:file-document-outline',
  
  // Таблицы
  xls: 'mdi:file-excel-outline',
  xlsx: 'mdi:file-excel-outline',
  ods: 'mdi:file-excel-outline',
  csv: 'mdi:file-delimited-outline',
  
  // Презентации
  ppt: 'mdi:file-powerpoint-box',
  pptx: 'mdi:file-powerpoint-box',
  odp: 'mdi:file-powerpoint-box',
  
  // Архивы
  zip: 'mdi:archive',
  rar: 'mdi:archive',
  '7z': 'mdi:archive',
  tar: 'mdi:archive',
  gz: 'mdi:archive',
  
  // Изображения (для полноты, хотя они отображаются как превью)
  jpg: 'mdi:file-image-outline',
  jpeg: 'mdi:file-image-outline',
  png: 'mdi:file-image-outline',
  gif: 'mdi:file-image-outline',
  webp: 'mdi:file-image-outline',
  svg: 'mdi:file-image-outline',
  bmp: 'mdi:file-image-outline',
  
  // Видео
  mp4: 'mdi:file-video-outline',
  avi: 'mdi:file-video-outline',
  mov: 'mdi:file-video-outline',
  wmv: 'mdi:file-video-outline',
  webm: 'mdi:file-video-outline',
  
  // Аудио
  mp3: 'mdi:file-music-outline',
  wav: 'mdi:file-music-outline',
  flac: 'mdi:file-music-outline',
  ogg: 'mdi:file-music-outline',
  
  // Текстовые файлы
  txt: 'mdi:file-document-outline',
  md: 'mdi:file-document-outline',
  json: 'mdi:code-json',
  xml: 'mdi:code-tags',
  html: 'mdi:language-html5',
  css: 'mdi:language-css3',
  js: 'mdi:language-javascript',
  ts: 'mdi:language-typescript',
  
  // По умолчанию
  default: 'mdi:file-outline'
};

/**
 * Расширения изображений, которые можно отображать как превью
 */
export const imageExtensions = [
  'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'
];

/**
 * Расширения видео файлов
 */
export const videoExtensions = [
  'mp4', 'avi', 'mov', 'wmv', 'webm', 'mkv', 'flv', '3gp'
];

/**
 * Расширения аудио файлов  
 */
export const audioExtensions = [
  'mp3', 'wav', 'flac', 'ogg', 'aac', 'wma', 'm4a'
];

/**
 * Расширения документов
 */
export const documentExtensions = [
  'pdf', 'doc', 'docx', 'odt', 'rtf', 'txt', 'md'
];

/**
 * Расширения архивов
 */
export const archiveExtensions = [
  'zip', 'rar', '7z', 'tar', 'gz', 'bz2'
];

/**
 * Расширения файлов, которые можно отображать в iframe
 */
export const iframeCompatibleExtensions = [
  'pdf', 'html', 'htm', 'txt', 'md', 'xml', 'json'
];

/**
 * Проверяет, является ли файл изображением
 */
export function isImage(file: File): boolean {
  return imageExtensions.includes(file.extension.toLowerCase());
}

/**
 * Проверяет, является ли файл PDF документом
 */
export function isPdf(file: File): boolean {
  return file.extension.toLowerCase() === 'pdf';
}

/**
 * Проверяет, является ли файл видео
 */
export function isVideo(file: File): boolean {
  return videoExtensions.includes(file.extension.toLowerCase());
}

/**
 * Проверяет, является ли файл аудио
 */
export function isAudio(file: File): boolean {
  return audioExtensions.includes(file.extension.toLowerCase());
}

/**
 * Проверяет, можно ли отобразить файл в iframe
 */
export function isIframeCompatible(file: File): boolean {
  return iframeCompatibleExtensions.includes(file.extension.toLowerCase());
}

/**
 * Проверяет, является ли файл документом
 */
export function isDocument(file: File): boolean {
  return documentExtensions.includes(file.extension.toLowerCase());
}

/**
 * Проверяет, является ли файл архивом
 */
export function isArchive(file: File): boolean {
  return archiveExtensions.includes(file.extension.toLowerCase());
}

/**
 * Определяет тип медиа файла
 */
export function getMediaType(file: File): MediaType {
  const extension = file.extension.toLowerCase();
  
  if (imageExtensions.includes(extension)) {
    return 'image';
  } else if (extension === 'pdf') {
    return 'pdf';
  } else if (documentExtensions.includes(extension)) {
    return 'document';
  } else if (archiveExtensions.includes(extension)) {
    return 'archive';
  } else {
    return 'other';
  }
}

/**
 * Возвращает название иконки для файла
 */
export function getFileIcon(file: File): string {
  const extension = file.extension.toLowerCase();
  return fileIconConfig[extension] || fileIconConfig.default;
}

/**
 * Форматирует размер файла в удобочитаемый формат
 */
export function formatFileSize(bytes: number): string {
  if (bytes === 0) return '0 Bytes';
  
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Проверяет, поддерживается ли превью файла в браузере
 */
export function canPreview(file: File): boolean {
  return isImage(file) || isPdf(file);
}

/**
 * Генерирует URL для превью файла (если поддерживается)
 */
export function getPreviewUrl(file: File): string | null {
  if (canPreview(file)) {
    return file.url;
  }
  return null;
}

/**
 * Возвращает MIME тип файла по расширению
 */
export function getMimeType(file: File): string {
  const extension = file.extension.toLowerCase();
  
  const mimeTypes: Record<string, string> = {
    // Изображения
    jpg: 'image/jpeg',
    jpeg: 'image/jpeg',
    png: 'image/png',
    gif: 'image/gif',
    webp: 'image/webp',
    svg: 'image/svg+xml',
    bmp: 'image/bmp',
    
    // Документы
    pdf: 'application/pdf',
    doc: 'application/msword',
    docx: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    
    // И так далее...
  };
  
  return mimeTypes[extension] || 'application/octet-stream';
}
