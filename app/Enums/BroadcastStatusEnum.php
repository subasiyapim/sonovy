<?php

namespace App\Enums;

use ReflectionClass;

enum BroadcastStatusEnum: string
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
            fn(BroadcastStatusEnum $enum) => $enum->title(),
            array_combine(array_column(self::cases(), 'value'), self::cases())
        );
    }

    public function title()
    {
        return match ($this) {
            self::NEW => __('panel.broadcast.status_new'),
            self::WAITING_FOR_APPROVAL => __('panel.broadcast.status_waiting'),
            self::APPROVED => __('panel.broadcast.status_approved'),
            self::REJECTED => __('panel.broadcast.status_rejected'),
            self::NOT_BROADCASTING => __('panel.broadcast.status_not_broadcasting'),
            self::DRAFT => __('panel.broadcast.status_draft'),
        };
    }
}
