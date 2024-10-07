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
            self::PENDING => 'panel.payment.enums.status.pending',
            self::PROCESSING => 'panel.payment.enums.status.processing',
            self::APPROVED => 'panel.payment.enums.status.approved',
            self::FAILED => 'panel.payment.enums.status.failed',
            self::REJECTED => 'panel.payment.enums.status.rejected',
        };
    }
}
