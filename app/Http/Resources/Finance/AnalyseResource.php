<?php

namespace App\Http\Resources\Finance;

use App\Services\AnalyseService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;

class AnalyseResource extends JsonResource
{
    protected mixed $tab;
    protected Collection $data;
    protected AnalyseService $analyseService;

    private ?Collection $groupedData = null;
    private ?float $totalEarnings = null;

    public function __construct($resource, $tab)
    {
        parent::__construct($resource);
        $this->tab = $tab;
        $this->data = $resource;
        $this->analyseService = new AnalyseService($this->data);
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'metadata' => $this->metadata(),
            'data' => $this->resolveTabData(),
        ];
    }

    /**
     * Lazily load grouped data.
     */
    private function groupedData(): Collection
    {
        if (!$this->groupedData) {
            $this->groupedData = $this->data->groupBy(fn($item) => Carbon::parse($item->sales_date)
                ->locale(app()->getLocale())
                ->translatedFormat('F Y')
            );
        }
        return $this->groupedData;
    }

    /**
     * Lazily calculate total earnings.
     */
    private function totalEarnings(): float
    {
        if (!$this->totalEarnings) {
            $this->totalEarnings = $this->data->sum('earning');
        }
        return $this->totalEarnings;
    }

    /**
     * Metadata extraction, optimized for repeated calculations.
     */
    private function metadata(): array
    {
        $currentMonth = Carbon::now()->locale(app()->getLocale())->translatedFormat('F Y');
        $currentMonthEarnings = $this->data->whereBetween('sales_date', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ])->sum('earning');

        return [
            'all_time_earning' => Number::currency($this->totalEarnings(), 'USD', app()->getLocale()),
            'current_month' => $currentMonth,
            'current_month_earning' => Number::currency($currentMonthEarnings, 'USD', app()->getLocale()),
        ];
    }

    /**
     * Resolve tab-specific data.
     */
    private function resolveTabData(): array
    {
        return match ($this->tab) {
            'top-lists' => $this->topLists(),
            'platforms' => $this->analyseService->platforms(),
            'countries' => $this->analyseService->countries(),
            default => $this->general(),
        };
    }

    /**
     * General Tab Data.
     */
    private function general(): array
    {
        return [
            'monthly_net_earnings' => $this->analyseService->monthlyNetEarning(),
            'spotify_discovery_mode_earnings' => $this->analyseService->spotifyDiscoveryModeEarnings(),
            'earning_from_platforms' => $this->analyseService->earningFromPlatforms(),
            'earning_from_countries' => $this->analyseService->earningFromCountries(),
            'earning_from_youtube' => $this->analyseService->earningFromYoutube(),
            'earning_from_youtube_with_premium' => $this->analyseService->earningFromYoutubeWithPremium(),
            'earning_from_sales_type' => $this->analyseService->earningFromSalesType(),
            'trending_albums' => $this->analyseService->trendingAlbums(),
        ];
    }

    /**
     * Top Lists Tab Data.
     */
    private function topLists(): array
    {
        return [
            'top_artists' => $this->analyseService->topArtists(),
            'top_albums' => $this->analyseService->topAlbums(),
            'top_songs' => $this->analyseService->topSongs(),
            'top_labels' => $this->analyseService->topLabels(),
        ];
    }
}