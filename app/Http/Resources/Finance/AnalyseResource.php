<?php

namespace App\Http\Resources\Finance;

use App\Models\Earning;
use App\Models\Platform;
use App\Services\EarningService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;

class AnalyseResource extends JsonResource
{
    protected mixed $tab;
    protected string $start_date;
    protected string $end_date;
    protected $data;
    protected $groupedData;

    public function __construct($resource, $tab)
    {
        parent::__construct($resource);
        $this->tab = $tab;
        $this->data = $resource;
        $this->groupedData = $this->data->groupBy(function ($item) {
            return Carbon::parse($item->sales_date)->format('F Y');
        });

    }


    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            $this->content(),
            $this->metadata()
        ];
    }

    private function content(): array
    {
        return [
            'all_time_earning' => Number::currency($this->data->sum('earning'), 'USD', app()->getLocale()),
            'current_month' => now()->format('F Y'),
            'current_month_earning' => Number::currency($this->data->whereBetween('sales_date',
                [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('earning'), 'USD',
                app()->getLocale()),
        ];
    }

    private function metadata(): array
    {
        return match ($this->tab) {
            'general' => $this->general(),
            'top-lists' => $this->topLists(),
            'platforms' => $this->platforms(),
            'countries' => $this->countries(),
            default => $this->metadata(),
        };
    }

    private function general(): array
    {
        return [
            'monthly_net_earnings' => $this->monthlyNetEarning(),
            'spotify_discovery_mode_earnings' => $this->spotifyDiscoveryModeEarnings(),
            'earning_from_platforms' => $this->earningFromPlatforms(),
            'earning_from_countries' => $this->earningFromCountries(),
        ];
    }

    private function topLists(): array
    {
        return [];
    }

    private function platforms(): array
    {
        return [];
    }

    private function countries(): array
    {
        return [];
    }

    private function monthlyNetEarning(): array
    {
        $platforms = ['Spotify', 'Amazon', 'Youtube'];

        $calculateEarnings = function ($data) use ($platforms) {
            $earnings = $data->groupBy('platform')->mapWithKeys(function ($platformData, $platform) use ($platforms) {
                $sum = $platformData->sum('earning');
                if (in_array($platform, $platforms)) {
                    return [$platform => Number::currency($sum, 'USD', app()->getLocale())];
                }
                return ['other' => $sum];
            });

            foreach ($platforms as $platform) {
                if (!isset($earnings[$platform])) {
                    $earnings[$platform] = Number::currency(0, 'USD', app()->getLocale());
                }
            }

            $earnings['other'] = isset($earnings['other'])
                ? Number::currency($earnings['other'], 'USD', app()->getLocale())
                : Number::currency(0, 'USD', app()->getLocale());

            return $earnings;
        };

        $items = $this->groupedData->map(fn($monthData) => $calculateEarnings($monthData));

        $total = $calculateEarnings($this->data);

        return [
            'total' => $total->toArray(),
            'items' => $items->toArray(),
        ];
    }


    private function spotifyDiscoveryModeEarnings(): array
    {
        $items = $this->groupedData->mapWithKeys(function ($monthData, $month) {
            $monthData = collect($monthData);

            $promotion = $monthData->filter(function ($item) {
                return $item['platform_id'] == 2 && $item['sales_type'] === 'PLATFORM PROMOTION';
            })->sum('earning');

            $earning = $monthData->filter(function ($item) {
                return $item['platform_id'] == 2 && $item['sales_type'] !== 'PLATFORM PROMOTION';
            })->sum('earning');

            $net = $promotion + $earning;

            return [
                $month => [
                    'promotion' => Number::currency($promotion, 'USD', app()->getLocale()),
                    'earning' => Number::currency($earning, 'USD', app()->getLocale()),
                    'total' => Number::currency($net, 'USD', app()->getLocale()),
                ]
            ];
        });

        $total = $this->data->where('platform_id', 2)
            ->groupBy('platform_id')
            ->mapWithKeys(function ($platformData) {
                $promotion = $platformData->where('sales_type', 'PLATFORM PROMOTION')->sum('earning');
                $earning = $platformData->filter(function ($item) {
                    return $item['sales_type'] !== 'PLATFORM PROMOTION';
                })->sum('earning');

                $total = $promotion + $earning;

                return [
                    'promotion' => Number::currency($promotion, 'USD', app()->getLocale()),
                    'earning' => Number::currency($earning, 'USD', app()->getLocale()),
                    'total' => Number::currency($total, 'USD', app()->getLocale()),
                ];
            });

        return [
            'total' => $total->toArray(),
            'items' => $items->toArray(),
        ];
    }

    private function earningFromPlatforms(): array
    {
        $platformEarnings = $this->data->groupBy('platform')->map(function ($platformData) {
            return $platformData->sum('earning');
        });

        $sortedEarnings = $platformEarnings->sortDesc();

        $topPlatforms = $sortedEarnings->take(5);
        $otherEarnings = $sortedEarnings->slice(5)->sum();

        $topPlatforms['others'] = $otherEarnings;

        return $topPlatforms->mapWithKeys(function ($earning, $platform) {
            return [$platform => Number::currency($earning, 'USD', app()->getLocale())];
        })->toArray();
    }

    private function earningFromCountries(): array
    {
        $countryEarnings = $this->data->groupBy('country')->map(function ($countryData) {
            return $countryData->sum('earning');
        });

        $sortedEarnings = $countryEarnings->sortDesc();

        $topCountries = $sortedEarnings->take(5);
        $otherEarnings = $sortedEarnings->slice(5)->sum();

        $topCountries['others'] = $otherEarnings;

        return $topCountries->mapWithKeys(function ($earning, $country) {
            return [$country => Number::currency($earning, 'USD', app()->getLocale())];
        })->toArray();
    }

}
