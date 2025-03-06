<?php

namespace App\Http\Controllers\Control;

use App\Enums\EarningReportStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ReportStoreRequest;
use App\Http\Resources\Report\ReportResource;
use App\Jobs\AutomaticIncomeReportJob;
use App\Jobs\ProcessEarningReportJob;
use App\Jobs\RequestedIncomeReportJob;
use App\Models\Artist;
use App\Models\Earning;
use App\Models\Label;
use App\Models\Platform;
use App\Models\Song;
use App\Models\Product;
use App\Models\Report;
use App\Models\System\Country;
use App\Services\CountryServices;
use App\Models\EarningReport;
use App\Models\EarningReportFile;
use App\Imports\EarningImport;
use App\Enums\EarningReportFileStatusEnum;
use App\Exports\ReportExport;
use App\Services\MediaServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Inertia\ResponseFactory;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;
use App\Jobs\CreateDemoEarningsJob;
use App\Models\User;
use App\Http\Resources\EarningReportResource;
use Illuminate\Support\Facades\Artisan;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response|ResponseFactory
    {
        // abort_if(Gate::denies('report_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'slug' => ['nullable', 'string', 'in:auto-reports,demanded-reports'],
            'demo' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('demo')) {
            Artisan::call('demo:create-earnings');
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
        //abort_if(Gate::denies('report_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'file' => [
                'required',
                'file',
                'max:51200', // 50MB limit
                function ($attribute, $value, $fail) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    $mimeType = $value->getMimeType();

                    $validExtensions = ['xlsx', 'xls', 'csv'];
                    $validMimeTypes = [
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'text/comma-separated-values'
                    ];

                    if (!in_array($extension, $validExtensions) || !in_array($mimeType, $validMimeTypes)) {
                        $fail('Dosya biçimi xlsx, xls veya csv olmalıdır.');
                    }
                }
            ],
            'platform_id' => ['required', 'integer', 'exists:platforms,id'],
            'name' => ['required', 'string'],
            'report_date' => ['required', 'date'],
            'report_language' => ['required', 'string', 'in:en,tr']
        ]);

        try {
            DB::beginTransaction();

            // Rapor dosyasını kaydet
            $reportFile = EarningReportFile::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'is_processed' => false,
                'report_language' => $request->report_language,
                'status' => EarningReportFileStatusEnum::PENDING,
                'total_rows' => 0,
                'processed_rows' => 0,
                'error_rows' => 0,
                'errors' => []
            ]);

            Log::info('Rapor dosyası oluşturuldu', [
                'report_file_id' => $reportFile->id,
                'user_id' => Auth::id(),
                'name' => $request->name,
                'language' => $request->report_language
            ]);

            // Excel import işlemini başlat
            $isEnglishFormat = $request->report_language === 'en';

            // Medya koleksiyonu için disk adını belirle ve disk mevcutsa işlemi gerçekleştir
            $disk = 'earning_report_files';

            $file = $request->file('file');

            if (!$file || !$file->isValid()) {
                return response()->json(['error' => 'Dosya yüklenirken hata oluştu'], 400);
            }

            // Dosyayı geçici bir konuma kaydet
            $tempPath = $file->storeAs('temp', $file->getClientOriginalName(), 'local');
            $fullPath = Storage::disk('local')->path($tempPath);

            try {
                // Excel import işlemini queue ile gerçekleştir
                Excel::import(
                    new EarningImport(
                        $isEnglishFormat,
                        $reportFile->id,
                        $request->report_date,
                        $fullPath
                    ),
                    Storage::disk('local')->path($tempPath)
                );

                // MediaServices ile dosyayı kaydet
                MediaServices::upload($reportFile, $file, $disk, $disk);

                // Geçici dosyayı temizle
                Storage::disk('local')->delete($tempPath);

                DB::commit();

                return response()->json([
                    'message' => 'Rapor başarıyla yüklendi ve işleme alındı. Büyük dosyalar için işlem biraz zaman alabilir.',
                    'report_file' => $reportFile
                ]);
            } catch (\Exception $e) {
                // Hata durumunda geçici dosyayı temizle
                Storage::disk('local')->delete($tempPath);
                throw $e;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Rapor yükleme hatası: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'file_name' => $request->file('file')->getClientOriginalName(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Rapor yüklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Download file by EarningReportFile
     */
    public function downloadFile($fileId)
    {
        try {
            $file = EarningReportFile::findOrFail($fileId);

            // Yetki kontrolü
            if (Gate::denies('report_download') || $file->user_id !== Auth::id()) {
                abort(Response::HTTP_FORBIDDEN, '403 Forbidden');
            }


            $media = $file->getMedia('earning_report_files')->last();

            if (!$media) {
                return response()->json(['error' => 'Dosya bulunamadı'], 404);
            }

            $filePath = $media->getPath();

            if (!file_exists($filePath)) {
                return response()->json(['error' => 'Fiziksel dosya bulunamadı'], 404);
            }

            return response()->streamDownload(function () use ($filePath) {
                readfile($filePath);
            }, $media->file_name ?? 'rapor.xlsx');
        } catch (\Exception $e) {
            Log::error('Dosya indirme hatası: ' . $e->getMessage(), [
                'file_id' => $fileId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Dosya indirilirken bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function reportFiles(Request $request)
    {
        $request->validate([
            'platform' => ['nullable', 'integer', 'exists:platforms,id'],
            'status' => ['nullable']
        ]);

        $platforms = Platform::all();
        $statuses = enumToSelectInputFormat(EarningReportFileStatusEnum::getTitles());
        $filters = [
            [
                "title" => "Platformlar",
                "param" => "platform",
                "options" => getDataFromInputFormat($platforms, 'id', 'name', null)
            ],
            [
                'title' => 'Durum',
                'param' => 'status',
                'options' => $statuses,
            ]
        ];

        $earningReports = EarningReport::with('reportFile', 'platform')
            ->when(request('platform'), function ($query) {
                $query->where('platform_id', request('platform'));
            })
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->advancedFilter();

        $earningReports = EarningReportResource::collection($earningReports)->resource;
        return inertia('Control/Finance/Imports/index', compact('earningReports', 'platforms', 'statuses', 'filters'));
    }

    public function participantReports(Request $request)
    {
        $earnings = Earning::query()
            ->join('users', 'users.id', '=', 'earnings.user_id') // Ensure correct user join
            ->select(
                'earnings.platform',
                'earnings.platform_id',
                'users.id as user_id',
                'earnings.report_date',
                DB::raw('SUM(earnings.earning) as user_total_earning'),
                DB::raw('SUM((earnings.earning * users.commission_rate)) as service_provider_earning'),

            )
            ->groupBy('earnings.platform_id', 'earnings.platform', 'earnings.report_date', 'users.id')
            ->get()
            ->map(function ($earning) {
                return [
                    'id' => $earning->id,
                    'user' => [
                        'id' => $earning?->user?->id,
                        'name' => $earning?->user?->name,
                        'email' => $earning?->user?->email,
                        'commission_rate' => $earning?->user?->commission_rate,
                    ],
                    'platform' => $earning->platform,
                    'participant_earning' => Number::currency($earning->user_total_earning, 'USD', app()->getLocale()),
                    'total_earning' => Number::currency(
                        $earning->user_total_earning * ($earning->user?->commission_rate ?? 0), 'USD',
                        app()->getLocale()
                    ),
                    'provider_earning' => Number::currency(($earning->user_total_earning * ($earning->user?->commission_rate ?? 0)) - $earning->user_total_earning, 'USD',
                        app()->getLocale()
                    ),
                    'participant_rate' => (1 - $earning->client_share_rate) * 100,
                    'platform_id' => $earning->platform_id,
                    'report_date' => $earning->report_date,
                ];
            });

        return inertia('Control/Finance/Imports/participants', [
            'earnings' => $earnings
        ]);
    }

    public function exportParticipantReports(Request $request)
    {
        $request->validate([
            'platform_id' => ['required', 'exists:platforms,id'],
            'user_id' => ['required', 'exists:users,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date']
        ]);

        $query = Earning::query()
            ->select(
                'report_date',
                'sales_date',
                'platform',
                'country',
                'label_name',
                'artist_name',
                'release_name',
                'song_name',
                'upc_code',
                'isrc_code',
                'catalog_number',
                'release_type',
                'sales_type',
                'quantity',
                'currency',
                'earning',
                'client_share_rate'
            )
            ->where('user_id', $request->user_id)
            ->where('platform_id', $request->platform_id);

        if ($request->filled('start_date')) {
            $query->where('report_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('report_date', '<=', $request->end_date);
        }

        $earnings = $query->get();
        $platform = Platform::find($request->platform_id);
        $user = User::findOrFail($request->user_id);

        // Yeni bir rapor oluştur
        $report = Report::create([
            'name' => $user->name . ' - ' . $platform->name . ' Kazanç Raporu',
            'user_id' => $request->user_id,
            'period' => Carbon::now()->format('Y-m'),
            'status' => 0,
            'report_type' => 'participant_earnings'
        ]);

        $period = Carbon::now()->format('Y-m-d_H-i-s');
        $reportExport = new ReportExport($earnings, $period, $report);

        return Excel::download($reportExport, "{$user->name}_{$platform->name}_participant_earnings_{$period}.xlsx");
    }
}
