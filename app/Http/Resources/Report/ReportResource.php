<?php

namespace App\Http\Resources\Report;

use App\Models\Artist;
use App\Models\Label;
use App\Models\Platform;
use App\Models\Product;
use App\Models\Song;
use App\Models\System\Country;
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
            'tooltipData' => $this->getTooltipData(),
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
                    "%s %s- %s %s",
                    Carbon::create(null, $startMonth)->translatedFormat('F'),
                    $year,
                    Carbon::create(null, $endMonth)->translatedFormat('F'),
                    $year
                );
            }
        }

        return $period; // Format uymuyorsa olduğu gibi döndür
    }

    private function getTooltipData(): ?array
    {
        if (!$this->report_type || !$this->report_ids) {
            return null;
        }

        $data = match ($this->report_type) {
            'artists', 'multiple_artists' => Artist::whereIn('id', $this->report_ids)
                ->select('id', 'name')
                ->get()
                ->map(fn($item) => ['id' => $item->id, 'name' => $item->name])
                ->toArray(),

            'songs', 'multiple_songs' => Song::whereIn('id', $this->report_ids)
                ->select('id', 'name')
                ->get()
                ->map(fn($item) => ['id' => $item->id, 'name' => $item->name])
                ->toArray(),

            'platforms', 'multiple_platforms' => Platform::whereIn('id', $this->report_ids)
                ->select('id', 'name')
                ->get()
                ->map(fn($item) => ['id' => $item->id, 'name' => $item->name])
                ->toArray(),

            'products', 'multiple_products' => Product::whereIn('id', $this->report_ids)
                ->select('id', 'album_name')
                ->get()
                ->map(fn($item) => ['id' => $item->id, 'name' => $item->name])
                ->toArray(),

            'countries', 'multiple_countries' => Country::whereIn('id', $this->report_ids)
                ->select('id', 'name')
                ->get()
                ->map(fn($item) => ['id' => $item->id, 'name' => $item->name])
                ->toArray(),

            'labels', 'multiple_labels' => Label::whereIn('id', $this->report_ids)
                ->select('id', 'name')
                ->get()
                ->map(fn($item) => ['id' => $item->id, 'name' => $item->name])
                ->toArray(),

            default => null
        };

        return $data ?? [];
    }
}
