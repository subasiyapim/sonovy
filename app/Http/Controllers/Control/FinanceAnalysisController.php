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
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
            $startDate = Carbon::now()->subMonths(1)->startOfMonth()->format('Y-m-d');
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

            Log::info('Earnings Query Result', [
                'count' => $earnings->count(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'user_id' => Auth::id(),
                'sample_data' => $earnings->take(1)->toArray()
            ]);

            return $earnings;
        });
    }

    private function formatResponse($earnings, string $tab)
    {
        Log::info('Format Response', [
            'tab' => $tab,
            'earnings_count' => $earnings->count()
        ]);

        $response = (new AnalyseResource($earnings, $tab))->resolve();

        Log::info('Response Data', [
            'has_data' => !empty($response['data']),
            'metadata' => $response['metadata'] ?? null,
            'data' => $response['data'] ?? null
        ]);

        return $response;
    }

    public function show(Request $request)
    {
        $request->validate([
            'slug' => [
                'required',
                'string',
                'in:earning_from_platforms,earning_from_countries,earning_from_sales_type,trending_albums,top_artists,top_albums,top_songs,top_labels,platforms,countries'
            ],
            'request_type' => ['required', 'string', 'in:view,download'],
            'start_date' => ['date', 'required_with:end_date'],
            'end_date' => ['date', 'required_with:start_date'],
        ]);

        $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
        $endDate = Carbon::parse($request->end_date)->format('Y-m-d');

        Cache::put('start_date', $startDate, self::CACHE_DURATION);
        Cache::put('end_date', $endDate, self::CACHE_DURATION);

        $cacheKey = 'earning_analysis_show_' . md5($request->fullUrl());
        $earnings = Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($startDate, $endDate) {
            return Earning::with([
                'song:id,name,isrc',
                'platform:id,name',
                'country:id,name',
                'label:id,name'
            ])
            ->whereBetween('sales_date', [$startDate, $endDate])
            ->get();
        });

        $service = new AnalyseService($earnings);
        $data = $this->getDataBySlug($service, $request->slug);

        if ($request->request_type === 'view') {
            return $this->view($data);
        }

        if ($request->request_type === 'download') {
            return $this->download($data, $request->slug);
        }
    }

    private function getDataBySlug(AnalyseService $service, string $slug): array
    {
        return match ($slug) {
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
    }

    private function view($earnings)
    {
        return $earnings;
    }

    private function download($earnings, $slug)
    {
        return Excel::download(new AnalyseExport($earnings, $slug), $slug . '.xlsx');
    }
}
