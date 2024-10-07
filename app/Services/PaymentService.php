<?php

namespace App\Services;

use App\Models\Setting;

class PaymentService
{
    public static function calculateCommission($amount): array
    {
        $commission_rate = Setting::where('key', 'withdrawal_commission_rate')->first()->value;
        $commission = $amount * $commission_rate / 100;

        return [
            'commission_rate' => $commission_rate,
            'commission' => $commission,
            'amount' => $amount - $commission,
        ];
    }

}
