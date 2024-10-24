<?php

namespace App\Enums;

use ReflectionClass;

enum ProductStatusEnum: string
{
    case NEW = '0';
    case WAITING_FOR_APPROVAL = '1';
    case APPROVED = '2';
    case REJECTED = '3';
    case NOT_BROADCASTING = '4';

    case DRAFT = '5';

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
        };
    }
}
