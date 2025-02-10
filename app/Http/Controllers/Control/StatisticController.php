<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use App\Models\Platform;
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
        $earnings = Earning::query()->where('user_id', $user->id)->whereBetween('report_date', [$startDate, $endDate])->get();

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
            'tabData' => $tabData,
        ]);
    }

    //Aylık dinleme istatistikleri
    private function getMonthlyListeningStatistics($earnings)
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

    private function getPlatformSalesCount($earnings)
    {
        //release_type a göre gruplandırılan dataların toplam satış sayıları aylık gruplandırılmış quantity toplamları
        //Aylık Dinleme istatistikleri ile aynı mantık

        $platformSalesCount = $earnings->where('sales_type', 'Download')
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
        //
    }

    private function getArtistsTabData($earnings)
    {
        //
    }

    private function getCountriesTabData($earnings)
    {
        //
    }

    private function getPlatformsTabData($earnings)
    {
        //
    }

    private function getLabelsTabData($earnings)
    {
        //
    }

    private function getSongsTabData($earnings)
    {
        $earnings = $earnings->load('song');
        $songs = $earnings->pluck('song')->unique('id');
        return $songs;

        dd($songs);
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
}
