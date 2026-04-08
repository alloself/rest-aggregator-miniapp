export const LEGAL_PRIVACY_PDF_KEY = 'legal_privacy_pdf';
export const LEGAL_PERSONAL_DATA_PDF_KEY = 'legal_personal_data_pdf';

export const LEGAL_DOCUMENT_FILE_KEYS = [
  LEGAL_PRIVACY_PDF_KEY,
  LEGAL_PERSONAL_DATA_PDF_KEY,
] as const;

export type LegalDocumentFileKey =
  typeof LEGAL_DOCUMENT_FILE_KEYS[number];

export interface LegalDocumentFileDefinition {
  key: LegalDocumentFileKey;
  title: string;
  description: string;
}

export const LEGAL_DOCUMENT_FILE_DEFINITIONS: LegalDocumentFileDefinition[] = [
  {
    key: LEGAL_PRIVACY_PDF_KEY,
    title: 'Политика конфиденциальности',
    description: 'Показывается на странице /legal/privacy для выбранного ресторана.',
  },
  {
    key: LEGAL_PERSONAL_DATA_PDF_KEY,
    title: 'Поручение на обработку персональных данных',
    description: 'Показывается на странице /legal/personal-data для выбранного ресторана.',
  },
];

export function isLegalDocumentFileKey(
  key: string | null | undefined,
): key is LegalDocumentFileKey {
  if (!key) {
    return false;
  }

  return LEGAL_DOCUMENT_FILE_KEYS.includes(key as LegalDocumentFileKey);
}
