<?php

namespace App\Enums;

use ReflectionClass;

enum VideoTypeEnum: int
{
    case APPLE = 1;
    case MUSIC = 2;

    // Bu statik fonksiyon, enum değerlerinin başlıklarını döner (title fonksiyonu ile belirlenen)

    /**
     * Returns an array of titles corresponding to each enum value.
     *
     * @return array An array where keys are enum values, and values are the respective titles.
     */
    public static function getTitles(): array
    {
        return array_map(
            fn(VideoTypeEnum $enum) => $enum->title(),
            // Her enum değerinin başlığını almak için title fonksiyonu çağrılır
            array_combine(array_column(self::cases(), 'value'), self::cases()) // Değerleri enum nesneleri ile eşler
        );
    }

    // Bu statik fonksiyon, enum değerlendirinin anahtarlarını (isimlerini) döner
    public static function getKeys(): array
    {
        return array_column(self::cases(), 'name'); // Tüm enum değerlerinin isimlerini döner
    }

    // Bu fonksiyon, enum değerlerinin başlıklarını döner
    public function title()
    {
        return match ($this) {
            self::APPLE => 'Apple Video', // Enum değeri APPLE ise 'Apple Video' döner
            self::MUSIC => 'Muzik Video' // Enum değeri MUSIC ise 'Muzik Video' döner
        };
    }
}
