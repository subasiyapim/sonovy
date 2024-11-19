<?php

namespace App\Providers;

use App\Models\Song;
use App\Services\FFMpegServices;
use App\Services\SongServices;
use Error;
use Exception;
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
            $storagePath = storage_path('app/public/tenant_'.tenant('domain').'_songs');

            if (!File::exists($storagePath)) {
                File::makeDirectory($storagePath, 0775, true, true);
            }

            $server->setApiPath('/control/tus');
            $server->setUploadDir($storagePath);

            $server->event()->addListener(
                'tus-server.upload.complete',
                function (\TusPhp\Events\TusEvent $event) use ($storagePath) {
                    $this->handleFileUploadComplete($event, $storagePath);
                }
            );

            return $server;
        });
    }

    protected function handleFileUploadComplete($event, $storagePath): void
    {
        //izin verilen dosya uzantıları
        $allowedExtensions = [
            'sound' => ['wav', 'flac', 'mp3'],
            'video' => ['mp4', 'avi', 'mkv', 'mov'],
        ];

        $fileMeta = $event->getFile()->details();
        Log::info("Upload tamamlandı: ".json_encode($fileMeta));

        $filePath = $storagePath.'/'.$fileMeta['name'];

        //Dosya uzantısı kontrolü
        $fileExtension = strtolower(File::extension($filePath));

        if (!in_array($fileExtension, $allowedExtensions['sound'])) {
            Log::error("Dosya türü desteklenmiyor: ".$fileExtension);
            $event->getResponse()->setHeaders(['error_message' => "Dosya türü desteklenmiyor: ".$fileExtension]);

            return;
        }

        $details = FFMpegServices::getMediaDetails(file: $filePath);

        if (!$details['status']) {
            Log::error("Dosya türü desteklenmiyor veya bir hata oluştu: ".json_encode($details));
            return;
        }

        $data = [
            "type" => $details['type'],
            "name" => $fileMeta['metadata']['orignalName'],
            "path" => $fileMeta['name'],
            "mime_type" => $fileMeta['metadata']['mime_type'],
            "size" => $fileMeta['metadata']['size'],
            "duration" => self::formatDuration($details['details']['duration']),
            "details" => $details,
            "created_by" => auth()->id()
        ];


        try {
            $file = Song::create($data);
            $file->products()->attach([$fileMeta['metadata']['product_id']]);
            Log::info("IDD: ".$fileMeta['metadata']['product_id']);

            $event->getResponse()->setHeaders(['upload_info' => $file->id]);
        } catch (Error $e) {
            Log::info("HATA: ".$e);
        }
    }


    private static function formatDuration($seconds): string
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }

    public function boot()
    {
    }
}
