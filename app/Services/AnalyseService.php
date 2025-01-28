<?php

namespace App\Services;

use App\Models\Earning;
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
        $cacheKey = 'countries_analysis_'.md5($this->data->pluck('id')->implode(','));

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $countries = ['Turkey', 'United states', 'United kingdom', 'Portugal', 'Spain'];

            // 1. Ülke bazlı toplam kazanç ve miktar hesaplama
            $countryEarnings = Earning::selectRaw('country, SUM(earning) as total_earning, SUM(quantity) as total_quantity')
                ->groupBy('country')
                ->orderBy('total_earning', 'desc')
                ->get();

            // 2. Verileri verilen ülkelere ve "Others" grubuna ayırma
            $topCountries = $countryEarnings->filter(function ($item) use ($countries) {
                return in_array($item->country, $countries);
            });

            $otherCountries = $countryEarnings->filter(function ($item) use ($countries) {
                return !in_array($item->country, $countries);
            });

            $otherEarnings = [
                'country' => 'Others',
                'total_earning' => $otherCountries->sum('total_earning'),
                'total_quantity' => $otherCountries->sum('total_quantity'),
            ];

            // Verilerin birleştirilmesi
            $allCountries = $topCountries->merge([$otherEarnings]);

            // 3. Release bazlı kazançlar (ülkelerle ilişkilendirilmiş bir alt rapor)
            $releaseEarnings = Earning::selectRaw('release_name, country, SUM(earning) as total_earning, SUM(quantity) as total_quantity')
                ->groupBy(['release_name', 'country'])
                ->orderBy('total_earning', 'desc')
                ->get();

            // Release verilerini işleme
            $releaseData = $releaseEarnings->groupBy('release_name')->map(function ($releaseGroup, $releaseName) use (
                $countries
            ) {
                $countryData = [];
                $otherEarnings = 0;
                $otherQuantity = 0;

                foreach ($releaseGroup as $row) {
                    if (in_array($row->country, $countries)) {
                        $countryData[strtolower($row->country)] = [
                            'earning' => Number::currency($row->total_earning, 'USD', app()->getLocale()),
                            'percentage' => 0, // Yüzdelik için toplam kazanç gerekiyor
                            'quantity' => $row->total_quantity,
                        ];
                    } else {
                        $otherEarnings += $row->total_earning;
                        $otherQuantity += $row->total_quantity;
                    }
                }

                $countryData['others'] = [
                    'earning' => Number::currency($otherEarnings, 'USD', app()->getLocale()),
                    'percentage' => 0, // Yüzdelik için toplam yine eklenir.
                    'quantity' => $otherQuantity,
                ];

                return [
                    'release_name' => $releaseName,
                    'countries' => $countryData,
                ];
            })->values();

            return [
                'countries' => $allCountries->toArray(),
                'releases' => $releaseData->toArray(),
            ];
        });
    }

    public function platforms(): array
    {
        $cacheKey = 'platforms_analysis_'.md5($this->data->pluck('id')->implode(','));

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            // 1. Platformlar bazında toplam gelir ve miktarı alıyoruz.
            $platformEarnings = Earning::selectRaw('platform, SUM(earning) as total_earning, SUM(quantity) as total_quantity')
                ->groupBy('platform')
                ->orderBy('total_earning', 'desc')
                ->get();

            // Platform listesini belirliyoruz. (Sabit olanlar + Dinamik "Other" kategorisi)
            $platforms = ['Spotify', 'Facebook', 'Youtube', 'Apple Music', 'Tiktok'];

            // 2. Top 5 platformu ayırıyoruz.
            $topPlatforms = $platformEarnings->filter(function ($item) use ($platforms) {
                return in_array($item->platform, $platforms);
            });

            // 3. Diğer platformları "Others" altında topluyoruz.
            $otherPlatforms = $platformEarnings->filter(function ($item) use ($platforms) {
                return !in_array($item->platform, $platforms);
            });

            $otherEarnings = [
                'platform' => 'Other',
                'total_earning' => $otherPlatforms->sum('total_earning'),
                'total_quantity' => $otherPlatforms->sum('total_quantity'),
            ];

            // Platform ve "Other" verilerini birleştiriyoruz.
            $platformEarnings = $topPlatforms->merge([$otherEarnings]);

            // 4. Release bazlı rapor oluşturma
            $releaseEarnings = Earning::selectRaw('release_name, SUM(earning) as release_earning, SUM(quantity) as total_quantity')
                ->groupBy('release_name')
                ->orderBy('release_earning', 'desc')
                ->get();

            return [
                'platforms' => $platformEarnings->toArray(),
                'releases' => $releaseEarnings->toArray(),
            ];
        });
    }

    public function topArtists(): array
    {
        $cacheKey = 'top_artists_'.md5($this->data->pluck('id')->implode(','));

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return Earning::selectRaw('artist_id, artist_name, SUM(earning) as total_earning, SUM(quantity) as total_quantity')
                ->groupBy('artist_id', 'artist_name')
                ->orderBy('total_earning', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($artistData) {
                    return [
                        'start_date' => Cache::get('start_date'),
                        'end_date' => Cache::get('end_date'),
                        'artist_id' => $artistData->artist_id,
                        'artist_name' => $artistData->artist_name,
                        'earning' => Number::currency($artistData->total_earning, 'USD', app()->getLocale()),
                        'streams' => $artistData->total_quantity,
                        'percentage' => round(($artistData->total_earning / $this->totalEarnings) * 100, 2),
                    ];
                });
        })->toArray();
    }

    public function topAlbums(): array
    {
        $cacheKey = 'top_albums_'.md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, 60 * 60, function () {
            $filteredData = $this->data->filter(function ($item) {
                return !empty($item->upc_code);
            });

            $groupedData = $filteredData->groupBy('upc_code')->values();

            $mappedData = $groupedData->map(function ($albumData) {
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
                    'raw_earning' => $albumEarnings,
                    'percentage' => round($percentage, 2)
                ];
            });

            $result = $mappedData
                ->sortByDesc('raw_earning')
                ->take(10)
                ->values()
                ->map(function ($item) {
                    unset($item['raw_earning']);
                    return $item;
                })
                ->toArray();

            return $result;
        });
    }

    public function topSongs(): array
    {
        $cacheKey = 'top_songs_'.md5($this->data->pluck('id')->implode(','));
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
        $cacheKey = 'top_labels_'.md5($this->data->pluck('id')->implode(','));
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
        $cacheKey = 'trending_albums_'.$this->data->pluck('id')->implode(',');

        // Tarih verilerini cache üzerinden bir kez al, grup başına çağrı yerine paylaşılmış sabit değerlerde kullan
        $startDate = Cache::get('start_date', 'default_start_date');
        $endDate = Cache::get('end_date', 'default_end_date');

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($startDate, $endDate) {
            return $this->data
                ->groupBy('upc_code') // Veriyi `upc_code` bazında gruplar
                ->map(function ($items) use ($startDate, $endDate) {
                    $firstItem = $items->first();
                    $totalEarnings = $items->sum('earning'); // Toplam kazanç

                    return [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'platform' => $firstItem->platform,
                        'quantity' => $items->sum('quantity'), // Tüm gruba ait toplam adet
                        'release_name' => $firstItem->release_name,
                        'earning' => Number::currency($totalEarnings, 'USD', app()->getLocale()), // Formatlama
                    ];
                })
                ->sortByDesc('earning') // En yüksek kazanca göre sırala
                ->take(10) // İlk 10 elemanı al
                ->values() // Koleksiyon sırasını yeniden sıfırla (diziyi indeksle)
                ->toArray(); // Diziyi döndür
        });
    }

    public function earningFromSalesType(): array
    {
        $cacheKey = 'earning_from_sales_type_'.md5($this->data->pluck('id')->implode(','));
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
        $cacheKey = 'earning_from_youtube_premium_'.md5($this->data->pluck('id')->implode(','));
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
        $cacheKey = 'earning_from_youtube_'.md5($this->data->pluck('id')->implode(','));
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
        $startDate = Cache::get('start_date');
        $endDate = Cache::get('end_date');

        $countryData = $this->data
            ->groupBy('country')
            ->map(function ($items) use ($startDate, $endDate) {
                $totalEarning = $items->sum('earning');
                $totalQuantity = $items->sum('quantity');
                $percentage = $this->calculatePercentage($totalEarning);

                return [
                    'country' => $items->first()->country,
                    'earning' => $totalEarning,
                    'quantity' => $totalQuantity,
                    'percentage' => $percentage,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ];
            })
            ->sortByDesc('earning');

        // İlk 5 ülkeyi al
        $topCountries = $countryData->take(5);

        // Geri kalanları "Others" olarak grupla
        $otherCountries = $countryData->slice(5);
        if ($otherCountries->isNotEmpty()) {
            $topCountries->put('Others', [
                'country' => 'Others',
                'earning' => $otherCountries->sum('earning'),
                'quantity' => $otherCountries->sum('quantity'),
                'percentage' => $this->calculatePercentage($otherCountries->sum('earning')),
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
        }

        return $topCountries->values()->toArray();
    }

    public function earningFromPlatforms(): array
    {
        $startDate = Cache::get('start_date');
        $endDate = Cache::get('end_date');

        $platformData = $this->data
            ->groupBy('platform')
            ->map(function ($items) use ($startDate, $endDate) {
                $totalEarning = $items->sum('earning');
                $totalQuantity = $items->sum('quantity');
                $percentage = $this->calculatePercentage($totalEarning);

                return [
                    'platform' => $items->first()->platform,
                    'earning' => $totalEarning,
                    'quantity' => $totalQuantity,
                    'percentage' => $percentage,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ];
            })
            ->sortByDesc('earning');

        // İlk 5 platformu al
        $topPlatforms = $platformData->take(5);

        // Geri kalanları "Others" olarak grupla
        $otherPlatforms = $platformData->slice(5);
        if ($otherPlatforms->isNotEmpty()) {
            $topPlatforms->put('Others', [
                'platform' => 'Others',
                'earning' => $otherPlatforms->sum('earning'),
                'quantity' => $otherPlatforms->sum('quantity'),
                'percentage' => $this->calculatePercentage($otherPlatforms->sum('earning')),
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
        }

        return $topPlatforms->values()->toArray();
    }

    protected function calculatePercentage($value): float
    {
        $total = $this->data->sum('earning');
        if ($total > 0) {
            return round(($value / $total) * 100, 2);
        }
        return 0;
    }

    public function monthlyNetEarning(): array
    {
        $cacheKey = 'monthly_net_earnings_'.md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $platforms = ['Spotify', 'Apple Music', 'Youtube'];

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
        $cacheKey = 'spotify_discovery_mode_earnings_'.md5($this->data->pluck('id')->implode(','));
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
        $cacheKey = 'youtube_earnings_by_subscription_type_'.md5($this->data->pluck('id')->implode(','));
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
