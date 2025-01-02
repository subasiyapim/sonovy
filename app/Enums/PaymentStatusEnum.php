<?php

namespace App\Enums;

use ReflectionClass;

enum PaymentStatusEnum: int
{
    case PENDING = 1;
    case PROCESSING = 2;
    case APPROVED = 3;

    case FAILED = 4;
    case REJECTED = 5;


    public static function getTitles(): array
    {
        return array_map(
            fn(PaymentStatusEnum $enum) => $enum->title(),
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
            self::PENDING => __('control.payment.enums.status.pending'),
            self::PROCESSING => __('control.payment.enums.status.processing'),
            self::APPROVED => __('control.payment.enums.status.approved'),
            self::FAILED => __('control.payment.enums.status.failed'),
            self::REJECTED => __('control.payment.enums.status.rejected')
        };
    }
}
