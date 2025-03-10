<?php

namespace App\Providers;

use App\Enums\SongTypeEnum;
use App\Jobs\ProcessSongUpload;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Song;
use App\Services\FFMpegServices;
use Error;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use TusPhp\Tus\Server as TusServer;

class TusServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('tus-server', function ($app) {
            $server = new TusServer();

            // Tenant'a özgü depolama yolunu önbellekle
            $storagePath = Cache::remember('tus_storage_path_'.tenant('domain'), 60*24, function() {
                $path = storage_path('app/public/tenant_' . tenant('domain') . '_songs');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0775, true, true);
                }
                return $path;
            });

            // API yolunu da önbellekle
            $apiPath = Cache::remember('tus_api_path_'.tenant('domain'), 60*24, function() {
                return '/control/tus';
            });

            $server->setApiPath($apiPath);
            $server->setUploadDir($storagePath);

            // Upload tamamlandı olayını dinle
            $server->event()->addListener(
                'tus-server.upload.complete',
                function (\TusPhp\Events\TusEvent $event) use ($storagePath) {
                    try {
                        $fileMeta = $event->getFile()->details();
                        $metaType = $fileMeta['metadata']['type'] ?? null;

                        if (!$metaType) {
                            Log::error("Dosya tipi belirtilmemiş");
                            $event->getResponse()->setHeaders(['error_message' => 'Dosya tipi belirtilmemiş']);
                            return;
                        }

                        $filePath = $storagePath . '/' . $fileMeta['name'];

                        // Dosya uzantısını kontrol et
                        $fileExtension = strtolower(File::extension($filePath));

                        // Hızlı bir doğrulama yap
                        $settings = Cache::remember('allowed_file_extensions', 60 * 60 * 24, function () {
                            return Setting::whereIn(
                                'key',
                                ['allowed_song_formats', 'allowed_ringtone_formats', 'allowed_video_formats']
                            )->get(['key', 'value'])->keyBy('key')->toArray();
                        });

                        $allowedExtensions = [
                            'sound' => explode(',', $settings['allowed_song_formats']['value'] ?? 'wav,flac'),
                            'ringtone' => explode(',', $settings['allowed_ringtone_formats']['value'] ?? 'wav'),
                            'video' => explode(',', $settings['allowed_video_formats']['value'] ?? 'mp4,avi,flv'),
                        ];

                        $allowedExtension = [];

                        switch ($metaType) {
                            case SongTypeEnum::SOUND->value:
                                $allowedExtension = $allowedExtensions['sound'];
                                break;
                            case SongTypeEnum::VIDEO->value:
                                $allowedExtension = $allowedExtensions['video'];
                                break;
                            case SongTypeEnum::RINGTONE->value:
                                $allowedExtension = $allowedExtensions['ringtone'];
                                break;
                            default:
                                Log::error("Geçersiz medya tipi: " . $metaType);
                                $event->getResponse()->setHeaders(['error_message' => 'Geçersiz medya tipi']);
                                return;
                        }

                        if (!in_array($fileExtension, $allowedExtension)) {
                            Log::error("Dosya türü desteklenmiyor: " . $fileExtension, $allowedExtension);
                            $event->getResponse()->setHeaders(['error_message' => 'Gecersiz dosya tipi: ' . $fileExtension]);
                            return;
                        }

                        // Tenant ID'yi al ve metadata'ya ekle
                        $tenantId = function_exists('tenant') && tenant() ? tenant()->id : null;
                        if ($tenantId) {
                            $fileMeta['metadata']['tenant_id'] = $tenantId;
                        }

                        // Ağır işlemleri kuyrukta işle
                        ProcessSongUpload::dispatch($fileMeta, $filePath, $storagePath, Auth::user()?->id);

                        // İşlenmeye başlandığı bilgisini hemen döndür
                        $event->getResponse()->setHeaders([
                            'message' => 'Dosya yüklendi, işleniyor...',
                            'status' => 'processing',
                            'file_name' => $fileMeta['name']
                        ]);

                    } catch (\Throwable $e) {
                        Log::error("Dosya yükleme işlemi sırasında hata: " . $e->getMessage(), [
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        $event->getResponse()->setHeaders(['error_message' => 'Dosya yükleme işlemi sırasında hata: ' . $e->getMessage()]);
                    }
                }
            );

            // Patch olayını debug modunda ise loglamak için dinle
            $server->event()->addListener(
                'tus-server.upload.patch',
                function (\TusPhp\Events\TusEvent $event) {
                    $offset = $event->getRequest()->header('Upload-Offset');

                    // Sadece debug modunda loglama yap
                    if (config('app.debug')) {
                        Log::info("PATCH Request: Offset received - {$offset}");
                    }
                }
            );

            return $server;
        });
    }

    /**
     * Bu metod artık ProcessSongUpload iş sınıfında yönetiliyor.
     * Geriye dönük uyumluluk için burada tutuluyor.
     * @deprecated
     */
    protected function handleFileUploadComplete($event, $storagePath): void
    {
        // Bu metod artık ProcessSongUpload job sınıfına taşındı
        // Geriye dönük uyumluluk için korundu
        Log::warning('handleFileUploadComplete metodu kullanım dışıdır. ProcessSongUpload kullanın.');
    }

    private static function formatDuration($seconds): string
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }

    public function boot() {}
}
