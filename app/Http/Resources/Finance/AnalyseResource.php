<?php

namespace App\Http\Resources\Finance;

use App\Services\AnalyseService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class AnalyseResource extends JsonResource
{
    protected mixed $tab;
    protected string $start_date;
    protected string $end_date;
    protected $data;
    protected $totalEarnings;
    protected $groupedData;

    public AnalyseService $analyseService;


    public function __construct($resource, $tab)
    {
        parent::__construct($resource);
        $this->tab = $tab;
        $this->data = $resource;
        $this->groupedData = $this->data->groupBy(function ($item) {
            return Carbon::parse($item->sales_date)->locale(app()->getLocale())->translatedFormat('F Y');
        });
        $this->totalEarnings = $this->data->sum('earning');

        $this->analyseService = new AnalyseService($this->data);

    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'metadata' => $this->metadata(),
            'data' => $this->tab(),
        ];
    }

    private function metadata(): array
    {
        return [
            'all_time_earning' => priceFormat($this->data->sum('earning')),
            'current_month' => Carbon::now()->locale(app()->getLocale())->translatedFormat('F Y'),
            'current_month_earning' => priceFormat(
                $this->data->whereBetween(
                    'sales_date',
                    [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]
                )->sum('earning'),
                'USD',
                app()->getLocale()
            ),
        ];
    }

    private function tab(): array
    {
        return match ($this->tab) {
            'top-lists' => $this->topLists(),
            'platforms' => $this->platforms(),
            'countries' => $this->countries(),
            default => $this->general(),
        };
    }

    private function general(): array
    {
        return [
            'monthly_net_earnings' => $this->monthlyNetEarning(),
            'spotify_discovery_mode_earnings' => $this->spotifyDiscoveryModeEarnings(),
            'earning_from_platforms' => $this->earningFromPlatforms(),
            'earning_from_countries' => $this->earningFromCountries(),
            'earning_from_youtube' => $this->earningFromYoutube(),
            'earning_from_youtube_with_premium' => $this->earningFromYoutubeWithPremium(),
            'earning_from_sales_type' => $this->earningFromSalesType(),
            'trending_albums' => $this->trendingAlbums(),
        ];
    }

    private function topLists(): array
    {
        return [
            'top_artists' => $this->topArtists(),
            'top_albums' => $this->topAlbums(),
            'top_songs' => $this->topSongs(),
            'top_labels' => $this->topLabels(),
        ];
    }

    private function platforms(): array
    {
        return $this->analyseService->platforms();
    }

    private function countries(): array
    {
        return $this->analyseService->countries();
    }

    private function monthlyNetEarning(): array
    {
        return $this->analyseService->monthlyNetEarning();
    }

    private function spotifyDiscoveryModeEarnings(): array
    {
        return $this->analyseService->spotifyDiscoveryModeEarnings();
    }

    private function earningFromPlatforms(): array
    {
        return $this->analyseService->earningFromPlatforms();
    }

    private function earningFromCountries(): array
    {
        return $this->analyseService->earningFromCountries();
    }

    private function earningFromYoutube(): array
    {
        return $this->analyseService->earningFromYoutube();
    }

    private function earningFromYoutubeWithPremium(): array
    {
        return $this->analyseService->earningFromYoutubeWithPremium();
    }

    private function earningFromSalesType(): array
    {
        return $this->analyseService->earningFromSalesType();
    }

    private function trendingAlbums(): array
    {
        return $this->analyseService->trendingAlbums();
    }

    private function topArtists(): array
    {
        return $this->analyseService->topArtists();
    }

    private function topAlbums(): array
    {
        return $this->analyseService->topAlbums();
    }

    private function topSongs(): array
    {
        return $this->analyseService->topSongs();
    }

    private function topLabels(): array
    {
        return $this->analyseService->topLabels();
    }
}
