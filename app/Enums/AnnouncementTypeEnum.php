<?php

namespace App\Enums;

use ReflectionClass;

enum AnnouncementTypeEnum: string
{
    case SITE = 'site';
    case MAINTENANCE = 'maintenance';
    case EMAIL = 'email';  // Fixed typo here
    case SMS = 'sms';

    public static function getTitles(): array
    {
        return array_map(
            fn(AnnouncementTypeEnum $enum) => $enum->title(),
            array_combine(array_column(self::cases(), 'value'), self::cases())
        );
    }

    public static function getKeys(): array
    {
        return array_map(fn($case) => $case->name, self::cases());
    }

    public static function getValues($type = 'array'): mixed
    {
        $array = array_map(fn($case) => $case->value, self::cases());
        return $type !== 'array' ? implode(',', $array) : $array;
    }

    public static function getKeyTitleArray(): array
    {
        return array_map(
            fn(AnnouncementTypeEnum $enum) => [
                'key' => $enum->value,
                'value' => $enum->title()
            ],
            self::cases()
        );
    }

    public function title()
    {
        return match ($this) {
            self::SITE => __('panel.announcement.form.type_site'),
            self::MAINTENANCE => __('panel.announcement.form.type_maintenance'),
            self::EMAIL => __('panel.announcement.form.type_email'),  // Fixed typo here
            self::SMS => __('panel.announcement.form.type_sms'),
        };
    }
}