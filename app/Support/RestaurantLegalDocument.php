<?php

namespace App\Support;

final class RestaurantLegalDocument
{
    public const PRIVACY_PDF_KEY = 'legal_privacy_pdf';
    public const PERSONAL_DATA_PDF_KEY = 'legal_personal_data_pdf';

    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return [
            self::PRIVACY_PDF_KEY,
            self::PERSONAL_DATA_PDF_KEY,
        ];
    }

    public static function isLegalFileKey(?string $key): bool
    {
        if ($key === null || $key === '') {
            return false;
        }

        return in_array($key, self::keys(), true);
    }
}
