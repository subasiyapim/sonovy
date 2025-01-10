<?php

namespace App\Http\Resources\Finance;

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

    public function __construct($resource, $tab)
    {
        parent::__construct($resource);
        $this->tab = $tab;
        $this->data = $resource;
        $this->groupedData = $this->data->groupBy(function ($item) {
            return Carbon::parse($item->sales_date)->locale(app()->getLocale())->translatedFormat('F Y');
        });
        $this->totalEarnings = $this->data->sum('earning');

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
            'all_time_earning' => Number::currency($this->data->sum('earning'), 'USD', app()->getLocale()),
            'current_month' => Carbon::now()->locale(app()->getLocale())->translatedFormat('F Y'),
            'current_month_earning' => Number::currency(
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
        $platforms = ['Spotify', 'Facebook', 'Youtube', 'Apple', 'Tiktok'];

        $platformEarnings = $this->data->groupBy('platform')->map(function ($platformData, $platform) use ($platforms) {
            $totalEarnings = $platformData->sum('earning');
            $totalQuantity = $platformData->sum('quantity');
            $earnings = $platformData->groupBy('release_name')->map(function ($releaseData) use ($totalEarnings) {
                $releaseEarnings = $releaseData->sum('earning');
                $percentage = $totalEarnings > 0 ? ($releaseEarnings / $totalEarnings) * 100 : 0;

                return [
                    'release_name' => $releaseData->first()->release_name,
                    'total_quantity' => $releaseData->sum('quantity'),
                    'total_earning' => Number::currency($releaseEarnings, 'USD', app()->getLocale()),
                    'percentage' => round($percentage, 2),
                    'quantity' => $releaseData->sum('quantity'),
                ];
            });

            return [
                'total_earning' => $totalEarnings,
                'total_quantity' => $totalQuantity,
                'releases' => $earnings->toArray(),
            ];
        });

        $sortedEarnings = $platformEarnings->sortDesc();

        $topPlatforms = $sortedEarnings->only($platforms);
        $otherEarnings = $sortedEarnings->except($platforms)->sum('total_earning');

        $topPlatforms['Other'] = [
            'total_earning' => $otherEarnings,
            'total_quantity' => $sortedEarnings->except($platforms)->sum('total_quantity'),
            'releases' => [],
        ];

        $releases = $this->data->groupBy('release_name')->map(function ($releaseData) use ($platforms) {
            $platformData = [];
            $otherEarnings = 0;
            $otherQuantity = 0;

            foreach ($releaseData->groupBy('platform') as $platform => $data) {
                $platformEarnings = $data->sum('earning');
                $totalEarnings = $releaseData->sum('earning');
                $percentage = $totalEarnings > 0 ? ($platformEarnings / $totalEarnings) * 100 : 0;
                $quantity = $data->sum('quantity');

                if (in_array($platform, $platforms)) {
                    $platformData[strtolower($platform)] = [
                        'earning' => Number::currency($platformEarnings, 'USD', app()->getLocale()),
                        'percentage' => round($percentage, 2),
                        'quantity' => $quantity,
                    ];
                } else {
                    $otherEarnings += $platformEarnings;
                    $otherQuantity += $quantity;
                }
            }

            $platformData['others'] = [
                'earning' => Number::currency($otherEarnings, 'USD', app()->getLocale()),
                'percentage' => $totalEarnings > 0 ? round(($otherEarnings / $totalEarnings) * 100, 2) : 0,
                'quantity' => $otherQuantity,
            ];

            return [
                'release_name' => $releaseData->first()->release_name,
                'total_quantity' => $releaseData->sum('quantity'),
                'total_earning' => Number::currency($releaseData->sum('earning'), 'USD', app()->getLocale()),
                'platforms' => $platformData,
            ];
        })->values()->toArray();

        return [
            'platforms' => array_merge($platforms, ['Others']),
            'releases' => $releases,
        ];
    }


    private function countries(): array
    {
        $countries = ['Turkey', 'United states', 'United kingdom', 'Portugal', 'Spain'];

        $countryEarnings = $this->data->groupBy('country')->map(function ($countryData, $country) use ($countries) {
            $totalEarnings = $countryData->sum('earning');
            $totalQuantity = $countryData->sum('quantity');
            $earnings = $countryData->groupBy('release_name')->map(function ($releaseData) use ($totalEarnings) {
                $releaseEarnings = $releaseData->sum('earning');
                $percentage = $totalEarnings > 0 ? ($releaseEarnings / $totalEarnings) * 100 : 0;

                return [
                    'release_name' => $releaseData->first()->release_name,
                    'total_quantity' => $releaseData->sum('quantity'),
                    'total_earning' => Number::currency($releaseEarnings, 'USD', app()->getLocale()),
                    'percentage' => round($percentage, 2),
                    'quantity' => $releaseData->sum('quantity'),
                ];
            });

            return [
                'total_earning' => $totalEarnings,
                'total_quantity' => $totalQuantity,
                'releases' => $earnings->toArray(),
            ];
        });

        $sortedEarnings = $countryEarnings->sortDesc();

        $topCountries = $sortedEarnings->only($countries);
        $otherEarnings = $sortedEarnings->except($countries)->sum('total_earning');

        $topCountries['Others'] = [
            'total_earning' => $otherEarnings,
            'total_quantity' => $sortedEarnings->except($countries)->sum('total_quantity'),
            'releases' => [],
        ];

        $releases = $this->data->groupBy('release_name')->map(function ($releaseData) use ($countries) {
            $countryData = [];
            $otherEarnings = 0;
            $otherQuantity = 0;

            foreach ($releaseData->groupBy('country') as $country => $data) {
                $countryEarnings = $data->sum('earning');
                $totalEarnings = $releaseData->sum('earning');
                $percentage = $totalEarnings > 0 ? ($countryEarnings / $totalEarnings) * 100 : 0;
                $quantity = $data->sum('quantity');

                if (in_array($country, $countries)) {
                    $countryData[strtolower($country)] = [
                        'earning' => Number::currency($countryEarnings, 'USD', app()->getLocale()),
                        'percentage' => round($percentage, 2),
                        'quantity' => $quantity,
                    ];
                } else {
                    $otherEarnings += $countryEarnings;
                    $otherQuantity += $quantity;
                }
            }

            $countryData['others'] = [
                'earning' => Number::currency($otherEarnings, 'USD', app()->getLocale()),
                'percentage' => $totalEarnings > 0 ? round(($otherEarnings / $totalEarnings) * 100, 2) : 0,
                'quantity' => $otherQuantity,
            ];

            return [
                'release_name' => $releaseData->first()->release_name,
                'total_quantity' => $releaseData->sum('quantity'),
                'total_earning' => Number::currency($releaseData->sum('earning'), 'USD', app()->getLocale()),
                'countries' => $countryData,
            ];
        })->values()->toArray();

        return [
            'countries' => array_merge($countries, ['Others']),
            'releases' => $releases,
        ];
    }

    private function monthlyNetEarning(): array
    {
        $platforms = ['Spotify', 'Amazon', 'Youtube'];

        $calculateEarnings = function ($data) use ($platforms) {
            $totalEarnings = $data->sum('earning');
            $earnings = $data->groupBy('platform')->mapWithKeys(function ($platformData, $platform) use (
                $platforms,
                $totalEarnings
            ) {
                $sum = $platformData->sum('earning');
                $percentage = $totalEarnings > 0 ? ($sum / $totalEarnings) * 100 : 0;
                if (in_array($platform, $platforms)) {
                    return [
                        $platform => [
                            'earning' => Number::currency($sum, 'USD', app()->getLocale()),
                            'percentage' => round($percentage, 2),
                        ]
                    ];
                }
                return [
                    'other' => [
                        'earning' => Number::currency($sum, 'USD', app()->getLocale()),
                        'percentage' => round($percentage, 2),
                    ]
                ];
            });

            foreach ($platforms as $platform) {
                if (!isset($earnings[$platform])) {
                    $earnings[$platform] = [
                        'earning' => Number::currency(0, 'USD', app()->getLocale()),
                        'percentage' => 0,
                    ];
                }
            }

            if (!isset($earnings['other'])) {
                $earnings['other'] = [
                    'earning' => Number::currency(0, 'USD', app()->getLocale()),
                    'percentage' => 0,
                ];
            }

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
            return [$platform => $earning];
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
            return [$country => $earning];
        })->toArray();
    }

    private function earningFromYoutube(): array
    {
        $platformEarnings = $this->data->filter(function ($item) {
            return stripos($item->platform, 'Youtube') !== false;
        })->groupBy('platform')->map(function ($platformData) {
            return $platformData->sum('earning');
        });

        $totalEarnings = $platformEarnings->sum();

        $result = $platformEarnings->mapWithKeys(function ($earning, $platform) use ($totalEarnings) {
            $percentage = $totalEarnings > 0 ? ($earning / $totalEarnings) * 100 : 0;
            return [
                $platform => [
                    'earning' => Number::currency($earning, 'USD', app()->getLocale()),
                    'percentage' => round($percentage, 2),
                ]
            ];
        });

        return $result->toArray();
    }

    private function earningFromYoutubeWithPremium(): array
    {
        $platformEarnings = $this->data->filter(function ($item) {
            return stripos($item->platform, 'Youtube') !== false && !empty($item->streaming_subscription_type);
        })->groupBy(['platform', 'streaming_subscription_type'])->map(function ($platformData) {
            return $platformData->sum('earning');
        });

        $totalEarnings = $platformEarnings->sum();

        $result = $platformEarnings->mapWithKeys(function ($earning, $keys) use ($totalEarnings) {
            list($platform, $subscriptionType) = explode('.', $keys);
            $percentage = $totalEarnings > 0 ? ($earning / $totalEarnings) * 100 : 0;
            return [
                $platform => [
                    $subscriptionType => [
                        'earning' => Number::currency($earning, 'USD', app()->getLocale()),
                        'percentage' => round($percentage, 2),
                    ]
                ]
            ];
        });

        return $result->toArray();
    }

    private function earningFromSalesType(): array
    {
        $salesTypeEarnings = $this->data->groupBy('sales_type')->map(function ($salesTypeData) {
            return $salesTypeData->sum('earning');
        });

        $sortedEarnings = $salesTypeEarnings->sortDesc();

        $topSalesTypes = $sortedEarnings->take(5);

        return $topSalesTypes->mapWithKeys(function ($earning, $salesType) {
            return [$salesType => $earning];
        })->toArray();
    }

    private function trendingAlbums(): array
    {
        return $this->data->groupBy('upc_code')->take(10)->values()->map(function ($items) {
            $firstItem = $items->first();
            $product = $firstItem->product;

            return [
                'earning' => Number::currency($items->sum('earning'), 'USD', app()->getLocale()),
                'product_name' => $product->album_name ?? $firstItem->release_name,
                'product_id' => $product->id ?? '',
            ];
        })->toArray();
    }

    private function topArtists(): array
    {
        return $this->data->groupBy('artist_id')->values()->map(function ($artistData) {
            $artistEarnings = $artistData->sum('earning');
            $percentage = $this->totalEarnings > 0 ? ($artistEarnings / $this->totalEarnings) * 100 : 0;

            return [
                'artist_id' => $artistData->first()->artist_id,
                'artist_name' => $artistData->first()->artist_name,
                'earning' => Number::currency($artistEarnings, 'USD', app()->getLocale()),
                'streams' => $artistData->sum('quantity'),
                'percentage' => round($percentage, 2),
            ];
        })->sortByDesc('earning')->toArray();
    }

    private function topAlbums(): array
    {
        return $this->data->groupBy('release_name')->values()->map(function ($albumData) {
            $albumEarnings = $albumData->sum('earning');
            $percentage = $this->totalEarnings > 0 ? ($albumEarnings / $this->totalEarnings) * 100 : 0;
            $firstItem = $albumData->first();
            $artistName = $firstItem->artist_name;

            return [
                'album_name' => $firstItem->product->album_name ?? '',
                'upc_code' => $firstItem->upc_code,
                'product_id' => $firstItem->product->id ?? '',
                'artist_name' => $artistName,
                'earning' => Number::currency($albumEarnings, 'USD', app()->getLocale()),
                'streams' => $albumData->sum('quantity'),
                'percentage' => round($percentage, 2),
            ];
        })->sortByDesc('earning')->values()->toArray();
    }

    private function topSongs(): array
    {
        return $this->data->groupBy('isrc_code')->values()->map(function ($songData) {
            $songEarnings = $songData->sum('earning');
            $percentage = $this->totalEarnings > 0 ? ($songEarnings / $this->totalEarnings) * 100 : 0;
            $firstItem = $songData->first();

            return [
                'song_id' => $firstItem->song_id,
                'song_name' => $firstItem->song_name,
                'isrc_code' => $firstItem->isrc_code,
                'artist_id' => $firstItem->artist_id,
                'artist_name' => $firstItem->artist_name,
                'earning' => Number::currency($songEarnings, 'USD', app()->getLocale()),
                'streams' => $songData->sum('quantity'),
                'percentage' => round($percentage, 2),
            ];

        })->values()->toArray();


    }

    private function topLabels(): array
    {
        return $this->data->groupBy('label_id')->values()->map(function ($labelData) {
            $labelEarnings = $labelData->sum('earning');
            $percentage = $this->totalEarnings > 0 ? ($labelEarnings / $this->totalEarnings) * 100 : 0;
            $firstItem = $labelData->first();

            return [
                'label_id' => $firstItem->label_id,
                'label_name' => $firstItem->label_name,
                'earning' => Number::currency($labelEarnings, 'USD', app()->getLocale()),
                'percentage' => round($percentage, 2),
            ];
        })->sortByDesc('earning')->values()->toArray();
    }
}
