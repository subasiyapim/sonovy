<?php

namespace App\Services;

use App\Enums\ProductStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Models\Setting;
use App\Models\Song;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Reverb\Loggers\Log;

class ISRCServices
{
    public static function make($type, $tenant = null)
    {
        if ($tenant) {
            tenancy()->initialize($tenant);
            Log::info('Tenant initialized: '.$tenant->domain);
        }

        // Varsayılan değerleri ayarla
        $country_code = Setting::where('key', 'isrc_country_code')->first()->value ?? 'TR';
        $year_code = Setting::where('key', 'isrc_year')->first()->value ?? Carbon::now()->format('y');
        $registration_code = Setting::where('key', 'isrc_registration_code')->first()->value ?? '001';

        // Kod aralığını belirle
        if ($type == ProductTypeEnum::SOUND->value || $type == ProductTypeEnum::RINGTONE->value) {
            $min_code = 1;
            $max_code = 49999;
        } elseif ($type == ProductTypeEnum::VIDEO->value || $type == ProductTypeEnum::APPLE_VIDEO->value) {
            $min_code = 50000;
            $max_code = 99999;
        } else {
            return false; // Geçersiz bir tip için false döndür
        }

        // Hatalı ISRC kodlarını kontrol et ve sil
        Song::where('isrc', '=', '0')
            ->orWhereNull('isrc')
            ->delete(); // Hatalı olanları sil

        // Mevcut ISRC kodlarını al
        $existing_isrcs = Song::where('isrc', 'like', "$country_code-$registration_code-$year_code-%")
            ->whereNotNull('isrc')
            ->whereRaw('CAST(SUBSTRING_INDEX(isrc, "-", -1) AS UNSIGNED) BETWEEN ? AND ?', [$min_code, $max_code])
            ->orderByRaw('CAST(SUBSTRING_INDEX(isrc, "-", -1) AS UNSIGNED) ASC')
            ->pluck('isrc')
            ->toArray();

        // Yeni ISRC kodunu belirle
        $definition_code = $min_code;
        foreach ($existing_isrcs as $isrc) {
            $isrc_parts = explode('-', $isrc);
            if (isset($isrc_parts[3]) && is_numeric($isrc_parts[3])) {
                $current_code = intval($isrc_parts[3]);

                if ($current_code >= $definition_code) {
                    $definition_code = $current_code + 1;
                }

                // Eğer aralık dışına çıkarsa, döngüden çık
                if ($definition_code > $max_code) {
                    Log::error("ISRC code exceeded defined range: $definition_code");
                    return false;
                }
            }
        }

        // Kod sınırlarının dışında kalması durumunda false döndür
        if ($definition_code < $min_code || $definition_code > $max_code) {
            Log::error("Invalid ISRC definition code: $definition_code");
            return false;
        }

        // ISRC kodunu oluştur
        $definition_code = str_pad($definition_code, 5, '0', STR_PAD_LEFT);
        $new_isrc = "$country_code-$registration_code-$year_code-$definition_code";

        Log::info("Generated ISRC Code: $new_isrc");

        return $new_isrc;
    }


    public static function search($search): mixed
    {
        return Song::whereNotNull('isrc')
            ->where('isrc', 'like', "%$search%")
            ->get();
    }
}
