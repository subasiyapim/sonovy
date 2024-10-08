<?php

namespace App\Http\Controllers\Control;

use App\Enums\PaymentProcessTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Earning;
use App\Models\Payment;
use App\Services\EarningService;
use Illuminate\Support\Facades\DB;

class FinanceAndEarningController extends Controller
{


    public function index()
    {
        $earning_total = EarningService::balance();

        // Kullanıcıların toplam kazançlarını hesapla
        $earnings = Earning::select('user_id', DB::raw('SUM(earning) as total_earning'))
            ->groupBy('user_id')
            ->get()
            ->pluck('total_earning', 'user_id')
            ->toArray();

        // Tüm ödemeleri kullanıcı bazında sıralı olarak al
        $payments = Payment::orderBy('user_id')
            ->advancedFilter();

        // Kümülatif bakiyeyi hesaplamak için bir dizi oluştur
        $userBalances = [];

        // Kümülatif bakiyeyi manuel olarak hesapla
        foreach ($payments as $payment) {
            if (!isset($userBalances[$payment->user_id])) {
                // Kullanıcının toplam kazancı varsa başlangıç bakiyesi olarak kullan
                $userBalances[$payment->user_id] = isset($earnings[$payment->user_id]) ? $earnings[$payment->user_id] : 0;
            }

            // Sadece "ödeme başarılı" olanları kümülatif bakiyeye ekle ve negatif yap
            if ($payment->status === PaymentStatusEnum::APPROVED->value) {
                $userBalances[$payment->user_id] += -1 * $payment->amount;
            }

            // Mevcut bakiyeyi kümülatif bakiye olarak ayarla
            $payment->cumulative_balance = $userBalances[$payment->user_id];
        }

        $payment_process_types = PaymentProcessTypeEnum::getTitles();
        $payment_statuses = PaymentStatusEnum::getTitles();

        return inertia('Control/FinanceAndEarning/Index',
            [
                'earning_total' => $earning_total,
                'payments' => $payments,
                'payment_process_types' => $payment_process_types,
                'payment_statuses' => $payment_statuses,
            ]);
    }

}
