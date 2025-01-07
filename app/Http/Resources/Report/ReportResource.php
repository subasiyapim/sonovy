<?php

namespace App\Http\Resources\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => Carbon::parse($this->created_at)
                ->locale(app()->getLocale())
                ->translatedFormat('d F Y H:i'),
            'period' => $this->period,
            'amount' => Number::currency($this->amount, 'USD', app()->getLocale()),
            'monthly_amount' => collect($this->monthly_amount)
                ->map(function ($value, $key) {
                    $monthName = Carbon::create(null, $key)->locale(app()->getLocale())->translatedFormat('F');
                    return "$monthName: ".Number::currency($value, 'USD', app()->getLocale());
                })
                ->implode('<br>'),
            'status' => $this->status,
            'status_text' => $this->status == 1 ? 'İşlendi' : 'İşleniyor',
        ];
    }
}
