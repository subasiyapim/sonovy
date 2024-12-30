<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ReportStoreRequest;
use App\Jobs\IncomeReportJob;
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
        // abort_if(Gate::denies('report_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $reports = Report::advancedFilter();

        return inertia('Control/Finance/Reports/Index', compact('reports'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportStoreRequest $request)
    {
        $start_date = Carbon::parse($request->date_1)->format('Y-m-d');
        $end_date = Carbon::parse($request->date_2)->format('Y-m-d');
        $report_type = $request->report_type;
        $data = $request->report_data;

        //report_type da all dışındakilerin son harfi s silinmeli
        if ($report_type !== 'all') {
            $report_type = Str::singular($report_type);
        }

        IncomeReportJob::dispatch($start_date, $end_date, Auth::id(), $report_type, $data);

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
