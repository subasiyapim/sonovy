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
                ->translatedFormat('d-m-Y H:i'),
            'period' => $this->period,
            'name' => $this->name,
            'amount' => Number::currency(
                $this->batch_id
                    ? $this->total_amount
                    : $this->amount,
                'USD',
                app()->getLocale()
            ),
            'monthly_amount' => collect($this->monthly_amount)
                ->map(function ($value, $key) {
                    $monthName = Carbon::create(null, $key)->locale(app()->getLocale())->translatedFormat('F');
                    return "$monthName: ".$value;
                })
                ->implode('<br>'),
            'status' => $this->status,
            'status_text' => $this->status == 1 ? 'Oluşturuldu' : 'Rapor Oluşturuluyor',
        ];
    }
}
