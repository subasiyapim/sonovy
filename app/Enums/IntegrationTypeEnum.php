<?php

namespace App\Enums;

use ReflectionClass;

enum IntegrationTypeEnum: int
{
    case SMS = 1;
    case EMAIL = 2;
    case PAYMENT_GATEWAY = 3;
    case OTHER = 4;
    case DSP_INTEGRATON = 5;


    public static function getTitles(): array
    {
        return array_map(
            fn(IntegrationTypeEnum $enum) => $enum->title(),
            array_combine(array_column(self::cases(), 'value'), self::cases())
        );
    }

    public static function getTitlesFromInputFormat(): array
    {
        return array_map(
            fn(IntegrationTypeEnum $enum) => ['label' => $enum->title(), 'value' => $enum->value],
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
            self::SMS => __('control.integration.enum_types.sms'),
            self::EMAIL => __('control.integration.enum_types.email'),
            self::PAYMENT_GATEWAY => __('control.integration.enum_types.payment_gateway'),
            self::OTHER => __('control.integration.enum_types.other'),
            self::DSP_INTEGRATON => __('control.integration.enum_types.dsp_integration'),
        };
    }
}
