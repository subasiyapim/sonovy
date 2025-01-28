<?php

namespace App\Services;

use App\Enums\ProductTypeEnum;
use App\Exports\FakerEarningReport;
use App\Models\EarningReport;
use App\Models\Product;
use App\Models\System\Country;
use App\Models\Platform;
use App\Models\Earning;
use App\Models\EarningReportFile;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class EarningService
{
    public static function uploadFile($file)
    {
        $reservedReportFile = EarningReportFile::create([
            'is_processed' => false,
        ]);

        $reservedReportFile->addMedia($file)
            ->toMediaCollection('reserved_report_files');

        return $reservedReportFile;
    }

    protected static function hasAdmin()
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        $roles = $user->roles->pluck('code')->toArray();

        return in_array('admin', $roles);
    }

    public static function balance($user_id = null)
    {
        if (App::runningInConsole()) {
            return Earning::sum('earning');
        } else {
            $earned = self::getEarnedTotal($user_id);

            $payments = PaymentService::getTotalPayment($user_id);

            $advances = PaymentService::getAdvancePaymentTotal($user_id);

            return ($earned + $advances) - $payments;
        }
    }

    protected static function getEarnedTotal($user_id = null)
    {
        $twoMonthsAgo = now()->subMonths(2);

        $earning_total = Earning::when($user_id, function ($query) use ($twoMonthsAgo, $user_id) {
            $query->where('user_id', $user_id)
                ->where('sales_date', '<', $twoMonthsAgo)->sum('earning');
        }, function ($query) use ($twoMonthsAgo) {
            $query->whereHas('report', function ($query) use ($twoMonthsAgo) {
                $query->where('sales_date', '<', $twoMonthsAgo);
            })->where('user_id', Auth::id())->sum('earning');
        })->get();


        return $earning_total->sum('earning');
    }


    public static function balanceReport(): array
    {
        // Bu ayın kazancı
        $currentMonthEarnings = Earning::when(self::hasAdmin(), function ($query) {
            $query->where('sales_date', '>=', Carbon::now()->startOfMonth());
        }, function ($query) {
            $query->where('sales_date', '>=', Carbon::now()->startOfMonth())
                ->where('user_id', Auth::id());
        })->sum('earning');

        // Geçen ayın kazancı
        $previousMonthEarnings = Earning::when(self::hasAdmin(), function ($query) {
            $query->where('sales_date', '>=', Carbon::now()->subMonth()->startOfMonth())
                ->where('sales_date', '<', Carbon::now()->startOfMonth());
        }, function ($query) {
            $query->where('sales_date', '>=', Carbon::now()->subMonth()->startOfMonth())
                ->where('sales_date', '<', Carbon::now()->startOfMonth())
                ->where('user_id', Auth::id());
        })->sum('earning');

        // Bu yılın kazancı
        $currentYearEarnings = Earning::when(self::hasAdmin(), function ($query) {
            $query->where('sales_date', '>=', Carbon::now()->startOfYear());
        }, function ($query) {
            $query->where('sales_date', '<=', Carbon::now()->endOfYear())
                ->where('user_id', Auth::id());
        })->sum('earning');
        // Geçen yılın kazancı
        $previousYearEarnings = Earning::when(self::hasAdmin(), function ($query) {
            $query->where('sales_date', '>=', Carbon::now()->subYear()->startOfYear())
                ->where('sales_date', '<', Carbon::now()->startOfYear());
        }, function ($query) {
            $query->where('sales_date', '>=', Carbon::now()->subYear()->startOfYear())
                ->where('sales_date', '<', Carbon::now()->startOfYear())
                ->where('user_id', Auth::id());
        })->sum('earning');

        // Son 1 yılın kazancı
        $lastYearEarnings = Earning::when(self::hasAdmin(), function ($query) {
            $query->where('sales_date', '>=', Carbon::now()->subYear()->startOfYear())
                ->where('sales_date', '<', Carbon::now()->startOfYear());
        }, function ($query) {
            $query->where('sales_date', '>=', Carbon::now()->subYear()->startOfYear())
                ->where('sales_date', '<', Carbon::now()->startOfYear())
                ->where('user_id', Auth::id());
        })->sum('earning');

        // Üyelik başlangıcından itibaren toplam kazanç
        $totalEarnings = Earning::when(self::hasAdmin(), function ($query) {
            $query->sum('earning');
        }, function ($query) {
            $query->where('user_id', Auth::id())->sum('earning');
        })->sum('earning');

        // Yüzde değişim hesaplamaları
        $monthChange = $previousMonthEarnings > 0 ? (($currentMonthEarnings - $previousMonthEarnings) / $previousMonthEarnings) * 100 : 0;
        $yearChange = $previousYearEarnings > 0 ? (($currentYearEarnings - $previousYearEarnings) / $previousYearEarnings) * 100 : 0;

        $lastYearChange = $previousYearEarnings > 0 ? (($lastYearEarnings - $previousYearEarnings) / $previousYearEarnings) * 100 : 0;

        // Sonuçlar
        return [
            'current_month' => [
                'amount' => number_format($currentMonthEarnings, 2),
                'change' => number_format($monthChange, 2),
            ],
            'last_year' => [
                'amount' => number_format($lastYearEarnings, 2),
                'change' => number_format($lastYearChange, 2),
            ],
            'current_year' => [
                'amount' => number_format($currentYearEarnings, 2),
                'change' => number_format($yearChange, 2),
            ],
            'total' => [
                'amount' => number_format($totalEarnings, 2),
            ],
        ];
    }

    public static function monthlyPlatformReport($start_date = null, $end_date = null)
    {
        $startDate = $start_date ?? Carbon::now()->startOfMonth();
        $endDate = $end_date ?? Carbon::now();

        $data = Earning::select('platform', DB::raw('count(*) as total'))
            ->whereBetween('sales_date', [$startDate, $endDate])
            ->groupBy('platform')
            ->get();

        $series = $data->pluck('total')->toArray();
        $labels = $data->pluck('platform')->toArray();

        return [
            'series' => $series,
            'labels' => $labels,
        ];
    }

    public static function monthlyEarningReport($start_date = null, $end_date = null)
    {
        $startDate = $start_date ?? Carbon::now()->startOfMonth();
        $endDate = $end_date ?? Carbon::now();

        $data = Earning::with('report')
            ->whereBetween('sales_date', [$startDate, $endDate])
            ->get();

        $groupedData = $data->whereNotNull('report.sales_type')->groupBy('report.sales_type');

        $series = $groupedData->map(function ($item) {
            return $item->sum('earning');
        })->values()->toArray();

        $labels = $groupedData->map(function ($item) {
            return $item->first()->report->sales_type;
        })->values()->toArray();

//        $labels = array_map(function ($label) {
//            return match ($label) {
//                'Download' => 'İndirme',
//                'Stream' => 'Dinleme',
//                'PLATFORM PROMOTION' => 'Platform Promosyonu',
//                'Creation' => 'Yaratım',
//                default => $label,
//            };
//        }, $labels);

        return [
            'series' => $series,
            'labels' => $labels,
        ];
    }

    //Aylık dinleme raporu
    public static function monthlyListeningReport($start_date = null, $end_date = null)
    {
        $startDate = $start_date ? Carbon::parse($start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $end_date ? Carbon::parse($end_date)->endOfDay() : Carbon::now()->endOfDay();

        // Fetch all earnings with their reports within the date range
        $earnings = Earning::with('report')
            ->whereBetween('sales_date', [$startDate, $endDate])
            ->get();

        // Initialize collections
        $dates = collect();
        $dailyListeningCounts = collect();

        // Process data
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->format('Y-m-d');
            $dates->push($dateString);

            $dailyListenings = $earnings->filter(function ($earning) use ($dateString) {
                return $earning->sales_date->format('Y-m-d') == $dateString && $earning->report->sales_type == 'Stream';
            })->sum('report.quantity');

            $dailyListeningCounts->push($dailyListenings);
        }

        $totalListening = number_format($dailyListeningCounts->sum(), 0, ',', '.');

        // Calculate total downloads
        $totalDownloadingCount = Earning::with('report')
            ->whereBetween('sales_date', [$startDate, $endDate])
            ->whereHas('report', function ($query) {
                $query->where('sales_type', 'Download');
            })
            ->count();

        $albumDownloadCount = Earning::with('report')
            ->whereBetween('sales_date', [$startDate, $endDate])
            ->whereHas('report', function ($query) {
                $query->where('release_type', 'Album')
                    ->where('sales_type', 'Download');
            })
            ->count();

        $videoDownloadCount = Earning::with('report')
            ->whereBetween('sales_date', [$startDate, $endDate])
            ->whereHas('report', function ($query) {
                $query->where('release_type', 'Video')
                    ->where('sales_type', 'Download');
            })
            ->count();

        return [
            'dates' => $dates->toArray(),
            'dailyListeningCounts' => $dailyListeningCounts->toArray(),
            'totalListening' => $totalListening,
            'totalDownloads' => $totalDownloadingCount,
            'albumDownloadCount' => $albumDownloadCount,
            'videoDownloadCount' => $videoDownloadCount,
        ];
    }

    public static function getMonthlyTrendRequest($trend = null, $start_date = null, $end_date = null)
    {
        $startDate = Carbon::parse($start_date)->format('Y-m-d') ?? Carbon::now()->subMonth();
        $endDate = Carbon::parse($end_date)->format('Y-m-d') ?? Carbon::now();

        $query = Earning::query()->with(['platform', 'country', 'label', 'artist', 'song', 'song.products'])
            ->when(self::hasAdmin(), function ($query) {
                return $query;
            }, function ($query) {
                $query->where('earnings.user_id', Auth::id());
            })
            ->whereBetween('sales_date', [$startDate, $endDate]);

        return match ($trend) {
            'albums' => self::getMonthlyAlbums($query),
            'artists' => self::getMonthlyArtists($query),
            'labels' => self::getMonthlyLabels($query),
            'platforms' => self::getMonthlyPlatforms($query),
            'countries' => self::getMonthlyCountries($query),
            default => self::getMonthlySongs($query),
        };
    }

    public static function getMonthlySongs($query)
    {
        // Seçili tarih aralığındaki tüm şarkıların toplam dinleme sayısını al
        $totalPlaysQuery = (clone $query)->whereHas('song', function ($query) {
            $query->whereHas('products', function ($query) {
                $query->where('type', ProductTypeEnum::SOUND->value);
            });
        });

        $totalPlays = $totalPlaysQuery->sum('quantity');

        // Gruplama ve toplama işlemlerini uygula
        $songs = $query->selectRaw('song_id, song_name, SUM(quantity) as total_plays')
            ->whereHas('song', function ($query) {
                $query->whereHas('products', function ($query) {
                    $query->where('type', ProductTypeEnum::SOUND->value);
                });
            })
            ->groupBy('song_id', 'song_name')
            ->get();

        // Yüzdelik oranı hesapla ve sonuçlara ekle
        foreach ($songs as $song) {
            $song->total_plays = (int) $song->total_plays; // total_plays'i sayıya çevir
            $song->play_percentage = $totalPlays > 0 ? ($song->total_plays / $totalPlays) * 100 : 0;

            // İlişkili label ve artist bilgilerini al
            $songData = $song->song->products->first();
            $song->label = $songData ? $songData->label : null;
            $song->artist = $songData ? $songData->artists()->first() : null;
        }

        return $songs->map(function ($song) use ($totalPlays) {
            return [
                'song_id' => $song->song_id,
                'song_name' => $song->song_name,
                'isrc_code' => $song->song->isrc,
                'total_plays' => $song->total_plays,
                'total_songs' => $totalPlays,
                'play_percentage' => $song->play_percentage,
                'label' => $song->label,
                'artist' => $song->artist,
                'product' => $song->song->products->first(),
            ];
        });
    }

    public static function getMonthlyAlbums($query)
    {
        // Seçili tarih aralığındaki tüm albümlerin toplam dinleme sayısını al
        $totalStreamsQuery = (clone $query);
        $totalStreams = $totalStreamsQuery->sum('quantity');

        // Gruplama ve toplama işlemlerini uygula ve ilişkileri önceden yükle
        $albums = $query->with(['song', 'platform'])
            ->select('release_name', 'artist_name', DB::raw('SUM(quantity) as total_streams'))
            ->groupBy('release_name', 'artist_name')
            ->get();

        // Hata ayıklama: Gruplama ve toplama işlemi kontrolü

        // Yüzdelik oranı hesapla ve sonuçlara ekle
        foreach ($albums as $album) {
            $album->total_streams = (int) $album->total_streams; // total_streams'i sayıya çevir
            $album->stream_percentage = $totalStreams > 0 ? ($album->total_streams / $totalStreams) * 100 : 0;

            // İlişkili label, artist ve yayın tarihi bilgilerini al
            $earning = Earning::with(['song.products'])->where('release_name', $album->release_name)
                ->where('artist_name', $album->artist_name)
                ->first();

            if ($earning) {
                if ($earning->song) {
                    $album->song_name = $earning->song_name;
                    $album->artist_name = $earning->artist_name;
                    $album->release_date = optional($earning->song->products->first())->release_date;
                } else {
                    // Hata ayıklama: $earning->song null olduğunda
                    $album->song_name = 'No song found';
                    $album->artist_name = 'No song found';
                    $album->release_date = 'No song found';
                }
            } else {
                // Hata ayıklama: $earning null olduğunda
                $album->song_name = 'No earning found';
                $album->artist_name = 'No earning found';
                $album->release_date = 'No earning found';
            }
        }

        return $albums->map(function ($album) use ($totalStreams) {
            return [
                'release_name' => $album->release_name,
                'artist_name' => $album->artist_name,
                'release_date' => $album->release_date,
                'total_streams' => $album->total_streams,
                'stream_percentage' => $album->stream_percentage,
            ];
        });
    }

    public static function getMonthlyArtists($query)
    {
        // Seçili tarih aralığındaki tüm sanatçıların toplam dinleme sayısını al
        $totalStreamsQuery = (clone $query);
        $totalStreams = $totalStreamsQuery->sum('quantity');

        // Gruplama ve toplama işlemlerini uygula ve ilişkileri önceden yükle
        $artists = $query->select('artist_name', DB::raw('SUM(quantity) as total_streams'),
            DB::raw('COUNT(DISTINCT song_id) as total_songs'))
            ->groupBy('artist_name')
            ->get();

        // Yüzdelik oranı hesapla ve sonuçlara ekle
        foreach ($artists as $artist) {
            $artist->total_streams = (int) $artist->total_streams; // total_streams'i sayıya çevir
            $artist->stream_percentage = $totalStreams > 0 ? ($artist->total_streams / $totalStreams) * 100 : 0;
        }

        return $artists->map(function ($artist) use ($totalStreams) {
            return [
                'artist_name' => $artist->artist_name,
                'total_streams' => $artist->total_streams,
                'stream_percentage' => $artist->stream_percentage,
                'total_songs' => $artist->total_songs,
            ];
        });
    }

    public static function getMonthlyLabels($query)
    {
        // Seçili tarih aralığındaki tüm label'ların toplam dinleme sayısını al
        $totalStreamsQuery = (clone $query);
        $totalStreams = $totalStreamsQuery->sum('quantity');

        // Gruplama ve toplama işlemlerini uygula ve ilişkileri önceden yükle
        $labels = $query->select('label_name', DB::raw('SUM(quantity) as total_streams'),
            DB::raw('COUNT(DISTINCT song_id) as total_songs'))
            ->groupBy('label_name')
            ->get();

        // Yüzdelik oranı hesapla ve sonuçlara ekle
        foreach ($labels as $label) {
            $label->total_streams = (int) $label->total_streams; // total_streams'i sayıya çevir
            $label->stream_percentage = $totalStreams > 0 ? ($label->total_streams / $totalStreams) * 100 : 0;
        }

        return $labels->map(function ($label) use ($totalStreams) {
            return [
                'label_name' => $label->label_name,
                'total_streams' => $label->total_streams,
                'stream_percentage' => $label->stream_percentage,
                'total_songs' => $label->total_songs,
            ];
        });
    }

    public static function getMonthlyPlatforms($query)
    {
        // Seçili tarih aralığındaki tüm platformların toplam dinleme sayısını al
        $totalStreamsQuery = (clone $query);
        $totalStreams = $totalStreamsQuery->sum('quantity');

        // Gruplama ve toplama işlemlerini uygula ve ilişkileri önceden yükle
        $platforms = $query->select('platform', DB::raw('SUM(quantity) as total_streams'),
            DB::raw('COUNT(DISTINCT song_id) as total_songs'))
            ->groupBy('platform')
            ->get();

        // Yüzdelik oranı hesapla ve sonuçlara ekle
        foreach ($platforms as $platform) {
            $platform->total_streams = (int) $platform->total_streams; // total_streams'i sayıya çevir
            $platform->stream_percentage = $totalStreams > 0 ? ($platform->total_streams / $totalStreams) * 100 : 0;
        }

        return $platforms->map(function ($platform) use ($totalStreams) {
            return [
                'platform_name' => $platform->platform,
                'total_streams' => $platform->total_streams,
                'stream_percentage' => $platform->stream_percentage,
                'total_songs' => $platform->total_songs,
            ];
        });
    }

    public static function getMonthlyCountries($query)
    {
        // Seçili tarih aralığındaki tüm ülkelerin toplam dinleme sayısını al
        $totalStreamsQuery = (clone $query);
        $totalStreams = $totalStreamsQuery->sum('quantity');

        // Gruplama ve toplama işlemlerini uygula ve ilişkileri önceden yükle
        $countries = $query->select('country', DB::raw('SUM(quantity) as total_streams'),
            DB::raw('COUNT(DISTINCT song_id) as total_songs'))
            ->groupBy('country')
            ->get();

        // Yüzdelik oranı hesapla ve sonuçlara ekle
        foreach ($countries as $country) {
            $country->total_streams = (int) $country->total_streams; // total_streams'i sayıya çevir
            $country->stream_percentage = $totalStreams > 0 ? ($country->total_streams / $totalStreams) * 100 : 0;
        }

        return $countries->map(function ($country) use ($totalStreams) {
            return [
                'country' => $country->country,
                'total_streams' => $country->total_streams,
                'stream_percentage' => $country->stream_percentage,
                'total_songs' => $country->total_songs,
            ];
        });
    }

    public static function earningsOfTheUser(User $user)
    {
        $earnings = Earning::where('user_id', $user->id)
            ->select('platform', DB::raw('DATE_FORMAT(sales_date, "%Y-%m") as month_year'),
                DB::raw('SUM(earning) as total_earning'))
            ->groupBy('platform', 'month_year')
            ->orderBy('platform')
            ->orderBy('month_year')
            ->get()
            ->map(function ($item) use ($user) {
                $item->details = Earning::where('user_id', $user->id)
                    ->where('platform', $item->platform)
                    ->where(DB::raw('DATE_FORMAT(sales_date, "%Y-%m")'), $item->month_year)
                    ->select('platform', 'platform_id', 'earning', 'sales_date', 'report_date')
                    ->get();
                return $item;
            });

        return $earnings;
    }

    private const STREAMING_TYPES = ['Freemium', 'Premium', 'Ad-Supported', ''];
    private const PLATFORMS = [
        'Spotify', 'Apple Music', 'YouTube Music', 'Amazon Music', 'Facebook / Instagram', 'TikTok', 'Deezer'
    ];
    private const SALES_TYPES = ['Stream', 'PLATFORM PROMOTION', 'Creation', 'Download'];
    private const RELEASE_TYPES = ['Music Release', 'Ringtone', 'Video', 'User Generated Content'];

    public static function createDemoEarnings($userId)
    {
        $faker = \Faker\Factory::create();
        $data = [];
        $totalProcessed = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            // CSV dosyasını oku
            $csvPath = public_path('assets/sample-earnings-tr.csv');
            $csvFile = fopen($csvPath, 'r');

            // Header'ı atla
            fgetcsv($csvFile, 0, ';', '"', '\\');

            // Mevcut ürünleri ve şarkıları al
            $products = Product::with(['songs', 'artists', 'label', 'downloadPlatforms'])
                ->whereHas('songs')
                ->whereHas('artists')
                ->whereHas('label')
                ->whereHas('downloadPlatforms')
                ->get();

            $songs = collect();
            foreach ($products as $product) {
                $songs = $songs->merge($product->songs);
            }

            if ($products->isEmpty()) {
                throw new \Exception('Demo kazanç oluşturmak için uygun ürün bulunamadı.');
            }

            // CSV'den verileri oku
            while (($row = fgetcsv($csvFile, 0, ';')) !== false) {
                try {
                    $randomProduct = $products->random();
                    $randomSong = $songs->random();

                    $sales_date = Carbon::parse($faker->dateTimeBetween('-1 year', now()));

                    $data[] = [
                        'user_id' => $userId,
                        'report_date' => Carbon::parse($faker->dateTimeBetween($sales_date, now()))->format('Y-m-d'),
                        'reporting_month' => $sales_date->format('Y/m/01'),
                        'sales_date' => $sales_date->format('Y-m-d'),
                        'sales_month' => $sales_date->format('Y/m/01'),
                        'platform' => $row[2] ?? '',
                        'platform_id' => Platform::where('name', $row[2])->first()?->id,
                        'country' => $row[3] ?? '',
                        'region' => $faker->randomElement([
                            'Europe', 'North America', 'South America', 'Asia', 'Africa', 'Oceania'
                        ]),
                        'country_id' => Country::where('name', $row[3])->first()?->id,
                        'label_name' => $row[4] ?? '',
                        'label_id' => $randomProduct->label->id,
                        'artist_name' => $row[5] ?? '',
                        'artist_id' => $randomProduct->artists->random()->id,
                        'release_name' => $row[6] ?? '',
                        'song_name' => $row[7] ?? '',
                        'song_id' => $randomSong->id,
                        'upc_code' => $randomProduct->upc_code,
                        'isrc_code' => $randomSong->isrc,
                        'catalog_number' => $row[10] ?? '',
                        'streaming_type' => $row[11] ?? '',
                        'streaming_subscription_type' => $row[11] ?? '',
                        'release_type' => $row[12] ?? '',
                        'sales_type' => $row[13] ?? '',
                        'quantity' => intval($row[14] ?? 0),
                        'currency' => $row[15] ?? 'EUR',
                        'client_payment_currency' => $row[15] ?? 'EUR',
                        'unit_price' => str_replace(',', '.', $row[16] ?? 0),
                        'mechanical_fee' => str_replace(',', '.', $row[17] ?? 0),
                        'gross_revenue' => str_replace(',', '.', $row[18] ?? 0),
                        'client_share_rate' => str_replace(',', '.', $row[19] ?? 0),
                        'earning' => str_replace(',', '.', $row[20] ?? 0),
                    ];

                    $totalProcessed++;
                } catch (\Exception $e) {
                    $errors++;
                    Log::warning('Demo kazanç kaydı oluşturma hatası', [
                        'error' => $e->getMessage(),
                        'row' => $row
                    ]);
                    continue;
                }
            }

            fclose($csvFile);

            if (empty($data)) {
                throw new \Exception('Demo kazanç verileri oluşturulamadı.');
            }

            $file = EarningReportFile::create([
                'user_id' => $userId,
                'name' => 'Demo Earning Report '.Carbon::now()->format('Y-m-d'),
            ]);

            // Chunk'lar halinde kaydet
            foreach (array_chunk($data, 100) as $chunk) {
                self::createEarning($chunk, $file->id);
            }

            DB::commit();

            // Excel dosyası oluştur
            $exportResult = Excel::store(
                new FakerEarningReport($data),
                'earnings-'.time().'.csv',
                'tenant_'.tenant('domain').'_earning_reports'
            );

            Log::info('Demo kazanç verileri oluşturuldu', [
                'total_records' => count($data),
                'total_processed' => $totalProcessed,
                'errors' => $errors,
                'export_status' => $exportResult
            ]);

            return $exportResult;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Demo kazanç oluşturma hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected static function createEarning($data, $file_id)
    {
        foreach ($data as $row) {
            // Önce EarningReport oluştur
            $report = EarningReport::updateOrCreate(
                [
                    'report_date' => $row['report_date'],
                    'sales_date' => $row['sales_date'],
                    'platform' => $row['platform'],
                    'isrc_code' => $row['isrc_code'],
                    'net_revenue' => str_replace(',', '.', $row['earning']),
                ],
                [
                    'platform_id' => $row['platform_id'],
                    'country_id' => $row['country_id'],
                    'label_id' => $row['label_id'],
                    'artist_id' => $row['artist_id'],
                    'song_id' => $row['song_id'],
                    'earning_report_file_id' => $file_id,
                    'country' => $row['country'],
                    'region' => $row['region'] ?? null,
                    'label_name' => $row['label_name'],
                    'artist_name' => $row['artist_name'],
                    'release_name' => $row['release_name'],
                    'song_name' => $row['song_name'],
                    'upc_code' => $row['upc_code'],
                    'catalog_number' => $row['catalog_number'],
                    'streaming_type' => $row['streaming_type'],
                    'streaming_subscription_type' => $row['streaming_subscription_type'],
                    'release_type' => $row['release_type'],
                    'sales_type' => $row['sales_type'],
                    'quantity' => $row['quantity'],
                    'currency' => $row['currency'],
                    'client_payment_currency' => $row['client_payment_currency'],
                    'unit_price' => isset($row['unit_price']) ? str_replace(',', '.', $row['unit_price']) : null,
                    'mechanical_fee' => isset($row['mechanical_fee']) ? str_replace(',', '.',
                        $row['mechanical_fee']) : null,
                    'gross_revenue' => isset($row['gross_revenue']) ? str_replace(',', '.',
                        $row['gross_revenue']) : null,
                    'client_share_rate' => isset($row['client_share_rate']) ? str_replace(',', '.',
                        $row['client_share_rate']) : null,
                    'reporting_month' => $row['reporting_month'],
                    'sales_month' => $row['sales_month'],
                ]
            );

            // Sonra Earning kaydı oluştur
            Earning::create([
                'earning_report_id' => $report->id,
                'user_id' => $row['user_id'],
                'report_date' => $row['report_date'],
                'reporting_month' => $row['reporting_month'],
                'sales_date' => $row['sales_date'],
                'sales_month' => $row['sales_month'],
                'platform' => $row['platform'],
                'platform_id' => $row['platform_id'],
                'country' => $row['country'],
                'region' => $row['region'] ?? null,
                'country_id' => $row['country_id'],
                'label_name' => $row['label_name'],
                'label_id' => $row['label_id'],
                'artist_name' => $row['artist_name'],
                'artist_id' => $row['artist_id'],
                'release_name' => $row['release_name'],
                'song_name' => $row['song_name'],
                'song_id' => $row['song_id'],
                'upc_code' => $row['upc_code'],
                'isrc_code' => $row['isrc_code'],
                'catalog_number' => $row['catalog_number'],
                'streaming_type' => $row['streaming_type'],
                'streaming_subscription_type' => $row['streaming_subscription_type'],
                'release_type' => $row['release_type'],
                'sales_type' => $row['sales_type'],
                'quantity' => $row['quantity'],
                'currency' => $row['currency'],
                'client_payment_currency' => $row['client_payment_currency'],
                'unit_price' => $row['unit_price'],
                'mechanical_fee' => $row['mechanical_fee'],
                'gross_revenue' => $row['gross_revenue'],
                'client_share_rate' => $row['client_share_rate'],
                'earning' => $row['earning'],
            ]);
        }
    }


}
