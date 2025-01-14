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

    public function platforms(): array
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
                'start_date' => Cache::get('start_date'),
                'end_date' => Cache::get('end_date'),
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

    public function topArtists()
    {
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
        })->sortByDesc('earning')->toArray();
    }

    public function topAlbums(): array
    {
        return $this->data->groupBy('release_name')->values()->map(function ($albumData) {
            $albumEarnings = $albumData->sum('earning');
            $percentage = $this->totalEarnings > 0 ? ($albumEarnings / $this->totalEarnings) * 100 : 0;
            $firstItem = $albumData->first();
            $artistName = $firstItem->artist_name;

            return [
                'start_date' => Cache::get('start_date'),
                'end_date' => Cache::get('end_date'),
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

    public function topSongs(): array
    {
        return $this->data->groupBy('isrc_code')->values()->map(function ($songData) {
            $songEarnings = $songData->sum('earning');
            $percentage = $this->totalEarnings > 0 ? ($songEarnings / $this->totalEarnings) * 100 : 0;
            $firstItem = $songData->first();

            return [
                'start_date' => Cache::get('start_date'),
                'end_date' => Cache::get('end_date'),
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

    public function topLabels(): array
    {
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
        })->sortByDesc('earning')->values()->toArray();
    }

    public function trendingAlbums(): array
    {
        return $this->data->groupBy('upc_code')->take(10)->values()->map(function ($items) {
            $firstItem = $items->first();
            $product = $firstItem->product;

            return [
                'start_date' => Cache::get('start_date'),
                'end_date' => Cache::get('end_date'),
                'platform' => $firstItem->platform,
                'quantity' => $firstItem->quantity,
                'earning' => Number::currency($items->sum('earning'), 'USD', app()->getLocale()),
                'product_name' => $product->album_name ?? $firstItem->release_name,
                'product_id' => $product->id ?? '',
            ];
        })->toArray();
    }

    public function earningFromSalesType(): array
    {
        $salesTypeEarnings = $this->data->groupBy('sales_type')->map(function ($salesTypeData) {
            return $salesTypeData->sum('earning');
        });

        $sortedEarnings = $salesTypeEarnings->sortDesc();

        $topSalesTypes = $sortedEarnings->take(5);

        return $topSalesTypes->mapWithKeys(function ($earning, $salesType) {
            return [
                $salesType =>
                    [
                        'start_date' => Cache::get('start_date'),
                        'end_date' => Cache::get('end_date'),
                        'platform' => $salesType,
                        'quantity' => $this->data->where('sales_type', $salesType)->sum('quantity'),
                        'earning' => $earning,
                        'percentage' => Number::percentage($this->totalEarnings > 0 ? ($earning / $this->totalEarnings) * 100 : 0),
                    ]
            ];
        })->toArray();
    }

    public function earningFromYoutubeWithPremium(): array
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

    public function earningFromYoutube(): array
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

    public function earningFromCountries(): array
    {
        $countryEarnings = $this->data->groupBy('country')->map(function ($countryData) {
            return $countryData->sum('earning');
        });

        $sortedEarnings = $countryEarnings->sortDesc();

        $topCountries = $sortedEarnings->take(5);
        $otherEarnings = $sortedEarnings->slice(5)->sum();

        $topCountries['others'] = $otherEarnings;

        return $topCountries->mapWithKeys(function ($earning, $country) {
            return [
                $country => [
                    'start_date' => Cache::get('start_date'),
                    'end_date' => Cache::get('end_date'),
                    'country' => $country,
                    'earning' => $earning,
                    'quantity' => $this->data->where('country', $country)->sum('quantity'),
                    'percentage' => Number::percentage($this->totalEarnings > 0 ? ($earning / $this->totalEarnings) * 100 : 0),
                ]
            ];
        })->toArray();
    }

    public function earningFromPlatforms(): array
    {
        $platformEarnings = $this->data->groupBy('platform')->map(function ($platformData) {
            return $platformData->sum('earning');
        });

        $sortedEarnings = $platformEarnings->sortDesc();

        $topPlatforms = $sortedEarnings->take(5);
        $otherEarnings = $sortedEarnings->slice(5)->sum();

        $topPlatforms['others'] = $otherEarnings;

        return $topPlatforms->mapWithKeys(function ($earning, $platform) {
            return [
                $platform => [
                    'start_date' => Cache::get('start_date'),
                    'end_date' => Cache::get('end_date'),
                    'platform' => $platform,
                    'quantity' => $this->data->where('platform', $platform)->sum('quantity'),
                    'earning' => $earning,
                    'percentage' => Number::percentage($this->totalEarnings > 0 ? ($earning / $this->totalEarnings) * 100 : 0),
                ]
            ];
        })->toArray();
    }

    public function spotifyDiscoveryModeEarnings(): array
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

//    public function monthlyNetEarning(): array
//    {
//        $platforms = ['Spotify', 'Amazon', 'Youtube'];
//
//        $calculateEarnings = function ($data) use ($platforms) {
//            $totalEarnings = $data->sum('earning');
//            $earnings = $data->groupBy('platform')->mapWithKeys(function ($platformData, $platform) use (
//                $platforms,
//                $totalEarnings
//            ) {
//                $sum = $platformData->sum('earning');
//                $percentage = $totalEarnings > 0 ? ($sum / $totalEarnings) * 100 : 0;
//                if (in_array($platform, $platforms)) {
//                    return [
//                        $platform => [
//                            'earning' => Number::currency($sum, 'USD', app()->getLocale()),
//                            'percentage' => round($percentage, 2),
//                        ]
//                    ];
//                }
//                return [
//                    'other' => [
//                        'earning' => Number::currency($sum, 'USD', app()->getLocale()),
//                        'percentage' => round($percentage, 2),
//                    ]
//                ];
//            });
//
//            foreach ($platforms as $platform) {
//                if (!isset($earnings[$platform])) {
//                    $earnings[$platform] = [
//                        'earning' => Number::currency(0, 'USD', app()->getLocale()),
//                        'percentage' => 0,
//                    ];
//                }
//            }
//
//            if (!isset($earnings['other'])) {
//                $earnings['other'] = [
//                    'earning' => Number::currency(0, 'USD', app()->getLocale()),
//                    'percentage' => 0,
//                ];
//            }
//
//            return $earnings;
//        };
//        $items = $this->groupedData->map(fn($monthData) => $calculateEarnings($monthData));
//
//        $total = $calculateEarnings($this->data);
//
//        return [
//            'total' => $total->toArray(),
//            'items' => $items->toArray(),
//        ];
//    }

    public function monthlyNetEarning(): array
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
    }
}
