<?php

namespace App\Enums;

use ReflectionClass;

enum ProductStatusEnum: int
{
    case NEW = 1;
    case WAITING_FOR_APPROVAL = 2;
    case APPROVED = 3;
    case REJECTED = 4;
    case NOT_BROADCASTING = 5;
    case PLANNED = 6;
    case DRAFT = 7;

    public static function getTitles(): array
    {
        return array_map(
            fn(ProductStatusEnum $enum) => $enum->title(),
            array_combine(array_column(self::cases(), 'value'), self::cases())
        );
    }

    public function title()
    {
        return match ($this) {
            self::NEW => __('control.product.status_new'),
            self::WAITING_FOR_APPROVAL => __('control.product.status_waiting'),
            self::APPROVED => __('control.product.status_approved'),
            self::REJECTED => __('control.product.status_rejected'),
            self::NOT_BROADCASTING => __('control.product.status_not_producting'),
            self::DRAFT => __('control.product.status_draft'),
            self::PLANNED => __('control.product.status_planned'),
        };
    }
}
