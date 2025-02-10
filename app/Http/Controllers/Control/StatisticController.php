<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use App\Models\Platform;
use App\Models\Product;
use App\Models\System\Country;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatisticController extends Controller
{


    public function index(Request $request)
    {
        //gelen tarih formatı m-Y den Y-m-d ye çevir
        [$startDate, $endDate] = $this->getDateRange($request);

        $user = Auth::user();
        $earnings = Earning::query()->where('user_id', $user->id)->whereBetween('report_date',
            [$startDate, $endDate])->get();

        //Aylık Dinleme istatistikleri
        $monthlyStats = $this->getMonthlyListeningStatistics($earnings);

        //Aylık İndirme istatistikleri
        $downloadStats = $this->getDownloadStatistics($earnings);

        //Aylık Platform istatistikleri
        $platformStats = $this->getPlatformStatistics($earnings);

        //Platform bazlı satış sayıları
        $platformSalesCount = $this->getPlatformSalesCount($earnings);

        //En iyiler

        $tab = $request->input('slug');

        $tabData = $this->getTabData($tab, $earnings);

        return Inertia::render('Control/Statistics/index', [
            'monthlyStats' => $monthlyStats,
            'downloadCounts' => $downloadStats,
            'platformStatistics' => $platformStats,
            'platformSalesCount' => $platformSalesCount,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'platforms' => $this->getPlatforms($earnings),
            'tab' => $tabData,
        ]);
    }

    //Aylık dinleme istatistikleri
    private function getMonthlyListeningStatistics($earnings, Product $product = null)
    {
        $monthlyStats = $earnings->where('sales_type', 'Stream')
            ->groupBy('report_date')
            ->map(function ($item) {
                return $item->sum('quantity');
            })
            ->mapWithKeys(function ($value, $key) {
                return [Carbon::parse($key)->format('Y-m') => $value];
            })
            ->sortKeys()
            ->mapWithKeys(function ($value, $key) {
                $date = Carbon::createFromFormat('Y-m', $key);
                $date->setLocale(app()->getLocale());
                return [$date->translatedFormat('F Y') => $value];
            });

        $average = round($monthlyStats->avg(), 2);
        $total = $monthlyStats->sum();
        $monthlyStats = $monthlyStats->sortKeys();
        $labels = $monthlyStats->keys()->toArray();
        $series = $monthlyStats->values()->toArray();

        return [
            'monthly_data' => $monthlyStats->toArray(),
            'average' => $average,
            'total' => $total,
            'labels' => $labels,
            'series' => $series,
        ];
    }

    private function getDownloadStatistics($earnings)
    {
        // release_type a göre gruplandırılan dataların toplam indirme istatistikleri quantity toplamları
        // song download
        // album download
        // video download

        $downloadStats = $earnings->where('sales_type', 'Download')
            ->groupBy('release_type')
            ->map(function ($item) {
                return $item->sum('quantity');
            })
            ->mapWithKeys(function ($value, $key) {
                return [$key => $value];
            });

        return $downloadStats;
    }

    private function getPlatformStatistics($earnings)
    {
        $platforms = ['Spotify', 'Apple'];

        // Ana platformları topla
        $mainPlatforms = collect();
        foreach ($platforms as $platform) {
            $platformData = $earnings->filter(function ($item) use ($platform) {
                return str_contains(strtolower($item->platform), strtolower($platform));
            });

            $mainPlatforms[strtolower($platform)] = $platformData->isNotEmpty() ? $platformData->sum('quantity') : 0;
        }

        // Diğer platformları topla
        $otherPlatforms = $earnings->filter(function ($item) use ($platforms) {
            foreach ($platforms as $platform) {
                if (str_contains(strtolower($item->platform), strtolower($platform))) {
                    return false;
                }
            }
            return true;
        });

        $mainPlatforms['other'] = $otherPlatforms->isNotEmpty() ? $otherPlatforms->sum('quantity') : 0;

        // Frontend'in beklediği formatta veriyi döndür
        return [
            'platforms' => [
                'spotify' => $mainPlatforms['spotify'] ?? 0,
                'apple' => $mainPlatforms['apple'] ?? 0,
                'other' => $mainPlatforms['other'] ?? 0
            ]
        ];
    }

    private function getPlatformMonthlyStatistics($earnings, $platform = 'Spotify')
    {
        $platformMonthlyStats = $earnings->where('platform', $platform)
            ->groupBy('platform')
            ->map(function ($item) {
                return $item->sum('quantity');
            })
            ->mapWithKeys(function ($value, $key) {
                return [Carbon::parse($key)->format('Y-m') => $value];
            });

        return $platformMonthlyStats;

    }

    private function getPlatformSalesCount($earnings, $platform = 'Spotify', Product $product = null)
    {
        //release_type a göre gruplandırılan dataların toplam satış sayıları aylık gruplandırılmış quantity toplamları
        //Aylık Dinleme istatistikleri ile aynı mantık

        if ($product) {
            $earnings = $earnings->where('upc_code', $product->upc_code);
        }

        $platformSalesCount = $earnings->where('sales_type', 'Download')
            ->where('platform', $platform)
            ->groupBy(['release_type', 'report_date'])
            ->map(function ($releaseTypeGroup) {
                return $releaseTypeGroup->map(function ($dateGroup) {
                    return $dateGroup->sum('quantity');
                });
            })
            ->map(function ($releaseTypeData) {
                return $releaseTypeData->mapWithKeys(function ($value, $key) {
                    return [Carbon::parse($key)->format('Y-m') => $value];
                })->sortKeys();
            })->sortKeys();

        return $platformSalesCount;
    }

    private function getTabData($tab, $earnings)
    {
        switch ($tab) {
            case 'products':
                return $this->getAlbumsTabData($earnings);
            case 'artists':
                return $this->getArtistsTabData($earnings);
            case 'countries':
                return $this->getCountriesTabData($earnings);
            case 'platforms':
                return $this->getPlatformsTabData($earnings);
            case 'labels':
                return $this->getLabelsTabData($earnings);
            default:
                return $this->getSongsTabData($earnings);
        }
    }

    private function getAlbumsTabData($earnings)
    {
        $totalQuantity = $earnings->sum('quantity');

        $albums = $earnings->load('product')
            ->groupBy('upc_code')
            ->map(function ($group) use ($totalQuantity) {
                return [
                    'album_type' => $group->first()->product->type,
                    'album_id' => $group->first()->product->id,
                    'album_image' => $group->first()->product->image,
                    'album_name' => $group->first()->release_name,
                    'upc_code' => $group->first()->upc_code,
                    'artist_name' => $group->first()->artist_name,
                    'label_name' => $group->first()->label_name,
                    'release_date' => $group->first()->sales_date,
                    'quantity' => $group->sum('quantity'),
                    'quantity_percentage' => round(($group->sum('quantity') / $totalQuantity) * 100, 2),
                ];
            })
            ->sortByDesc('quantity')
            ->take(100)
            ->values()
            ->toArray();

        return $albums;


    }

    private function getArtistsTabData($earnings)
    {
        //artist_id a göre gruplandırılan dataların toplam dinleme sayıları
        //artist_id, artist_image, artist_name, spotify_id, apple_id, toplam parça sayısı, toplam dinleme sayısı, toplam dinleme sayısına oranı


        $totalQuantity = $earnings->sum('quantity');
        $spotifyId = Platform::where('code', 'spotify')->first()->id;
        $appleId = Platform::where('code', 'apple')->first()->id;

        $artists = $earnings->load(['artist', 'artist.platforms', 'artist.songs'])
            ->groupBy('artist_id')
            ->map(function ($group) use ($totalQuantity, $spotifyId, $appleId) {
                $artist = $group->first()->artist;
                return [
                    'artist_id' => $artist->id,
                    'artist_image' => $artist->image,
                    'artist_name' => $artist->name,
                    'spotify_id' => $artist->platforms->where('platform_id', $spotifyId)->first()?->platform_artist_id,
                    'apple_id' => $artist->platforms->where('platform_id', $appleId)->first()?->platform_artist_id,
                    'song_count' => $artist->songs->count(),
                    'quantity' => $group->sum('quantity'),
                    'quantity_percentage' => round(($group->sum('quantity') / $totalQuantity) * 100, 2),
                ];
            })
            ->sortByDesc('quantity')
            ->take(100)
            ->values()
            ->toArray();

        return $artists;
    }

    private function getCountriesTabData($earnings)
    {
        //country ye göre gruplandırılan dataların toplam dinleme sayıları
        //country, country_image song_count quantity, quantity_percentage

        $totalQuantity = $earnings->sum('quantity');

        $countryData = $earnings->groupBy('country')
            ->map(function ($group) use ($totalQuantity) {
                $countries = Country::whereIn('name', $group->pluck('country'))->get();
                $country = $countries->where('name', $group->first()->country)->first();
                return [
                    'country' => $country->name ?? $group->first()->country,
                    'country_id' => $country->id ?? null,
                    'emoji' => $country->emoji ?? null,
                    'song_count' => $group->first()->artist->songs->count(),
                    'quantity' => $group->sum('quantity'),
                    'quantity_percentage' => round(($group->sum('quantity') / $totalQuantity) * 100, 2),
                ];
            })
            ->sortByDesc('quantity')
            ->take(100)
            ->values()
            ->toArray();

        return $countryData;
    }

    private function getPlatformsTabData($earnings)
    {
        //platform_id a göre gruplandırılan dataların toplam dinleme sayıları
        //platform_id, platform_name, platform_image song_count, quantity, quantity_percentage

        $totalQuantity = $earnings->sum('quantity');

        $platforms = $earnings->load('platform')
            ->groupBy('platform_id')
            ->map(function ($group) use ($totalQuantity) {
                return [
                    'platform_id' => $group->first()->platform->id,
                    'platform_name' => $group->first()->platform->name,
                    'platform_image' => $group->first()->platform->image,
                    'song_count' => $group->first()->artist->songs->count(),
                    'quantity' => $group->sum('quantity'),
                    'quantity_percentage' => round(($group->sum('quantity') / $totalQuantity) * 100, 2),
                ];
            })
            ->sortByDesc('quantity')
            ->take(100)
            ->values()
            ->toArray();

        return $platforms;
    }

    private function getLabelsTabData($earnings)
    {
        //label_id a göre gruplandırılan dataların toplam dinleme sayıları
        //label_id, label_name, label_image song_count, quantity, quantity_percentage

        $totalQuantity = $earnings->sum('quantity');

        $labels = $earnings->load('label')
            ->groupBy('label_id')
            ->map(function ($group) use ($totalQuantity) {
                return [
                    'label_id' => $group->first()->label->id,
                    'label_name' => $group->first()->label->name,
                    'label_image' => $group->first()->label->image,
                    'song_count' => $group->first()->artist->songs->count(),
                    'quantity' => $group->sum('quantity'),
                    'quantity_percentage' => round(($group->sum('quantity') / $totalQuantity) * 100, 2),
                ];
            })
            ->sortByDesc('quantity')
            ->take(100)
            ->values()
            ->toArray();

        return $labels;
    }

    private function getSongsTabData($earnings)
    {
        //en çok dinlenen 20 şarkı sayısını bulmak için quantity toplamlarını al
        //verilecek format
        //song_type, song_id, name, artist_name,label_name quantity, toplam dinleme sayısına oranı
        //quantity, toplam dinleme sayısına oranı = quantity / toplam dinleme sayısı

        $totalQuantity = $earnings->sum('quantity');

        return $earnings->load('song')
            ->groupBy('song_id')
            ->map(function ($group) use ($totalQuantity) {
                return [
                    'song_type' => $group->first()->song->type,
                    'song_id' => $group->first()->song_id,
                    'name' => $group->first()->release_name,
                    'version' => $group->first()->song->version,
                    'isrc_code' => $group->first()->isrc_code,
                    'artist_image' => $group->first()->artist->image,
                    'artist_name' => $group->first()->artist_name,
                    'label_name' => $group->first()->label_name,
                    'quantity' => $group->sum('quantity'),
                    'quantity_percentage' => round(($group->sum('quantity') / $totalQuantity) * 100, 2),
                ];
            })
            ->sortByDesc('quantity')
            ->take(100)
            ->values()
            ->toArray();
    }

    private function getDateRange(Request $request, $defaultMonths = 6): array
    {
        $startDateInput = trim($request->input('start_date'));
        $endDateInput = trim($request->input('end_date'));

        if ($request->filled(['start_date', 'end_date'])) {
            $startDate = Carbon::createFromFormat('m-Y', $startDateInput)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::createFromFormat('m-Y', $endDateInput)->endOfMonth()->format('Y-m-d');
        } else {
            $startDate = Carbon::now()->subMonths($defaultMonths)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        return [$startDate, $endDate];
    }

    private function getPlatforms($earnings)
    {
        return Platform::all();
    }


    public function product(Product $product, Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);
        $platform = $request->input('platform') ?? 'Spotify';

        $spotifyId = Platform::where('code', 'spotify')->first()->id;
        $platforms = Platform::all();
        $spotifyId = Platform::where('code', 'spotify')->first()->id;
        $appleId = Platform::where('code', 'apple')->first()->id;
        $otherIds = Platform::whereNotIn('code', ['spotify', 'apple'])->first()->id;

        $product->loadMissing('downloadPlatforms', 'mainArtists', 'songs', 'earnings');

        $monthlyStats = $this->getMonthlyListeningStatistics($product->earnings, $product);

        $downloadCounts = [
            'songs' => $product->earnings->where('release_type', 'Music')?->sum('quantity'),
            'albums' => $product->earnings->where('release_type', 'Album')?->sum('quantity'),
            'videos' => $product->earnings->where('release_type', 'Video')?->sum('quantity'),
        ];

        $platformStats = [
            'total' => $product->earnings->sum('quantity'),
            'spotify' => $product->earnings->where('platform_id', $spotifyId)->sum('quantity'),
            'apple' => $product->earnings->where('platform_id', $appleId)->sum('quantity'),
            'other' => $product->earnings->where('platform_id', $otherIds)->sum('quantity'),
        ];

        $platformSalesCount = $this->getPlatformSalesCount($product->earnings, $platform, $product);

        return Inertia::render('Control/Statistics/product', [
            'product' => $product,
            'platforms' => $platforms,
            'downloadCounts' => $downloadCounts,
            'platformStats' => $platformStats,
            'monthlyStats' => $monthlyStats,
            'platformSalesCount' => $platformSalesCount,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
