<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Resources\Finance\AnalyseResource;
use App\Models\Earning;
use App\Services\AnalyseService;
use App\Services\EarningService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;


class FinanceAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'slug' => ['sometimes', 'string', 'in:general,top-lists,platforms,countries'],
            'start_date' => ['date', 'required_with:end_date'],
            'end_date' => ['date', 'required_with:start_date'],
        ]);

        $start_date = Carbon::now()->subMonths(11)->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::now()->endOfMonth()->format('Y-m-d');

        if (isset($request->start_date) && isset($request->end_date)) {
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d');
        }

        $tab = 'general';
        $tab = $request->input('slug') ?? $tab;

        $cacheKey = 'earning_analysis_'.md5($request->fullUrl());

        $earning = Cache::remember($cacheKey, 60 * 60 * 12, function () use ($start_date, $end_date) {
            return Earning::with('product', 'song')->whereBetween('sales_date', [$start_date, $end_date])->get();
        });

        $response = new AnalyseResource($earning, $tab);

        return inertia('Control/Finance/Analysis/Index', [
            'data' => $response->resolve()
        ]);
    }

    public function show(Request $request)
    {
        $request->validate(
            [
                'slug' => [
                    'required', 'string',
                    'in:earning_from_platforms,earning_from_countries,earning_from_sales_type,trending_albums,top_artists,top_albums,top_songs,top_labels,platforms,countries'
                ],
                'request_type' => ['required', 'string', 'in:view,download'],
                'start_date' => ['date', 'required_with:end_date'],
                'end_date' => ['date', 'required_with:start_date'],
            ]
        );

        $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
        $end_date = Carbon::parse($request->end_date)->format('Y-m-d');

        $earnings = Earning::with('product', 'song', 'platform', 'country', 'label')
            ->whereBetween('sales_date', [$start_date, $end_date])
            ->get();


        $service = new AnalyseService($earnings);

        $data = [];
        switch ($request->slug) {
            case 'earning_from_platforms':
                $data = $service->earningFromPlatforms();
                break;
            case 'earning_from_countries':
                $data = $service->earningFromCountries();
                break;
            case 'earning_from_sales_type':
                $data = $service->earningFromSalesType();
                break;
            case 'trending_albums':
                $data = $service->trendingAlbums();
                break;
            case 'top_artists':
                $data = $service->topArtists();
                break;
            case 'top_albums':
                $data = $service->topAlbums();
                break;
            case 'top_songs':
                $data = $service->topSongs();
                break;
            case 'top_labels':
                $data = $service->topLabels();
                break;
            case 'platforms':
                $data = $service->platforms();
                break;
            case 'countries':
                $data = $service->countries();
                break;
        }


        if ($request->request_type === 'view') {
            return $data;
        }

        if ($request->request_type === 'download') {
            //Excel export
        }


    }


    private function view($earnings)
    {
        return 'view';

    }

    private function download($earnings)
    {
        return 'download';
    }


}
