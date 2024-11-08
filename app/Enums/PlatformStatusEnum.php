<?php

namespace App\Enums;

use ReflectionClass;

enum PlatformStatusEnum: int
{
    case DRAFT = 1;
    case UNDER_REVIEW = 2;
    case PUBLISHED = 3;
    case REJECTED = 4;
    case WITHDRAWN = 5;
    case PLANNED = 6;


    public static function getTitles(): array
    {
        return array_map(
            fn(PlatformStatusEnum $enum) => $enum->title(),
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
            self::DRAFT => __('control.product.platform.status_draft'),
            self::UNDER_REVIEW => __('control.product.platform.status_under_review'),
            self::PUBLISHED => __('control.product.platform.status_published'),
            self::REJECTED => __('control.product.platform.status_rejected'),
            self::WITHDRAWN => __('control.product.platform.status_withdrawn'),
            self::PLANNED => __('control.product.platform.status_planned'),
        };
    }
}
