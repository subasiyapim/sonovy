<?php

namespace App\Enums;

use ReflectionClass;

enum UserStatusEnum: int
{
    case PENDING_APPROVAL = 1;
    case ACTIVE = 2;
    case PASSIVE = 3;


    public static function getTitles(): array
    {
        return array_map(
            fn(UserStatusEnum $enum) => $enum->title(),
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
            self::PENDING_APPROVAL => __('control.user.status_enum.status_pending_approval'),
            self::ACTIVE => __('control.user.status_enum.status_active'),
            self::PASSIVE => __('control.user.status_enum.status_passive'),
        };
    }
}
