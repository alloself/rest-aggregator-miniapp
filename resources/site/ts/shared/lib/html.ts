import DOMPurify from 'dompurify';

/**
 * Безопасная очистка HTML контента от потенциально опасных элементов
 * @param html - HTML строка для очистки
 * @returns Очищенная HTML строка
 */
export function sanitizeHtml(html: string): string {
  if (!html) return '';
  
  const allowedTags = [
    'b',
    'strong',
    'i',
    'em',
    'u',
    's',
    'p',
    'br',
    'ul',
    'ol',
    'li',
    'h1',
    'h2',
    'h3',
    'h4',
    'h5',
    'h6',
    'span',
  ];
  
  const sanitized = DOMPurify.sanitize(html, {
    ALLOWED_TAGS: allowedTags,
    ALLOWED_ATTR: [],
    USE_PROFILES: { html: true },
  });
  
  // Убираем неразрывные пробелы
  return sanitized.replace(/&nbsp;|&#160;/g, ' ').replace(/\u00A0/g, ' ');
}
