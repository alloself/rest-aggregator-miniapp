import {
  LEGAL_PERSONAL_DATA_PDF_KEY,
  LEGAL_PRIVACY_PDF_KEY,
  isLegalDocumentFileKey,
} from '@/shared/constants/legalDocuments';
import type { FileResourceData } from '@/shared/types/resources';

const REQUIRED_LEGAL_DOCUMENT_KEYS = [
  LEGAL_PERSONAL_DATA_PDF_KEY,
  LEGAL_PRIVACY_PDF_KEY,
] as const;

export function hasRequiredRestaurantLegalDocuments(
  files?: FileResourceData[] | null,
): boolean {
  const availableKeys = new Set(
    (files ?? [])
      .map((file) => file.pivot?.key)
      .filter(isLegalDocumentFileKey),
  );

  return REQUIRED_LEGAL_DOCUMENT_KEYS.every((key) => availableKeys.has(key));
}
