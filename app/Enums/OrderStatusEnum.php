<?php

namespace App\Enums;

use ReflectionClass;

enum OrderStatusEnum: string
{
    case NEW = 'new';
    case PAID = 'paid';
    case REJECTED = 'rejected';

    case FAILED = 'failed';


    public static function getTitles(): array
    {
        return array_map(
            fn(OrderStatusEnum $enum) => $enum->title(),
            array_combine(array_column(self::cases(), 'value'), self::cases())
        );
    }

    public static function getKeys(): array
    {
        return array_map(fn($case) => $case->name, self::cases());
    }

    public static function getValues($type = 'array'): mixed
    {

        $array = array_map(fn($case) => $case->value, self::cases());
        if ($type != 'array') {

            return implode(',', $array);
        }

        return $array;
    }

    public static function getKeyTitleArray(): array
    {
        return array_map(
            fn(OrderStatusEnum $enum) => [
                'key' => $enum->value,
                'value' => $enum->title()
            ],
            self::cases()
        );
    }

    public function title()
    {
        return match ($this) {
            self::NEW => __('panel.order.enums.new'),
            self::PAID => __('panel.order.enums.paid'),
            self::REJECTED => __('panel.order.enums.rejected'),
            self::FAILED => __('panel.order.enums.failed'),
        };
    }
}
