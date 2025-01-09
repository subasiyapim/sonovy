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
            'date' => $this->created_at->format('d.m.Y'),
            'process_type' => $this->process_type,
            'description' => $this->getDescription(),
            'amount' => Number::currency($this->amount, 'USD', app()->getLocale()),
            'balance' => $this->user->balance,
            'status_text' => PaymentStatusEnum::from($this->status->value)->title(),
            'status' => $this->status,
            'planned_payment_date' => Carbon::parse($this->created_at)->addDays(7)->format('d.m.Y')
        ];
    }

    /**
     * Generate a human-readable description based on the payment status.
     *
     * @return string
     */
    private function getDescription(): string
    {
        return match ($this->status) {
            PaymentStatusEnum::PENDING => 'Para çekme talebi',
            PaymentStatusEnum::PROCESSING => 'Para çekme işlemi devam ediyor',
            PaymentStatusEnum::APPROVED => 'Para çekme işlemi tamamlandı',
            PaymentStatusEnum::FAILED => 'Para çekme işlemi başarısız',
            PaymentStatusEnum::REJECTED => 'Para çekme işlemi reddedildi',
        };
    }
}
