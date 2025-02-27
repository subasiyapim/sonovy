<?php

namespace App\Jobs;

use App\Models\EarningReportFile;
use App\Imports\EarningImport;
use App\Enums\EarningReportFileStatusEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Database\DatabaseConfig;

class ProcessEarningReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = false;
    public $tries = 3;
    public $timeout = 3600;
    protected $reportFile;
    protected $isEnglishFormat;

    public function __construct(EarningReportFile $earningReportFile)
    {
        $this->reportFile = $earningReportFile;
    }


    public function handle()
    {
        try {
            $this->reportFile->update([
                'status' => EarningReportFileStatusEnum::PROCESSING
            ]);

            // Disk ve koleksiyon adını düzelt
            $disk = 'earning_report_files';
            $media = $this->reportFile->getFirstMedia($disk);

            if (!$media) {
                throw new \Exception('Rapor dosyası bulunamadı');
            }

            Log::info('Rapor işleme başladı', [
                'media_path' => $media->getPath(),
                'file_name' => $media->file_name,
                'disk' => $media->disk,
                'collection_name' => $media->collection_name
            ]);

            // Excel import işlemini başlat
            $reportLanguage = $this->reportFile->report_language === 'en';

            Excel::import(new EarningImport($this->reportFile, $reportLanguage), $media->getPath(), $media->disk);


            $finalStatus = $this->reportFile->error_rows > 0
                ? EarningReportFileStatusEnum::COMPLETED_WITH_ERRORS
                : EarningReportFileStatusEnum::COMPLETED;

            $this->reportFile->update([
                'status' => $finalStatus,
                'is_processed' => true,
                'processed_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('ProcessEarningReportJob hatası', [
                'file_id' => $this->reportFile->id,
                'file_name' => $this->reportFile->name,
                'error' => $e->getMessage()
            ]);

            $this->reportFile->update([
                'is_processed' => true,
                'processed_at' => now(),
                'errors' => [
                    [
                        'message' => $e->getMessage(),
                        'timestamp' => now()->format('Y-m-d H:i:s')
                    ]
                ],
                'status' => EarningReportFileStatusEnum::FAILED
            ]);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('ProcessEarningReportJob failed', [
            'error' => $exception->getMessage()
        ]);
    }
}
