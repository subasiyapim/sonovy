<?php

namespace App\Jobs;

use App\Events\SongProcessingComplete;
use App\Events\SongProcessingStarted;
use App\Models\Product;
use App\Models\Song;
use App\Services\FFMpegServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessSongUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Yeniden deneme sayısı
     *
     * @var int
     */
    public $tries = 3;

    /**
     * İş başarısız olduğunda backoff stratejisi (saniye)
     *
     * @var array
     */
    public $backoff = [5, 15, 30];

    /**
     * Zamana aşımı süresi (saniye)
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * Dosya meta verileri
     *
     * @var array
     */
    protected $fileMeta;

    /**
     * Dosya yolu
     *
     * @var string
     */
    protected $filePath;

    /**
     * Depolama yolu
     *
     * @var string
     */
    protected $storagePath;

    /**
     * Kullanıcı ID
     *
     * @var int|null
     */
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @param array $fileMeta
     * @param string $filePath
     * @param string $storagePath
     * @param int|null $userId
     * @return void
     */
    public function __construct(array $fileMeta, string $filePath, string $storagePath, $userId = null)
    {
        $this->fileMeta = $fileMeta;
        $this->filePath = $filePath;
        $this->storagePath = $storagePath;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // İşlem başladığında bildir
            $productId = $this->fileMeta['metadata']['product_id'] ?? null;

            if (!$productId) {
                Log::error("Ürün ID'si belirtilmemiş", [
                    'file_meta' => $this->fileMeta,
                ]);
                return;
            }

            // Tenant ID'yi al - varsa metadata'dan, yoksa aktif tenant'tan
            $tenantId = $this->fileMeta['metadata']['tenant_id'] ?? (function_exists('tenant') && tenant() ? tenant()->id : null);

            // İşlem başladı event'ini yayınla
            broadcast(new SongProcessingStarted($productId, $this->fileMeta['name'], $tenantId));

            // Dosya varlığını kontrol et
            if (!file_exists($this->filePath)) {
                $errorMessage = "Dosya bulunamadı: " . $this->filePath;
                Log::error($errorMessage, [
                    'file_path' => $this->filePath,
                    'storage_path' => $this->storagePath,
                    'file_meta' => $this->fileMeta
                ]);
                broadcast(new SongProcessingComplete($productId, $this->fileMeta['name'], false, $errorMessage, $tenantId));
                return;
            }

            // Dosya boyutunu kontrol et
            $fileSize = filesize($this->filePath);
            if ($fileSize <= 0) {
                $errorMessage = "Dosya boş veya geçersiz: " . $this->filePath;
                Log::error($errorMessage, [
                    'file_path' => $this->filePath,
                    'file_size' => $fileSize,
                    'file_meta' => $this->fileMeta
                ]);
                broadcast(new SongProcessingComplete($productId, $this->fileMeta['name'], false, $errorMessage, $tenantId));
                return;
            }

            // Dosya izinlerini kontrol et
            if (!is_readable($this->filePath)) {
                $errorMessage = "Dosya okunabilir değil: " . $this->filePath;
                Log::error($errorMessage, [
                    'file_path' => $this->filePath,
                    'permissions' => substr(sprintf('%o', fileperms($this->filePath)), -4),
                    'file_meta' => $this->fileMeta
                ]);
                broadcast(new SongProcessingComplete($productId, $this->fileMeta['name'], false, $errorMessage, $tenantId));
                return;
            }

            // FFmpeg ile medya detaylarını al
            Log::info("Medya detayları alınıyor", [
                'file_path' => $this->filePath,
                'file_size' => $fileSize,
                'mime_type' => $this->fileMeta['metadata']['mime_type'] ?? 'unknown'
            ]);

            $details = FFMpegServices::getMediaDetails(file: $this->filePath);

            if (!$details['status']) {
                $errorMessage = $details['error'] ?? "Medya bilgileri alınamadı";
                Log::error("Medya bilgileri alınamadı: " . json_encode($details));
                broadcast(new SongProcessingComplete($productId, $this->fileMeta['name'], false, $errorMessage, $tenantId));
                return;
            }

            // Song verisini hazırla
            $data = [
                "type" => $this->fileMeta['metadata']['type'],
                "name" => $this->fileMeta['metadata']['originalName'],
                "path" => $this->fileMeta['name'],
                "mime_type" => $this->fileMeta['metadata']['mime_type'],
                "size" => $this->fileMeta['metadata']['size'],
                "duration" => $this->formatDuration($details['details']['duration']),
                "details" => $details,
                "created_by" => $this->userId,
            ];

            // Transaction başlat - veritabanı işlemlerinin bir bütün halinde yapılmasını sağlar
            DB::beginTransaction();
            try {
                // Song oluştur
                $file = Song::create($data);

                // Product ilişkisini oluştur
                $file->products()->attach([$productId]);

                // Ana sanatçıları ekle
                $product = Product::find($productId);
                if ($product) {
                    $mainArtists = $product->mainArtists()->pluck('artists.id')->toArray();
                    if (!empty($mainArtists)) {
                        $file->mainArtists()->attach($mainArtists);
                    }
                }

                // Transaction'ı commit et
                DB::commit();

                // İşlem tamamlandı event'ini yayınla
                broadcast(new SongProcessingComplete($productId, $this->fileMeta['name'], true, $file->id, $tenantId));

                Log::info('Şarkı yükleme işlemi tamamlandı', [
                    'song_id' => $file->id,
                    'product_id' => $productId,
                ]);
            } catch (\Throwable $e) {
                // Hata durumunda rollback
                DB::rollBack();

                Log::error("Dosya kaydedilirken hata: " . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);

                // Hata durumunu bildir
                broadcast(new SongProcessingComplete($productId, $this->fileMeta['name'], false, $e->getMessage(), $tenantId));
            }
        } catch (\Throwable $e) {
            Log::error("Dosya işleme sırasında hata: " . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            $productId = $this->fileMeta['metadata']['product_id'] ?? null;
            if ($productId) {
                // Tenant ID'yi al - varsa metadata'dan, yoksa aktif tenant'tan
                $tenantId = $this->fileMeta['metadata']['tenant_id'] ?? (function_exists('tenant') && tenant() ? tenant()->id : null);
                broadcast(new SongProcessingComplete($productId, $this->fileMeta['name'], false, $e->getMessage(), $tenantId));
            }
        }
    }

    /**
     * Süreyi formatla
     *
     * @param float $seconds
     * @return string
     */
    private function formatDuration($seconds): string
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }

    /**
     * İşlem başarısız olduğunda
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Şarkı yükleme işi başarısız oldu', [
            'exception' => $exception->getMessage(),
            'file_meta' => $this->fileMeta,
        ]);

        $productId = $this->fileMeta['metadata']['product_id'] ?? null;
        if ($productId) {
            // Tenant ID'yi al - varsa metadata'dan, yoksa aktif tenant'tan
            $tenantId = $this->fileMeta['metadata']['tenant_id'] ?? (function_exists('tenant') && tenant() ? tenant()->id : null);
            broadcast(new SongProcessingComplete($productId, $this->fileMeta['name'], false, 'İşlem başarısız oldu: ' . $exception->getMessage(), $tenantId));
        }
    }
}
