<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;


class AnalyseService
{
    protected $totalEarnings;
    protected $data;
    protected $groupedData;
    protected const CACHE_DURATION = 60 * 60 * 12; // 12 saat

    public function __construct($data)
    {
        $this->data = $data;
        $this->totalEarnings = $data->sum('earning');
        $this->groupedData = $this->data->groupBy(function ($item) {
            return Carbon::parse($item->sales_date)->locale(app()->getLocale())->translatedFormat('F Y');
        });
    }

    public function countries(): array
    {
        $cacheKey = 'countries_analysis_' . md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $countries = ['Turkey', 'United states', 'United kingdom', 'Portugal', 'Spain'];

            $countryEarnings = $this->data->groupBy('country')->map(function ($countryData, $country) use ($countries) {
                $totalEarnings = $countryData->sum('earning');
                $totalQuantity = $countryData->sum('quantity');
                $earnings = $countryData->groupBy('release_name')->map(function ($releaseData) use ($totalEarnings) {
                    $releaseEarnings = $releaseData->sum('earning');
                    $percentage = $totalEarnings > 0 ? ($releaseEarnings / $totalEarnings) * 100 : 0;
                    $firstItem = $releaseData->first();
                    $product = $firstItem->product ?? null;

                    return [
                        'release_name' => $firstItem->release_name,
                        'total_quantity' => $releaseData->sum('quantity'),
                        'total_earning' => Number::currency($releaseEarnings, 'USD', app()->getLocale()),
                        'percentage' => round($percentage, 2),
                        'quantity' => $releaseData->sum('quantity'),
                        'product' => $product ? [
                            'id' => $product->id,
                            'image' => $product->image
                        ] : null
                    ];
                });

                return [
                    'start_date' => Cache::get('start_date'),
                    'end_date' => Cache::get('end_date'),
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
                $firstItem = $releaseData->first();
                $product = $firstItem->product ?? null;

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
                    'start_date' => Cache::get('start_date'),
                    'end_date' => Cache::get('end_date'),
                    'release_name' => $firstItem->release_name,
                    'upc_code' => $firstItem->upc_code,
                    'total_quantity' => $releaseData->sum('quantity'),
                    'total_earning' => Number::currency($releaseData->sum('earning'), 'USD', app()->getLocale()),
                    'countries' => $countryData,
                    'product' => $product ? [
                        'id' => $product->id,
                        'image' => $product->image
                    ] : null
                ];
            })->values()->toArray();

            return [
                'countries' => array_merge($countries, ['Others']),
                'releases' => $releases,
            ];
        });
    }

    public function platforms(): array
    {
        $cacheKey = 'platforms_analysis_' . md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $platforms = ['Spotify', 'Facebook', 'Youtube', 'Apple Music', 'Tiktok'];

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

                $firstItem = $releaseData->first();
                $product = $firstItem->product ?? null;

                return [
                    'start_date' => Cache::get('start_date'),
                    'end_date' => Cache::get('end_date'),
                    'release_name' => $firstItem->release_name,
                    'total_quantity' => $releaseData->sum('quantity'),
                    'total_earning' => Number::currency($releaseData->sum('earning'), 'USD', app()->getLocale()),
                    'platforms' => $platformData,
                    'product' => $product ? [
                        'id' => $product->id,
                        'image' => $product->image
                    ] : null
                ];
            })->values()->toArray();

            return [
                'platforms' => array_merge($platforms, ['Others']),
                'releases' => $releases,
            ];
        });
    }

    public function topArtists()
    {
        $cacheKey = 'top_artists_' . md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return $this->data->groupBy('artist_id')->values()->map(function ($artistData) {
                $artistEarnings = $artistData->sum('earning');
                $percentage = $this->totalEarnings > 0 ? ($artistEarnings / $this->totalEarnings) * 100 : 0;

                return [
                    'start_date' => Cache::get('start_date'),
                    'end_date' => Cache::get('end_date'),
                    'artist_id' => $artistData->first()->artist_id,
                    'artist_name' => $artistData->first()->artist_name,
                    'earning' => Number::currency($artistEarnings, 'USD', app()->getLocale()),
                    'streams' => $artistData->sum('quantity'),
                    'percentage' => round($percentage, 2),
                ];
            })->sortByDesc('percentage')
            ->take(10)
            ->values()
            ->toArray();
        });
    }

    public function topAlbums(): array
    {
        $cacheKey = 'top_albums_' . md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return $this->data->groupBy('release_name')->values()->map(function ($albumData) {
                $albumEarnings = $albumData->sum('earning');
                $percentage = $this->totalEarnings > 0 ? ($albumEarnings / $this->totalEarnings) * 100 : 0;
                $firstItem = $albumData->first();

                return [
                    'start_date' => Cache::get('start_date'),
                    'end_date' => Cache::get('end_date'),
                    'album_name' => $firstItem->release_name,
                    'artist_name' => $firstItem->artist_name,
                    'upc_code' => $firstItem->upc_code,
                    'streams' => $albumData->sum('quantity'),
                    'earning' => Number::currency($albumEarnings, 'USD', app()->getLocale()),
                    'percentage' => round($percentage, 2)
                ];
            })
            ->filter(function ($album) {
                return $album['earning'] > 0;
            })
            ->sortByDesc('percentage')
            ->take(10)
            ->values()
            ->toArray();
        });
    }

    public function topSongs(): array
    {
        $cacheKey = 'top_songs_' . md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return $this->data->groupBy('isrc_code')->values()->map(function ($songData) {
                $songEarnings = $songData->sum('earning');
                $percentage = $this->totalEarnings > 0 ? ($songEarnings / $this->totalEarnings) * 100 : 0;
                $firstItem = $songData->first();

                return [
                    'start_date' => Cache::get('start_date'),
                    'end_date' => Cache::get('end_date'),
                    'song_id' => $firstItem->song_id,
                    'song_name' => $firstItem->song_name ?? $firstItem->release_name,
                    'isrc_code' => $firstItem->isrc_code,
                    'artist_id' => $firstItem->artist_id,
                    'artist_name' => $firstItem->artist_name,
                    'earning' => Number::currency($songEarnings, 'USD', app()->getLocale()),
                    'streams' => $songData->sum('quantity'),
                    'percentage' => round($percentage, 2),
                ];
            })->sortByDesc('percentage')
            ->take(10)
            ->values()
            ->toArray();
        });
    }

    public function topLabels(): array
    {
        $cacheKey = 'top_labels_' . md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return $this->data->groupBy('label_id')->values()->map(function ($labelData) {
                $labelEarnings = $labelData->sum('earning');
                $percentage = $this->totalEarnings > 0 ? ($labelEarnings / $this->totalEarnings) * 100 : 0;
                $firstItem = $labelData->first();

                return [
                    'start_date' => Cache::get('start_date'),
                    'end_date' => Cache::get('end_date'),
                    'label_id' => $firstItem->label_id,
                    'label_name' => $firstItem->label_name,
                    'earning' => Number::currency($labelEarnings, 'USD', app()->getLocale()),
                    'percentage' => round($percentage, 2),
                ];
            })->sortByDesc('percentage')
            ->take(10)
            ->values()
            ->toArray();
        });
    }

    public function trendingAlbums(): array
    {
        $cacheKey = 'trending_albums_' . md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return $this->data->groupBy('upc_code')
                ->map(function ($items) {
                    $firstItem = $items->first();
                    $product = $firstItem->product;

                    return [
                        'start_date' => Cache::get('start_date'),
                        'end_date' => Cache::get('end_date'),
                        'platform' => $firstItem->platform,
                        'quantity' => $firstItem->quantity,
                        'release_name' => $firstItem->release_name,
                        'earning' => Number::currency($items->sum('earning'), 'USD', app()->getLocale()),
                    ];
                })
                ->sortByDesc('earning')
                ->take(10)
                ->values()
                ->toArray();
        });
    }

    public function earningFromSalesType(): array
    {
        $cacheKey = 'earning_from_sales_type_' . md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $salesTypeEarnings = $this->data->groupBy('sales_type')->map(function ($salesTypeData) {
                return [
                    'earning' => $salesTypeData->sum('earning'),
                    'quantity' => $salesTypeData->sum('quantity')
                ];
            });

            $sortedEarnings = $salesTypeEarnings->sortByDesc('earning');
            $topSalesTypes = $sortedEarnings->take(5);

            return $topSalesTypes->mapWithKeys(function ($data, $salesType) {
                return [
                    $salesType => [
                        'start_date' => Cache::get('start_date'),
                        'end_date' => Cache::get('end_date'),
                        'platform' => $salesType,
                        'quantity' => $data['quantity'],
                        'earning' => $data['earning'],
                        'percentage' => Number::percentage($this->totalEarnings > 0 ? ($data['earning'] / $this->totalEarnings) * 100 : 0),
                    ]
                ];
            })->toArray();
        });
    }

    public function earningFromYoutubeWithPremium(): array
    {
        $cacheKey = 'earning_from_youtube_premium_' . md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $youtubeData = $this->data->filter(function ($item) {
                return stripos($item->platform, 'Youtube') !== false && !empty($item->streaming_subscription_type);
            });

            if ($youtubeData->isEmpty()) {
                return [];
            }

            return $youtubeData
                ->groupBy('platform')
                ->map(function ($platformData, $platform) {
                    $result = ['name' => $platform];
                    
                    $platformData->groupBy('streaming_subscription_type')
                        ->each(function ($data, $type) use (&$result) {
                            $result[$type] = $data->sum('earning');
                        });
                    
                    return $result;
                })
                ->values()
                ->toArray();
        });
    }

    public function earningFromYoutube(): array
    {
        $cacheKey = 'earning_from_youtube_' . md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $platformEarnings = $this->data->filter(function ($item) {
                return stripos($item->platform, 'Youtube') !== false;
            })->groupBy('platform')->map(function ($platformData) {
                return $platformData->sum('earning');
            });

            $totalEarnings = $platformEarnings->sum();

            return $platformEarnings->mapWithKeys(function ($earning, $platform) use ($totalEarnings) {
                $percentage = $totalEarnings > 0 ? ($earning / $totalEarnings) * 100 : 0;
                return [
                    $platform => [
                        'earning' => Number::currency($earning, 'USD', app()->getLocale()),
                        'percentage' => round($percentage, 2),
                    ]
                ];
            })->toArray();
        });
    }

    public function earningFromCountries(): array
    {
        $cacheKey = 'earning_from_countries_' . md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $countryEarnings = $this->data->groupBy('country')->map(function ($countryData) {
                return [
                    'earning' => $countryData->sum('earning'),
                    'quantity' => $countryData->sum('quantity')
                ];
            });

            $sortedEarnings = $countryEarnings->sortByDesc('earning');
            $topCountries = $sortedEarnings->take(5);
            $otherEarnings = $sortedEarnings->slice(5)->sum('earning');

            $topCountries['others'] = [
                'earning' => $otherEarnings,
                'quantity' => $sortedEarnings->slice(5)->sum('quantity')
            ];

            return $topCountries->map(function ($data, $country) {
                return [
                    'start_date' => Cache::get('start_date'),
                    'end_date' => Cache::get('end_date'),
                    'country' => $country,
                    'quantity' => $data['quantity'],
                    'earning' => Number::currency($data['earning'], 'USD', app()->getLocale()),
                    'percentage' => Number::percentage($this->totalEarnings > 0 ? ($data['earning'] / $this->totalEarnings) * 100 : 0)
                ];
            })->values()->toArray();
        });
    }

    public function earningFromPlatforms(): array
    {
        $cacheKey = 'earning_from_platforms_' . md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $platformEarnings = $this->data->groupBy('platform')->map(function ($platformData) {
                return [
                    'earning' => $platformData->sum('earning'),
                    'quantity' => $platformData->sum('quantity')
                ];
            });

            $sortedEarnings = $platformEarnings->sortByDesc('earning');
            $topPlatforms = $sortedEarnings->take(5);
            $otherEarnings = $sortedEarnings->slice(5)->sum('earning');

            $topPlatforms['others'] = [
                'earning' => $otherEarnings,
                'quantity' => $sortedEarnings->slice(5)->sum('quantity')
            ];

            return $topPlatforms->map(function ($data, $platform) {
                return [
                    'start_date' => Cache::get('start_date'),
                    'end_date' => Cache::get('end_date'),
                    'platform' => $platform,
                    'quantity' => $data['quantity'],
                    'earning' => Number::currency($data['earning'], 'USD', app()->getLocale()),
                    'percentage' => Number::percentage($this->totalEarnings > 0 ? ($data['earning'] / $this->totalEarnings) * 100 : 0)
                ];
            })->values()->toArray();
        });
    }

    public function monthlyNetEarning(): array
    {
        $cacheKey = 'monthly_net_earnings_' . md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $platforms = ['Spotify', 'Apple Music', 'Youtube'];

            $calculateEarnings = function ($data) use ($platforms) {
                $totalEarnings = $data->sum('earning');
                $earnings = $data->groupBy('platform')->mapWithKeys(function ($platformData, $platform) use ($platforms, $totalEarnings) {
                    $sum = $platformData->sum('earning');
                    $percentage = $totalEarnings > 0 ? ($sum / $totalEarnings) * 100 : 0;
                    if (in_array($platform, $platforms)) {
                        return [
                            $platform => [
                                'earning_num' => $sum,
                                'earning' => Number::currency($sum, 'USD', app()->getLocale()),
                                'percentage' => round($percentage, 2),
                            ]
                        ];
                    }
                    return [
                        'other' => [
                            'earning_num' => $sum,
                            'earning' => Number::currency($sum, 'USD', app()->getLocale()),
                            'percentage' => round($percentage, 2),
                        ]
                    ];
                });

                foreach ($platforms as $platform) {
                    if (!isset($earnings[$platform])) {
                        $earnings[$platform] = [
                            'earning_num' => 0,
                            'earning' => Number::currency(0, 'USD', app()->getLocale()),
                            'percentage' => 0,
                        ];
                    }
                }

                if (!isset($earnings['other'])) {
                    $earnings['other'] = [
                        'earning_num' => 0,
                        'earning' => Number::currency(0, 'USD', app()->getLocale()),
                        'percentage' => 0,
                    ];
                }

                return array_merge($earnings->toArray(), [
                    'total' => Number::currency($totalEarnings, 'USD', app()->getLocale()),
                ]);
            };

            $items = $this->groupedData->map(fn($monthData) => $calculateEarnings($monthData));
            $total = $calculateEarnings($this->data);

            return [
                'total' => $total,
                'items' => $items->toArray(),
            ];
        });
    }

    public function spotifyDiscoveryModeEarnings(): array
    {
        $cacheKey = 'spotify_discovery_mode_earnings_' . md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
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
                        'earning_percentage' => $net > 0 ? ($net / $earning) * 100 : 0,
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
        });
    }

    public function youtubeEarningsBySubscriptionType(): array
    {
        $cacheKey = 'youtube_earnings_by_subscription_type_' . md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return $this->data
                ->filter(function ($item) {
                    return stripos($item->platform, 'Youtube') !== false;
                })
                ->groupBy('platform')
                ->map(function ($platformData, $platform) {
                    $subscriptionEarnings = $platformData
                        ->groupBy('streaming_subscription_type')
                        ->map(function ($data) {
                            return $data->sum('earning');
                        })
                        ->toArray();

                    return array_merge(
                        ['name' => $platform],
                        $subscriptionEarnings
                    );
                })
                ->values()
                ->toArray();
        });
    }
}
