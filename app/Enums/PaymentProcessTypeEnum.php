<?php

namespace App\Enums;

use ReflectionClass;

enum PaymentProcessTypeEnum: int
{
    case MONEY_TRANSFER = 1;
    case SALES = 2;
    case ROYALTY_EARNING = 3;
    case APPROVED_ADVANCE = 4;


    public static function getTitles(): array
    {
        return array_map(
            fn(PaymentProcessTypeEnum $enum) => $enum->title(),
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
            self::MONEY_TRANSFER => 'panel.payment.enums.process_type.money_transfer',
            self::SALES => 'panel.payment.enums.process_type.sales',
            self::ROYALTY_EARNING => 'panel.payment.enums.process_type.royalty_earning',
            self::APPROVED_ADVANCE => 'panel.payment.enums.process_type.approved_advance',
        };
    }
}
