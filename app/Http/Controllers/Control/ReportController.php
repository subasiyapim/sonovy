<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ReportStoreRequest;
use App\Http\Resources\Report\ReportResource;
use App\Jobs\AutomaticIncomeReportJob;
use App\Jobs\RequestedIncomeReportJob;
use App\Models\Artist;
use App\Models\Label;
use App\Models\Platform;
use App\Models\Song;
use App\Models\Product;
use App\Models\Report;
use App\Models\System\Country;
use App\Services\CountryServices;
use App\Services\EarningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\ResponseFactory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response|ResponseFactory
    {
        abort_if(Gate::denies('report_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            'slug' => ['nullable', 'string', 'in:auto-reports,demanded-reports'],
            'demo' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('demo')) {
            EarningService::createDemoEarnings();
        }

        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");

        $query = Report::query();

        $isAutoReport = true;

        if (!empty($request->slug)) {
            $isAutoReport = $request->slug === 'auto-reports';
        }

        $reportsWithBatchIdQuery = Report::where('is_auto_report', $isAutoReport)
            ->where('user_id', Auth::id())
            ->whereNotNull('batch_id')
            ->groupBy('batch_id')
            ->selectRaw('*, SUM(amount) as total_amount');

        $reportsWithoutBatchIdQuery = Report::where('is_auto_report', $isAutoReport)
            ->where('user_id', Auth::id())
            ->whereNull('batch_id');

        $reportsWithBatchId = $reportsWithBatchIdQuery->orderByDesc('id')->advancedFilter();  // 10, sayfa başına gösterilecek öğe sayısı
        $reportsWithoutBatchId = $reportsWithoutBatchIdQuery->orderByDesc('id')->advancedFilter(); // Aynı sayfa başına öğe sayısı

        $mergedReports = $reportsWithBatchId->getCollection()->merge($reportsWithoutBatchId->getCollection());

        $mergedReports = new \Illuminate\Pagination\LengthAwarePaginator(
            $mergedReports,
            $reportsWithBatchId->total() + $reportsWithoutBatchId->total(), // Toplam öğe sayısı
            $reportsWithBatchId->perPage(),
            $reportsWithBatchId->currentPage(),
            ['path' => \Request::url()]
        );

        $reports = ReportResource::collection($mergedReports)->resource;

        DB::statement("SET SESSION sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'))");

        $artists = Artist::with('platforms')->get();
        $albums = getDataFromInputFormat(Product::all(), 'id', 'name', 'image');
        $labels = Label::all();
        $songs = getDataFromInputFormat(Song::all(), 'id', 'name');
        $countries = getDataFromInputFormat(Country::all(), 'id', 'name', 'emoji');
        $products = Product::all();
        $platforms = Platform::all();
        $countriesGroupedByRegion = CountryServices::getCountriesGroupedByRegion();

        return inertia(
            'Control/Finance/Reports/Index',
            compact('reports', 'artists', 'albums', 'labels', 'songs', 'countries', 'platforms', 'products',
                'countriesGroupedByRegion')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportStoreRequest $request): JsonResponse|RedirectResponse
    {
        $start_date = Carbon::parse($request->validated()['start_date'])->format('Y-m-d');
        $end_date = Carbon::parse($request->validated()['end_date'])->format('Y-m-d');
        $report_type = $request->validated()['report_type'];
        $ids = $request->validated()['ids'];

        switch ($report_type) {
            case 'all':
            case 'artists':
            case 'songs':
            case 'platforms':
            case 'products':
            case 'countries':
            case 'labels':
                AutomaticIncomeReportJob::dispatch($start_date, $end_date, Auth::id(), $report_type, $ids);
                break;
            case 'multiple_artists':
            case 'multiple_songs':
            case 'multiple_platforms':
            case 'multiple_products':
            case 'multiple_countries':
            case 'multiple_labels':
                RequestedIncomeReportJob::dispatch($start_date, $end_date, Auth::id(), $report_type, $ids);
                break;
            default:
                return response()->json(['error' => 'Invalid report type'], 400);
        }

        return to_route('control.finance.reports.index');
    }

    public function download(Report $report): BinaryFileResponse|StreamedResponse|JsonResponse
    {
        if ($report->whereNotNull('batch_id')->count() > 1) {

            $zipFilePath = storage_path('app/public/tenant_'.tenant('domain').'_income_reports/multiple_reports/'.$report->user_id.'/'.Str::slug($report->period).'.zip');

            $zip = new ZipArchive;

            if ($zip->open($zipFilePath, ZipArchive::CREATE) === true) {

                $files = Storage::disk('public')->allFiles('tenant_'.tenant('domain').'_income_reports/multiple_reports/'.$report->user_id.'/'.Str::slug($report->period));

                foreach ($files as $file) {
                    $fullPath = Storage::disk('public')->path($file);

                    $zip->addFile($fullPath, basename($file));  // 'basename($file)' dosya adını ekler
                }

                $zip->close();

                return response()->download($zipFilePath)->deleteFileAfterSend(true);
            } else {
                return response()->json(['error' => 'Could not create zip file'], 500);
            }
        }


        $media = $report->getMedia('tenant_'.tenant('domain').'_income_reports')->last();

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

    public function destroy(Report $report): RedirectResponse
    {
        abort_if(Gate::denies('report_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $report->delete();

        return redirect()->back();
    }


}
