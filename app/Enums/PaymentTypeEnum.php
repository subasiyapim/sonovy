<?php

namespace App\Enums;

use ReflectionClass;

enum PaymentTypeEnum: int
{
    case BANK_TRANSFER = 1;
    case CREDIT_CARD = 2;
    case CASH = 3;


    public static function getTitles(): array
    {
        return array_map(
            fn(PaymentTypeEnum $enum) => $enum->title(),
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
            self::BANK_TRANSFER => 'panel.payment.enums.payment_type.bank_transfer',
            self::CREDIT_CARD => 'panel.payment.enums.payment_type.credit_card',
            self::CACHE => 'panel.payment.enums.payment_type.cache',
        };
    }
}
