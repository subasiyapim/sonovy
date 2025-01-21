<?php

namespace App\Http\Controllers\Control;

use App\Enums\EarningReportStatusEnum;
use App\Exports\FakerEarningReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Earning\EarningReportRequest;
use App\Http\Requests\Earning\UploadCSVRequest;
use App\Imports\EarningImport;
use App\Models\System\Country;
use App\Models\Earning;
use App\Models\EarningReport;
use App\Models\EarningReportFile;
use App\Models\Platform;
use App\Models\Song;
use App\Services\DDEXService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class EarningReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('earning_report_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $reports = EarningReport::with('file.user', 'user')->advancedFilter();
        $statuses = EarningReportStatusEnum::getTitles();

        $earnings = Earning::all();
        $earningReports = EarningReport::all();
        $earninReportFiles = EarningReportFile::all();

//        foreach ($earnings as $earning) {
//            $earning->delete();
//        }
//
//        foreach ($earningReports as $earningReport) {
//            $earningReport->delete();
//        }
//
//        foreach ($earninReportFiles as $earninReportFile) {
//            $earninReportFile->delete();
//        }

        return inertia('Control/Earnings/Reports/Index', compact('reports', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EarningReportRequest $request)
    {
        // Fetch the song with broadcasts and artists, using the provided ISRC code
        $song = Song::with('broadcasts.artists')
            ->whereNotNull('isrc')
            ->where('isrc', $request->input('isrc_code'))
            ->firstOrFail();

        $sales_type = $request->input('sales_type');
        $earning = number_format($request->input('net_revenue'), 15, '.', '');

        if ($sales_type == 'PLATFORM PROMOTION') {
            $earning = -abs($earning);
        }

        // Get the first product associated with the song
        $product = $song->broadcasts()->firstOrFail();

        // Build the earnings array
        $earnings = [
            [
                'report_date' => $request->input('report_date'),
                'sales_date' => $request->input('sales_date'),
                'platform' => Platform::findOrFail($request->input('platform_id'))->name,
                'country' => Country::findOrFail($request->input('country_id'))->name,
                'label_name' => $product->label->name ?? 'N/A',
                'artist_name' => $product->artists->first()->name ?? 'N/A',
                'release_name' => $product->name ?? 'N/A',
                'song_name' => $song->name ?? 'N/A',
                'upc_code' => $product->upc_code ?? 'N/A',
                'isrc_code' => $request->input('isrc_code'),
                'catalog_number' => $product->catalog_number ?? 'N/A',
                'release_type' => 'Yayın',
                'sales_type' => $request->input('sales_type'),
                'quantity' => 1,
                'currency' => 'EUR',
                'unit_price' => '',
                'earning' => $earning,
            ]
        ];


        // Define the CSV file name and path
        $file_name = 'earnings-'.time().'.csv';
        $file_path = storage_path('app/public/'.$file_name);

        // Export the earnings data to a CSV file
        $export_file = Excel::store(new FakerEarningReport($earnings), $file_name, 'public',
            \Maatwebsite\Excel\Excel::CSV);

        // If the file was successfully exported, process and save it
        if ($export_file) {
            $file = EarningReportFile::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'is_processed' => true,
                'processed_at' => now(),
            ]);

            // Import the earnings data from the CSV file
            Excel::import(new EarningImport, $file_path, null, \Maatwebsite\Excel\Excel::CSV);

            // Attach the file to the media collection
            $file->addMediaFromPath($file_path)->toMediaCollection('earning_report_files');
        }

        Storage::disk('public')->delete($file_name);

        // Redirect back to the earnings index with a success notification
        return redirect()->route('dashboard.earnings.index')
            ->with([
                'notification' => __('control.notification_created',
                    ['model' => __('control.earning_report.title_singular')])
            ]);
    }

    public function fileIndex()
    {
        $files = EarningReportFile::with('user')->advancedFilter();

        return inertia('Control/Earnings/Files/Index', compact('files'));
    }

    public function uploadFile(UploadCSVRequest $request)
    {

        $file = EarningReportFile::create(
            [
                'user_id' => Auth::id(),
                'name' => $request->name,
                'is_processed' => true,
                'processed_at' => now(),
            ]
        );
        Excel::import(new EarningImport, $request->file('file'), null, \Maatwebsite\Excel\Excel::CSV);
        $file->addMediaFromRequest('file')->toMediaCollection('earning_report_files');


    }

    /**
     * Display the specified resource.
     */
    public function show(EarningReport $earningReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EarningReport $earningReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EarningReport $earningReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EarningReport $earningReport)
    {
        dd($earningReport);
    }

    public function deleteFile(EarningReportFile $earningReportFile)
    {
        $earningReportFile->delete();

        return redirect()->route('dashboard.uploaded-earning-report-index')
            ->with(['notification' => __('control.notification_deleted', ['model' => __('Rapor dosyası')])]);
    }


    public function processAgain(Request $request)
    {
        $request->validate([
            'earning_report_ids' => 'required|array',
        ]);

        $earningReportIds = $request->input('earning_report_ids');
        $earning_reports = EarningReport::whereIn('id', $earningReportIds)->get();
        $earning_reports->load('earnings');

        $earning_reports->each(function ($earning_report) {
            $earning_report->earnings()->delete();
        });

        // Use `each` to update status for each earning report individually
        $earning_reports->each(function ($earning_report) {
            $earning_report->update(['status' => EarningReportStatusEnum::PENDING->value]);
        });

        return redirect()->route('dashboard.earnings.index')
            ->with([
                'notification' => __('control.notification_updated',
                    ['model' => __('control.earning_report.title_singular')])
            ]);
    }
}
