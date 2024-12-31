<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ReportStoreRequest;
use App\Jobs\IncomeReportJob;
use App\Models\Artist;
use App\Models\Label;
use App\Models\Platform;
use App\Models\Song;
use App\Models\Product;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('report_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $reports = Report::advancedFilter();
        $artists = getDataFromInputFormat(Artist::all(), 'id', 'name', 'image');
        $albums = getDataFromInputFormat(Product::all(), 'id', 'name', 'image');
        $labels = getDataFromInputFormat(Label::all(), 'value', 'label', 'image');
        $songs = getDataFromInputFormat(Song::all(), 'id', 'name');
        $countries = getDataFromInputFormat(\App\Models\System\Country::all(), 'id', 'name', 'emoji');

        $platforms = getDataFromInputFormat(Platform::all(), 'id', 'name', 'image');


        return inertia('Control/Finance/Reports/Index',
            compact('reports', 'artists', 'albums', 'labels', 'songs', 'countries', 'platforms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportStoreRequest $request)
    {
        $start_date = Carbon::parse($request->validated()['start_date'])->format('Y-m-d');
        $end_date = Carbon::parse($request->validated()['end_date'])->format('Y-m-d');
        $report_type = $request->validated()['report_type'];
        $ids = $request->validated()['ids'];

        //report_type da all dışındakilerin son harfi s silinmeli
        if ($report_type !== 'all') {
            $report_type = Str::singular($report_type);
        }

        IncomeReportJob::dispatch($start_date, $end_date, Auth::id(), $report_type, $ids);

        return to_route('dashboard.reports.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        //
    }

    public function download(Report $report)
    {
        $media = $report->getMedia('income-reports')->last();

        if ($media) {
            $path = $media->getPath();
            $fileName = $media->file_name;

            if (file_exists($path)) {
                return response()->streamDownload(function () use ($path) {
                    readfile($path);
                }, $fileName);
            } else {
                return response()->json(['error' => 'File does not exist'], 404);
            }
        }

        return response()->json(['error' => 'No media found'], 404);
    }
}
