<?php

namespace App\Traits;

trait GTINTrait
{
    private static function calculateCheckDigit($gtin): int
    {
        $length = strlen($gtin);
        $sum = 0;

        // GS1 GTIN uzunluğuna göre son karakter kontrol rakamı olacak şekilde döngü kuruyoruz
        for ($i = $length - 1; $i >= 0; $i--) {
            $number = (int)$gtin[$i];
            // Sağdan sola doğru, çift indeksler 3 ile çarpılır
            $sum += ($length % 2 == $i % 2) ? $number * 3 : $number;
        }

        // Toplamı 10'a bölümünden kalanı bul
        $remainder = $sum % 10;

        // Eğer kalan 0 ise kontrol rakamı 0, değilse 10 - kalan kontrol rakamı olur
        $checkDigit = ($remainder == 0) ? 0 : 10 - $remainder;

        return $checkDigit;
    }

    public static function generateGTIN($companyPrefix, $productNumber): string
    {
        // Şirket öneki ve ürün numarası birleştirilir
        $baseGTIN = $companyPrefix . $productNumber;

        // Kontrol rakamı hesaplanır
        $checkDigit = self::calculateCheckDigit($baseGTIN);

        // Kontrol rakamı eklenerek tam GTIN elde edilir
        $fullGTIN = $baseGTIN . $checkDigit;

        return $fullGTIN;
    }


    public static function validateGTIN($gtin): bool
    {
        // GTIN uzunluğu 8-14 arasında olmalıdır
        if (strlen($gtin) < 8 || strlen($gtin) > 14) {
            return false;
        }

        // GTIN sadece rakamlardan oluşmalıdır
        if (!ctype_digit($gtin)) {
            return false;
        }

        // GTIN'in kontrol rakamı hesaplanır
        $checkDigit = self::calculateCheckDigit(substr($gtin, 0, -1));

        // GTIN'in son karakteri kontrol rakamı ile eşleşmeli
        return $checkDigit == (int)$gtin[strlen($gtin) - 1];
    }
    
}
