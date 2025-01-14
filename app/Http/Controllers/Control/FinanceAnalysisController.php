<?php

namespace App\Http\Controllers\Control;

use App\Exports\AnalyseExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Finance\AnalyseResource;
use App\Models\Earning;
use App\Services\AnalyseService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class FinanceAnalysisController extends Controller
{
    private const CACHE_DURATION = 60 * 60 * 12; // 12 hours

    public function index(Request $request)
    {
        $this->validateRequest($request);

        [$startDate, $endDate] = $this->getDateRange($request);

        $defaultTab = 'general';
        $currentTab = $request->input('slug') ?? $defaultTab;

        $cacheKey = $this->generateCacheKey($request);
        $earnings = $this->getEarnings($cacheKey, $startDate, $endDate);

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
            $startDate = Carbon::now()->subMonths(11)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        return [$startDate, $endDate];
    }

    private function generateCacheKey(Request $request): string
    {
        return 'earning_analysis_'.md5($request->fullUrl());
    }

    private function getEarnings(string $cacheKey, string $startDate, string $endDate)
    {
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($startDate, $endDate) {
            return Earning::whereBetween('sales_date', [$startDate, $endDate])
                ->get();
        });
    }

    private function formatResponse($earnings, string $tab)
    {
        return (new AnalyseResource($earnings, $tab))->resolve();
    }

    public function show(Request $request)
    {
        $request->validate(
            [
                'slug' => [
                    'required',
                    'string',
                    'in:earning_from_platforms,earning_from_countries,earning_from_sales_type,trending_albums,top_artists,top_albums,top_songs,top_labels,platforms,countries'
                ],
                'request_type' => ['required', 'string', 'in:view,download'],
                'start_date' => ['date', 'required_with:end_date'],
                'end_date' => ['date', 'required_with:start_date'],
            ]
        );

        $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
        $end_date = Carbon::parse($request->end_date)->format('Y-m-d');

        Cache::put('start_date', $start_date);
        Cache::put('end_date', $end_date);

        $earnings = Earning::with('product', 'song', 'platform', 'country', 'label')
            ->whereBetween('sales_date', [$start_date, $end_date])
            ->get();

        $service = new AnalyseService($earnings);

        $data = $this->getDataBySlug($service, $request->slug, $start_date, $end_date);

        if ($request->request_type === 'view') {
            return $this->view($data);
        }

        if ($request->request_type === 'download') {
            return $this->download($data, $request->slug);
        }
    }

    private function getDataBySlug(AnalyseService $service, string $slug, string $start_date, string $end_date): array
    {
        switch ($slug) {
            case 'earning_from_platforms':
                return $service->earningFromPlatforms($start_date, $end_date);
            case 'earning_from_countries':
                return $service->earningFromCountries($start_date, $end_date);
            case 'earning_from_sales_type':
                return $service->earningFromSalesType();
            case 'trending_albums':
                return $service->trendingAlbums();
            case 'top_artists':
                return $service->topArtists();
            case 'top_albums':
                return $service->topAlbums();
            case 'top_songs':
                return $service->topSongs();
            case 'top_labels':
                return $service->topLabels();
            case 'platforms':
                return $service->platforms();
            case 'countries':
                return $service->countries();
            default:
                return [];
        }
    }

    private function view($earnings)
    {
        return $earnings;
    }

    private function download($earnings, $slug)
    {
        return Excel::download(new AnalyseExport($earnings, $slug), $slug.'.xlsx');
    }
}
