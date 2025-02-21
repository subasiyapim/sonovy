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
use App\Models\User;
use App\Services\CountryServices;
use App\Services\EarningService;
use App\Models\EarningReport;
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
use App\Jobs\CreateDemoEarningsJob;
use App\Models\EarningReportFile;
use App\Jobs\ProcessEarningReportJob;
use App\Imports\EarningImport;
use App\Jobs\EarningJob;
use Maatwebsite\Excel\Facades\Excel;

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
            CreateDemoEarningsJob::dispatch();
        }

        $isAutoReport = true;

        if (!empty($request->slug)) {
            $isAutoReport = $request->slug === 'auto-reports';
        }

        $query = Report::with([
            'child' => function ($query) {
                $query->select(
                    'id',
                    'parent_id',
                    'name',
                    'amount',
                    'report_type',
                    'report_ids',
                    'status',
                    'created_at',
                    'period',
                    'monthly_amount'
                );
            }
        ])
            ->select(
                'id',
                'name',
                'amount',
                'report_type',
                'report_ids',
                'status',
                'created_at',
                'period',
                'monthly_amount',
                'parent_id'
            )
            ->whereNull('parent_id')
            ->where('is_auto_report', $isAutoReport)
            ->where('user_id', Auth::id())
            ->advancedFilter();

        $reports = ReportResource::collection($query)->resource;

        //dd($reports);
        $artists = Artist::with('platforms')->get();
        $labels = Label::all();
        $songs = Song::all();
        $countries = getDataFromInputFormat(Country::all(), 'id', 'name', 'emoji');
        $products = Product::all();

        $platforms = Platform::all();
        $countriesGroupedByRegion = CountryServices::getCountriesGroupedByRegion();

        return inertia(
            'Control/Finance/Reports/Index',
            compact(
                'reports',
                'artists',
                'labels',
                'songs',
                'countries',
                'platforms',
                'products',
                'countriesGroupedByRegion'
            )
        );
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportStoreRequest $request): JsonResponse|RedirectResponse
    {
        $start_date = Carbon::createFromFormat(
            'm-Y',
            $request->validated()['start_date']
        )->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::createFromFormat('m-Y', $request->validated()['end_date'])->endOfMonth()->format('Y-m-d');
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
        if ($report->child()->count() > 1) {
            return $this->handleMultipleReports($report);
        }

        $media = $report->getMedia('tenant_' . tenant('domain') . '_income_reports')->last();
        if ($media) {
            return $this->streamMediaFile($media);
        }

        return response()->json(['error' => 'No media found'], 404);
    }

    private function handleMultipleReports(Report $report): BinaryFileResponse|JsonResponse
    {
        $zipFilePath = $this->getZipFilePath($report);
        $zip = new ZipArchive;

        if ($zip->open($zipFilePath, ZipArchive::CREATE) !== true) {
            return response()->json(['error' => 'Could not create zip file'], 500);
        }

        $files = $this->getReportFiles($report);
        foreach ($files as $file) {
            $zip->addFile(Storage::disk('public')->path($file), basename($file));
        }
        $zip->close();

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    private function getZipFilePath(Report $report): string
    {
        return storage_path(
            'app/public/tenant_' . tenant('domain') . '_income_reports/multiple_reports/' .
                $report->user_id . '/' . Str::slug($report->period) . '-' . Str::slug($report->name) . '.zip'
        );
    }

    private function getReportFiles(Report $report): array
    {
        return Storage::disk('public')->allFiles(
            'tenant_' . tenant('domain') . '_income_reports/multiple_reports/' .
                $report->user_id . '/' . Str::slug($report->period) . '/' . $report->id
        );
    }

    private function streamMediaFile($media): StreamedResponse|JsonResponse
    {
        $path = $media->getPath();
        $fileName = $media->file_name;

        if (!file_exists($path)) {
            return response()->json(['error' => 'File does not exist'], 404);
        }

        return response()->streamDownload(function () use ($path) {
            readfile($path);
        }, $fileName);
    }

    public function destroy(Report $report): RedirectResponse
    {
        abort_if(Gate::denies('report_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            DB::beginTransaction();

            // Önce medya dosyalarını sil
            $report->clearMediaCollection('tenant_' . tenant('domain') . '_income_reports');

            // Child raporları bul ve medya dosyalarını sil
            $childReports = $report->child()->get();
            foreach ($childReports as $childReport) {
                $childReport->clearMediaCollection('tenant_' . tenant('domain') . '_income_reports');
                $childReport->delete();
            }

            // Ana raporu sil
            $report->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Rapor ve ilişkili tüm veriler başarıyla silindi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Rapor silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function show(Report $report)
    {
        abort_if(Gate::denies('report_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return inertia('Control/Finance/Reports/Show', [
            'report' => new ReportResource($report)
        ]);
    }

    public function uploadFile(Request $request)
    {


        ini_set('memory_limit', '512M');
        ini_set('upload_max_filesize', '200M');
        ini_set('post_max_size', '200M');
        ini_set('max_execution_time', '300');
        // abort_if(Gate::denies('report_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            'file' => ['required', 'file'],
            'platform_id' => ['required', 'integer', 'exists:platforms,id'],
            'name' => ['required', 'string'],
            'report_date' => ['required', 'date'],
        ]);

        try {
            DB::beginTransaction();

            // Excel dosyasını import et ve raporları oluştur
            // EarningImport sınıfı doğrudan EarningReport kayıtlarını oluşturacak
            Excel::import(new EarningImport(), $request->file('file'));

            // Kazanç hesaplama işini kuyruğa ekle
            EarningJob::dispatch();

            DB::commit();

            return response()->json([
                'message' => 'Rapor başarıyla yüklendi ve işleme alındı.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Rapor yüklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filter reports by platform
     */
    public function filterByPlatform(Platform $platform)
    {
        abort_if(Gate::denies('report_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $reports = EarningReport::where('platform_id', $platform->id)
            ->where('user_id', Auth::id())
            ->advancedFilter();

        return response()->json([
            'reports' => ReportResource::collection($reports)
        ]);
    }

    /**
     * Filter reports by period
     */
    public function filterByPeriod(string $period)
    {
        abort_if(Gate::denies('report_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $reports = EarningReport::where('period', $period)
            ->where('user_id', Auth::id())
            ->advancedFilter();

        return response()->json([
            'reports' => ReportResource::collection($reports)
        ]);
    }

    /**
     * Filter reports by type
     */
    public function filterByType(string $type)
    {
        abort_if(Gate::denies('report_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $reports = EarningReport::where('report_type', $type)
            ->where('user_id', Auth::id())
            ->advancedFilter();

        return response()->json([
            'reports' => ReportResource::collection($reports)
        ]);
    }

    /**
     * Filter reports by status
     */
    public function filterByStatus(string $status)
    {
        abort_if(Gate::denies('report_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $reports = EarningReport::where('status', $status)
            ->where('user_id', Auth::id())
            ->advancedFilter();

        return response()->json([
            'reports' => ReportResource::collection($reports)
        ]);
    }

    /**
     * Download multiple reports as zip
     */
    public function bulkDownload(Request $request): BinaryFileResponse
    {
        abort_if(Gate::denies('report_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'report_ids' => ['required', 'array'],
            'report_ids.*' => ['exists:earning_reports,id']
        ]);

        $reports = EarningReport::whereIn('id', $request->report_ids)
            ->where('user_id', Auth::id())
            ->get();

        $zip = new ZipArchive;
        $fileName = 'reports-' . now()->format('Y-m-d-H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $fileName);

        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            foreach ($reports as $report) {
                $reportName = Str::slug($report->period) . '-' . Str::slug($report->name) . '.xlsx';
                // Excel dosyasını oluştur ve zip'e ekle
                // Bu kısım Excel export işlemini içerecek
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend();
    }

    /**
     * Delete multiple reports
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        abort_if(Gate::denies('report_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'report_ids' => ['required', 'array'],
            'report_ids.*' => ['exists:earning_reports,id']
        ]);

        try {
            DB::beginTransaction();

            $reports = EarningReport::whereIn('id', $request->report_ids)
                ->where('user_id', Auth::id())
                ->get();

            foreach ($reports as $report) {
                $report->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Seçili raporlar başarıyla silindi.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Raporlar silinirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export reports summary
     */
    public function exportSummary(Request $request)
    {
        abort_if(Gate::denies('report_export'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'platform_id' => ['nullable', 'exists:platforms,id'],
            'type' => ['nullable', 'string']
        ]);

        $query = EarningReport::query()
            ->where('user_id', Auth::id());

        if ($request->filled('start_date')) {
            $query->where('report_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('report_date', '<=', $request->end_date);
        }

        if ($request->filled('platform_id')) {
            $query->where('platform_id', $request->platform_id);
        }

        if ($request->filled('type')) {
            $query->where('report_type', $request->type);
        }

        $reports = $query->get();

        // Excel export işlemi burada yapılacak
        // return Excel::download(new ReportsExport($reports), 'reports-summary.xlsx');
    }

    public function reportFiles(Request $request)
    {
        $user = Auth::user();

        $user->load('earningReports');

        $platforms = Platform::all();
        return inertia('Control/Finance/Imports/index', compact('user', 'platforms'));
    }
}
