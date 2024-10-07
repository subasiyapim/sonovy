<?php

namespace App\Enums;

use ReflectionClass;

enum PlatformTypeEnum: int
{
    case DOWNLOADABLE = 1;
    case SOCIAL_MEDIA = 2;
    case STREAMING = 3;
    case DSP_INTEGRATION = 4;


    public static function getTitles(): array
    {
        return array_map(
            fn(PlatformTypeEnum $enum) => $enum->title(),
            array_combine(array_column(self::cases(), 'value'), self::cases())
        );
    }

    public static function getTitlesFromInputFormat(): array
    {
        return array_map(
            fn(PlatformTypeEnum $enum) => ['label' => $enum->title(), 'value' => $enum->value],
            array_combine(array_column(self::cases(), 'value'), self::cases())
        );
    }

    public static function getKeys(): array
    {
        return array_keys(self::cases());
    }


    public function title()
    {
        return match ($this) {
            self::SOCIAL_MEDIA => __('panel.platform.type_social_media'),
            self::DOWNLOADABLE => __('panel.platform.type_downloadable'),
            self::STREAMING => __('panel.platform.type_streaming'),
            self::DSP_INTEGRATION => __('panel.platform.type_dsp_integration'),
        };
    }
}
