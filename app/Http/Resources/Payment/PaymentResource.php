<?php

namespace App\Http\Resources\Payment;

use App\Enums\PaymentStatusEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date' => $this->created_at ?? null,
            'process_type' => $this->process_type ?? null,
            'description' => $this->getDescription() ?? null,
            'amount' => isset($this->amount) ? Number::currency($this->amount, 'USD', app()->getLocale()) : null,
            'balance' => $this->user->balance ?? null,
            'status_text' => isset($this->status) ? PaymentStatusEnum::from($this->status->value)->title() : null,
            'status' => $this->status ?? null,
            'planned_payment_date' => isset($this->created_at) ? $this->created_at->addDays(7)->format('d.m.Y') : null,
        ];
    }


    /**
     * Generate a human-readable description based on the payment status.
     *
     * @return string
     */
    private function getDescription(): string
    {
        if (!isset($this->status)) { // Null veya undefined durumda default değer döndür
            return 'Durum bilgisi mevcut değil.';
        }
        return match ($this->status) {
            PaymentStatusEnum::PENDING => 'Para çekme talebi',
            PaymentStatusEnum::PROCESSING => 'Para çekme işlemi devam ediyor',
            PaymentStatusEnum::APPROVED => 'Para çekme işlemi tamamlandı',
            PaymentStatusEnum::FAILED => 'Para çekme işlemi başarısız',
            PaymentStatusEnum::REJECTED => 'Para çekme işlemi reddedildi',
        };

    }
}
