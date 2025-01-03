<?php

namespace App\Services;

use App\Enums\PaymentProcessTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;

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

    public static function getTotalPayment($user_id = null)
    {
        return Payment::when($user_id, function ($query) use ($user_id) {
            $query->where('user_id', $user_id)
                ->where('process_type', PaymentProcessTypeEnum::MONEY_TRANSFER->value)
                ->whereNotIn('status', [PaymentStatusEnum::REJECTED->value, PaymentStatusEnum::FAILED->value]);
        }, function ($query) {
            $query->where('user_id', auth()->id())
                ->where('process_type', PaymentProcessTypeEnum::MONEY_TRANSFER->value)
                ->whereNotIn('status', [PaymentStatusEnum::REJECTED->value, PaymentStatusEnum::FAILED->value]);
        })->get()->sum('amount');

    }

    public static function getAdvancePaymentTotal($user_id = null)
    {
        return Payment::when($user_id, function ($query) use ($user_id) {
            $query->where('user_id', $user_id)
                ->where('status', PaymentStatusEnum::APPROVED->value)
                ->where('process_type', PaymentProcessTypeEnum::APPROVED_ADVANCE->value);
        }, function ($query) {
            $query->where('user_id', auth()->id())
                ->where('status', PaymentStatusEnum::APPROVED->value)
                ->where('process_type', PaymentProcessTypeEnum::APPROVED_ADVANCE->value);
        })->get()->sum('amount');
    }

    public static function getTotalPendingPayment(int $user_id = null)
    {
        return Payment::when($user_id, function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })
            ->where('status', PaymentStatusEnum::PENDING->value)
            ->get()->sum('amount');
    }

    public static function getPendingPayment(int $user_id = null)
    {
        return Payment::when($user_id, function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })
            ->where('status', PaymentStatusEnum::PENDING->value)
            ->first();
    }
}
