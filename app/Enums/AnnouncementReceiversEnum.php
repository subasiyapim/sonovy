<?php

namespace App\Enums;

use ReflectionClass;

enum AnnouncementReceiversEnum: string
{
    case ALL = 'all';
    case SELECTED = 'selected';
    case ALL_BUT = 'all_but';


    public static function getTitles(): array
    {
        return array_map(
            fn(AnnouncementReceiversEnum $enum) => $enum->title(),
            array_combine(array_column(self::cases(), 'value'), self::cases())
        );
    }

    public static function getKeys(): array
    {
        return array_keys(self::cases());
    }


    public static function getKeyTitleArray(): array
    {
        return array_map(
            fn(AnnouncementReceiversEnum $enum) => [
                'key' => $enum->value,
                'value' => $enum->title()
            ],
            self::cases()
        );
    }


    public function title()
    {
        return match ($this) {
            self::ALL => __('control.announcement.form.receivers_type_all'),
            self::SELECTED => __('control.announcement.form.receivers_type_selected'),
            self::ALL_BUT => __('control.announcement.form.receivers_type_all_but'),
        };
    }
}
