<?php

namespace App\Enums;

use ReflectionClass;

enum ProductStatusEnum: int
{
    case DRAFT = 1;
    case WAITING_FOR_APPROVAL = 2;
    case APPROVED = 3;
    case REJECTED = 4;
    case NOT_BROADCASTING = 5;
    case PLANNED = 6;


    public static function getTitles(): array
    {
        return array_map(
            fn(ProductStatusEnum $enum) => $enum->title(),
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
            self::DRAFT => 'Taslak',
            self::WAITING_FOR_APPROVAL => 'İnceleniyor',
            self::APPROVED => 'Yayınlandı',
            self::REJECTED => 'Reddedildi',
            self::NOT_BROADCASTING => 'Geri çekildi',
            self::PLANNED => 'Planlandı'
        };
    }
}
