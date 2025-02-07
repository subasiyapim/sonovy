<?php

namespace App\Http\Controllers\Control;

use App\Exports\AnalyseExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Finance\AnalyseResource;
use App\Models\Earning;
use App\Services\AnalyseService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class FinanceAnalysisController extends Controller
{
    private const CACHE_DURATION = 60 * 60 * 12; // 12 saat

    public function index(Request $request)
    {
        $this->validateRequest($request);
        [$startDate, $endDate] = $this->getDateRange($request);

        $defaultTab = 'general';
        $currentTab = $request->input('slug') ?? $defaultTab;

        $cacheKey = $this->generateCacheKey($request);
        $earnings = $this->getEarnings($cacheKey, $startDate, $endDate);

        Cache::put('start_date', $startDate, self::CACHE_DURATION);
        Cache::put('end_date', $endDate, self::CACHE_DURATION);

        return inertia('Control/Finance/Analysis/Index', [
            'data' => $this->formatResponse($earnings, $currentTab)
        ]);
    }

    private function validateRequest(Request $request): void
    {
        $request->validate([
            'slug' => ['sometimes', 'string', 'in:general,top-lists,platforms,countries'],
            'start_date' => ['required_with:end_date', 'regex:/^(0?[1-9]|1[0-2])-\d{4}$/'],
            'end_date' => ['required_with:start_date', 'regex:/^(0?[1-9]|1[0-2])-\d{4}$/'],
        ]);
    }

    private function getDateRange(Request $request): array
    {
        $startDateInput = trim($request->input('start_date'));
        $endDateInput = trim($request->input('end_date'));

        if ($request->filled(['start_date', 'end_date'])) {
            $startDate = Carbon::createFromFormat('m-Y', $startDateInput)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::createFromFormat('m-Y', $endDateInput)->endOfMonth()->format('Y-m-d');
        } else {
            $startDate = Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        return [$startDate, $endDate];
    }

    private function generateCacheKey(Request $request): string
    {
        return sprintf(
            'earning_analysis_%s_%s_%s_%s',
            Auth::id(),
            $request->input('slug', 'general'),
            $request->input('start_date', ''),
            $request->input('end_date', '')
        );
    }

    private function getEarnings(string $cacheKey, string $startDate, string $endDate)
    {
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($startDate, $endDate) {
            $earnings = Earning::with([
                'song:id,name,isrc',
                'platform:id,name',
                'country:id,name',
                'label:id,name',
                'product'
            ])
                ->select([
                    'id',
                    'sales_date',
                    'earning',
                    'quantity',
                    'song_id',
                    'platform_id',
                    'country_id',
                    'label_id',
                    'user_id',
                    'artist_id',
                    'artist_name',
                    'release_name',
                    'streaming_subscription_type',
                    'sales_type',
                    'isrc_code',
                    'upc_code',
                    'platform',
                    'country',
                    'label_name'
                ])
                ->whereBetween('sales_date', [$startDate, $endDate])
                ->where('user_id', Auth::id())
                ->get();

            return $earnings;
        });
    }

    private function formatResponse($earnings, string $tab)
    {
        $response = (new AnalyseResource($earnings, $tab))->resolve();

        return $response;
    }

    public function show(Request $request)
    {
        try {
            $data = $this->getDataBySlug($request->slug);

            if ($request->request_type === 'download') {
                $filename = match ($request->slug) {
                    'earning_from_platforms' => 'platform-earnings.xlsx',
                    'earning_from_countries' => 'country-earnings.xlsx',
                    'earning_from_sales_type' => 'sales-earnings.xlsx',
                    'top_artists' => 'top-artists.xlsx',
                    'top_albums' => 'top-albums.xlsx',
                    'top_songs' => 'top-songs.xlsx',
                    'top_labels' => 'top-labels.xlsx',
                    default => $request->slug.'.xlsx'
                };

                return Excel::download(new AnalyseExport($data, str_replace('-', '_', $request->slug)), $filename);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error in show method: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            throw $e;
        }
    }

    private function getDataBySlug(string $slug): array
    {
        $startDate = request('start_date');
        $endDate = request('end_date');

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');

            Cache::put('start_date', $startDate, self::CACHE_DURATION);
            Cache::put('end_date', $endDate, self::CACHE_DURATION);
        }

        $earnings = Earning::with([
            'song:id,name,isrc',
            'platform:id,name',
            'country:id,name',
            'label:id,name',
            'product'
        ])
            ->select([
                'id',
                'sales_date',
                'earning',
                'quantity',
                'song_id',
                'platform_id',
                'country_id',
                'label_id',
                'user_id',
                'artist_id',
                'artist_name',
                'release_name',
                'streaming_subscription_type',
                'sales_type',
                'isrc_code',
                'upc_code',
                'platform',
                'country',
                'label_name'
            ]);

        if ($startDate && $endDate) {
            $earnings = $earnings->whereBetween('sales_date', [$startDate, $endDate]);
        }

        $earnings = $earnings->where('user_id', Auth::id())->get();

        $service = new AnalyseService($earnings);
        $result = match ($slug) {
            'earning_from_platforms' => $service->earningFromPlatforms(),
            'earning_from_countries' => $service->earningFromCountries(),
            'earning_from_sales_type' => $service->earningFromSalesType(),
            'trending_albums' => $service->trendingAlbums(),
            'top_artists' => $service->topArtists(),
            'top_albums' => $service->topAlbums(),
            'top_songs' => $service->topSongs(),
            'top_labels' => $service->topLabels(),
            'platforms' => $service->platforms(),
            'countries' => $service->countries(),
            default => [],
        };

        // Ham sayısal değerleri döndür, formatlamayı frontend'de yap
        if (is_array($result)) {
            array_walk_recursive($result, function (&$value) {
                if (is_string($value) && is_numeric(str_replace([',', '$'], '', $value))) {
                    $value = (float) str_replace([',', '$'], '', $value);
                }
            });
        }

        return $result;
    }

    private function view($earnings)
    {
        return $earnings;
    }

    private function download($earnings, $slug)
    {
        try {
            Excel::download(new AnalyseExport($earnings, $slug), $slug.'.xlsx');
        } catch (\Exception $e) {
            Log::error('Error in download method', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
