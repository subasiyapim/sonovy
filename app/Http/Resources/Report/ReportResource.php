<?php

namespace App\Http\Resources\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class ReportResource extends JsonResource
{

    const QUARTERS = [
        ['name' => 'Q1', 'startMonth' => 1, 'endMonth' => 3],
        ['name' => 'Q2', 'startMonth' => 4, 'endMonth' => 6],
        ['name' => 'Q3', 'startMonth' => 7, 'endMonth' => 9],
        ['name' => 'Q4', 'startMonth' => 10, 'endMonth' => 12],
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'period' => $this->getPeriodName($this->period),
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

    private function getPeriodName($period)
    {
        if (preg_match('/^Q([1-4])-(\d{4})$/', $period, $matches)) {
            $quarterIndex = (int) $matches[1] - 1;
            $year = $matches[2];

            if (isset(self::QUARTERS[$quarterIndex])) {
                $startMonth = self::QUARTERS[$quarterIndex]['startMonth'];
                $endMonth = self::QUARTERS[$quarterIndex]['endMonth'];

                return sprintf(
                    "%s. Çeyrek (%s - %s %s)",
                    $matches[1],
                    Carbon::create(null, $startMonth)->translatedFormat('F'),
                    Carbon::create(null, $endMonth)->translatedFormat('F'),
                    $year
                );
            }
        }

        return $period; // Format uymuyorsa olduğu gibi döndür
    }
}
