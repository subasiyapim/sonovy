<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Log;


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
        $cacheKey = 'platforms_analysis_'.md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $platforms = ['Spotify', 'Facebook', 'Youtube', 'Apple Music', 'Tiktok'];

            $platformEarnings = $this->data->groupBy('platform')->map(function ($platformData, $platform) use (
                $platforms
            ) {
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
        $cacheKey = 'top_artists_'.md5($this->data->pluck('id')->implode(','));
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
        $cacheKey = 'top_albums_'.md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, 60 * 60, function () {
            Log::info('TopAlbums başlangıç', [
                'total_records' => $this->data->count(),
                'total_earnings' => $this->totalEarnings,
                'has_earnings' => $this->data->where('earning', '>', 0)->count(),
                'unique_upcs' => $this->data->pluck('upc_code')->unique()->count(),
                'sample_data' => $this->data->take(1)->toArray()
            ]);

            $filteredData = $this->data->filter(function ($item) {
                return !empty($item->upc_code);
            });

            $groupedData = $filteredData->groupBy('upc_code')->values();

            Log::info('TopAlbums gruplandırma sonrası', [
                'total_groups' => $groupedData->count(),
                'groups_with_earnings' => $groupedData->filter(function ($group) {
                    return $group->sum('earning') > 0;
                })->count(),
                'sample_group' => $groupedData->take(1)->toArray()
            ]);

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

            Log::info('TopAlbums dönüşüm sonrası', [
                'total_mapped' => $mappedData->count(),
                'sample_mapped' => $mappedData->take(1)->toArray()
            ]);

            $result = $mappedData
                ->sortByDesc('raw_earning')
                ->take(10)
                ->values()
                ->map(function ($item) {
                    unset($item['raw_earning']);
                    return $item;
                })
                ->toArray();

            Log::info('TopAlbums final', [
                'total_results' => count($result),
                'has_results' => !empty($result),
                'results' => $result
            ]);

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
        $cacheKey = 'trending_albums_'.md5($this->data->pluck('id')->implode(','));
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
            // Youtube içeren platformları filtrele
            $youtubeData = $this->data->filter(function ($item) {
                return stripos($item->platform, 'Youtube') !== false;
            });

            if ($youtubeData->isEmpty()) {
                return [];
            }

            return $youtubeData
                ->groupBy('platform')
                ->map(function ($platformData, $platform) {
                    $result = ['name' => $platform];

                    // Premium ve Freemium verilerini hesapla
                    $premiumData = $platformData->where('streaming_subscription_type', 'Premium');
                    $freemiumData = $platformData->where('streaming_subscription_type', 'Freemium');

                    // Others - Premium ve Freemium dışındaki tüm veriler
                    $othersData = $platformData->filter(function ($item) {
                        return !in_array($item->streaming_subscription_type, ['Premium', 'Freemium']);
                    });

                    // Her grubu ekle (0 bile olsa)
                    $premiumSum = $premiumData->sum('earning');
                    $freemiumSum = $freemiumData->sum('earning');
                    $othersSum = $othersData->sum('earning');

                    // Platform toplamını hesapla
                    $platformTotal = $premiumSum + $freemiumSum + $othersSum;

                    // Her grubun oranını hesapla
                    $result['Premium'] = $premiumSum;
                    $result['Freemium'] = $freemiumSum;
                    $result['Others'] = $othersSum;
                    $result['total'] = $platformTotal;

                    // Oranları hesapla ve ekle (yüzde olarak)
                    $result['Premium_percentage'] = $platformTotal > 0 ? round(($premiumSum / $platformTotal) * 100, 2) : 0;
                    $result['Freemium_percentage'] = $platformTotal > 0 ? round(($freemiumSum / $platformTotal) * 100, 2) : 0;
                    $result['Others_percentage'] = $platformTotal > 0 ? round(($othersSum / $platformTotal) * 100, 2) : 0;

                    return $result;
                })
                ->filter(function ($item) {
                    // En az bir değeri 0'dan büyük olan platformları göster
                    return $item['Premium'] > 0 || $item['Freemium'] > 0 || $item['Others'] > 0;
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
            $platforms = ['Spotify', 'Apple', 'Youtube'];
            $totalAllEarnings = $this->data->sum('earning');

            $calculateEarnings = function ($data) use ($platforms, $totalAllEarnings) {
                $totalEarnings = $data->sum('earning');
                $earnings = collect();

                // Platform gruplarını oluştur
                foreach ($platforms as $platformName) {
                    $platformData = $data->filter(function ($item) use ($platformName) {
                        return stripos($item->platform, $platformName) !== false;
                    });

                    $sum = $platformData->sum('earning');
                    $percentage = $totalEarnings > 0 ? ($sum / $totalEarnings) * 100 : 0;

                    $earnings[$platformName] = [
                        'earning_num' => $sum,
                        'earning' => Number::currency($sum, 'USD', app()->getLocale()),
                        'percentage' => round($percentage, 2)
                    ];
                }

                // Diğer platformları hesapla
                $otherData = $data->filter(function ($item) use ($platforms) {
                    foreach ($platforms as $platformName) {
                        if (stripos($item->platform, $platformName) !== false) {
                            return false;
                        }
                    }
                    return true;
                });

                $otherSum = $otherData->sum('earning');
                $otherPercentage = $totalEarnings > 0 ? ($otherSum / $totalEarnings) * 100 : 0;

                $earnings['other'] = [
                    'earning_num' => $otherSum,
                    'earning' => Number::currency($otherSum, 'USD', app()->getLocale()),
                    'percentage' => round($otherPercentage, 2)
                ];

                $monthPercentage = $totalAllEarnings > 0 ? round(($totalEarnings / $totalAllEarnings) * 100, 2) : 0;

                return array_merge($earnings->toArray(), [
                    'total' => Number::currency($totalEarnings, 'USD', app()->getLocale()),
                    'total_num' => $totalEarnings,
                    'month_percentage' => $monthPercentage
                ]);
            };

            // Tarihleri Carbon nesnelerine dönüştürüp sıralama yapacağız
            $sortedData = $this->groupedData->map(function ($monthData, $monthKey) {
                return [
                    'date' => Carbon::createFromLocaleFormat('F Y', app()->getLocale(), $monthKey),
                    'data' => $monthData
                ];
            })->sortByDesc(function ($item) {
                return $item['date']->timestamp;
            });

            $items = $sortedData->mapWithKeys(function ($item) use ($calculateEarnings) {
                return [$item['date']->locale(app()->getLocale())->translatedFormat('F Y') => $calculateEarnings($item['data'])];
            });

            $total = $calculateEarnings($this->data);

            // En yüksek aylık toplam değeri bul
            $maxMonthlyTotal = $items->max(function ($monthData) {
                return $monthData['total_num'];
            });

            // Y ekseni için uygun aralıkları hesapla
            $length = strlen(floor($maxMonthlyTotal));
            $divider = pow(10, $length - 1);
            $yAxisMax = ceil($maxMonthlyTotal / $divider) * $divider;

            $interval = $yAxisMax / 4; // 4 aralık olacak şekilde böl

            $yAxis = [
                'max' => $yAxisMax,
                'interval' => $interval,
                'values' => [
                    $yAxisMax,
                    round($yAxisMax * 0.75),
                    round($yAxisMax * 0.5),
                    round($yAxisMax * 0.25),
                    0
                ]
            ];

            // Series verilerini hazırla
            $series = collect($platforms)->concat(['other'])->map(function ($platform) use ($items) {
                return [
                    'name' => $platform,
                    'data' => $items->map(function ($monthData) use ($platform) {
                        return $monthData[$platform]['earning_num'] ?? 0;
                    })->values()->toArray()
                ];
            })->values()->toArray();

            return [
                'total' => $total,
                'items' => $items->toArray(),
                'series' => $series,
                'categories' => $items->keys()->toArray(),
                'yAxis' => $yAxis
            ];
        });
    }

    public function spotifyDiscoveryModeEarnings(): array
    {
        $cacheKey = 'spotify_discovery_mode_earnings_'.md5($this->data->pluck('id')->implode(','));
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            // Önce tarihleri Carbon nesnelerine dönüştürüp sıralama yapacağız
            $sortedData = $this->groupedData->map(function ($monthData, $monthKey) {
                return [
                    'date' => Carbon::createFromLocaleFormat('F Y', app()->getLocale(), $monthKey),
                    'data' => $monthData
                ];
            })->sortByDesc(function ($item) {
                return $item['date']->timestamp;
            });

            $items = $sortedData->mapWithKeys(function ($item) {
                $monthData = collect($item['data']);
                $month = $item['date']->locale(app()->getLocale())->translatedFormat('F Y');

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
