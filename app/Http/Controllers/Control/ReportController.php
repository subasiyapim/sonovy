<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ReportStoreRequest;
use App\Http\Resources\Report\ReportResource;
use App\Jobs\AutomaticIncomeReportJob;
use App\Jobs\RequestedIncomeReportJob;
use App\Models\Artist;
use App\Models\Earning;
use App\Models\Label;
use App\Models\Platform;
use App\Models\Song;
use App\Models\Product;
use App\Models\Report;
use App\Services\EarningService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Support\MediaStream;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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

        $isAutoReport = true; // Varsayılan olarak true

        if (!empty($request->slug)) {
            $isAutoReport = $request->slug === 'auto-reports';
        }

        $query->where('is_auto_report', $isAutoReport)
            ->where('user_id', Auth::id())->groupBy('batch_id');


        $reports = ReportResource::collection($query->advancedFilter())->resource;
        DB::statement("SET SESSION sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'))");

        $artists = Artist::with('platforms')->get();
        $albums = getDataFromInputFormat(Product::all(), 'id', 'name', 'image');
        $labels = getDataFromInputFormat(Label::all(), 'value', 'label', 'image');
        $songs = getDataFromInputFormat(Song::all(), 'id', 'name');
        $countries = getDataFromInputFormat(\App\Models\System\Country::all(), 'id', 'name', 'emoji');
        $products = Product::all();
        $platforms = Platform::all();


        return inertia(
            'Control/Finance/Reports/Index',
            compact('reports', 'artists', 'albums', 'labels', 'songs', 'countries', 'platforms', 'products')
        );
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


        $single_report_types = ['artists', 'songs', 'platforms', 'products', 'countries', 'labels'];
        $requested_report_types = [
            'multiple_artists', 'multiple_songs', 'multiple_platforms', 'multiple_products', 'multiple_countries',
            'multiple_labels'
        ];

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

    public function download(Report $report)
    {
        // Eğer batch_id'yi kontrol ediyoruz
        if ($report->whereNotNull('batch_id')->count() > 1) {

            // Path'leri oluşturuyoruz
            $path = storage_path('app/public/tenant_'.tenant('domain').'_income_reports/multiple_reports/'.$report->user_id.'/'.Str::slug($report->period));

            // Zip dosyasını oluşturacağız
            $zipFilePath = storage_path('app/public/tenant_'.tenant('domain').'_income_reports/multiple_reports/'.$report->user_id.'/'.Str::slug($report->period).'.zip');

            // ZipArchive nesnesi oluşturuyoruz
            $zip = new \ZipArchive;

            // Zip dosyasını açıyoruz
            if ($zip->open($zipFilePath, \ZipArchive::CREATE) === true) {

                // Dizindeki dosyaları alıyoruz
                $files = Storage::disk('public')->allFiles('tenant_'.tenant('domain').'_income_reports/multiple_reports/'.$report->user_id.'/'.Str::slug($report->period));

                // Dosyaları zip'e ekliyoruz
                foreach ($files as $file) {
                    // Dosya yolunu almak için Storage::disk('public')->path() kullanıyoruz
                    $fullPath = Storage::disk('public')->path($file);

                    // Dosyayı zip'e ekliyoruz
                    $zip->addFile($fullPath, basename($file));  // 'basename($file)' dosya adını ekler
                }

                // Zip dosyasını kapatıyoruz
                $zip->close();

                // Zip dosyasını kullanıcıya indirtiyoruz
                return response()->download($zipFilePath)->deleteFileAfterSend(true);
            } else {
                // Zip oluşturulamazsa hata döndürüyoruz
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
}
