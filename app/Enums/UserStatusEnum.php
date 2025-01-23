<?php

namespace App\Enums;

use ReflectionClass;

enum UserStatusEnum: int
{
    case PASSIVE = 0;
    case ACTIVE = 1;

    public static function getTitles(): array
    {
        return array_map(
            fn(UserStatusEnum $enum) => $enum->title(),
            self::cases()
        );
    }

    public static function getKeys(): array
    {
        return array_column(self::cases(), 'name');
    }

    public function title()
    {
        return match ($this) {
            self::PASSIVE => __('control.user.status_enum.status_passive'),
            self::ACTIVE => __('control.user.status_enum.status_active'),
        };
    }
}
