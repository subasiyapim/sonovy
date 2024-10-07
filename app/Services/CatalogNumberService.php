<?php

namespace App\Services;

use App\Models\CatalogNumber;
use App\Models\Setting;

class CatalogNumberService
{
    public static function make()
    {
        $prefix = Setting::where('key', 'catalog_number_prefix')->first()?->value ?? 'MD';

        $lastCatalogNumber = CatalogNumber::where('catalog_number', 'LIKE', "$prefix-%")->orderBy('catalog_number', 'desc')->first();

        // Yeni numara oluştur
        if ($lastCatalogNumber) {
            // Mevcut en yüksek numarayı artırın
            $lastNumber = (int)str_replace($prefix . '-', '', $lastCatalogNumber->catalog_number);
            $newNumber = $lastNumber + 1;
        } else {
            // İlk numarayı oluştur
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 7, '0', STR_PAD_LEFT);

    }
}
