<?php

namespace App\Http\Resources\Payment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'description' => 'Ödeme',
            'amount' => $this->amount,
            'balance' => 1000
        ];
    }
}
