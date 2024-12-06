<?php

namespace App\Providers;

use App\Enums\SongTypeEnum;
use App\Models\Setting;
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

        $settings = Cache::remember('settings', 60 * 60 * 24, function () {
            return Setting::whereIn('key',
                ['allowed_song_formats', 'allowed_ringtone_formats', 'allowed_video_formats'])->get(
                ['code', 'value']
            )->toArray();
        });

        $allowedExtensions = [
            'sound' => explode(',', $settings[0]['value'] ?? 'wav,flac'),
            'ringtone' => explode(',', $settings[1]['value'] ?? 'wav ,mp3'),
            'video' => explode(',', $settings[2]['value'] ?? 'mp4,avi,flv'),
        ];

        $fileMeta = $event->getFile()->details();
        $metaType = $fileMeta['metadata']['type'];
        $filePath = $storagePath.'/'.$fileMeta['name'];

        //Dosya uzantısı kontrolü
        $fileExtension = strtolower(File::extension($filePath));

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
        }
        if (!in_array($fileExtension, $allowedExtension)) {
            Log::error("Dosya türü desteklenmiyor: ".$fileExtension, $allowedExtension);
            $event->getResponse()->setHeaders(['error_message' => 'Gecersiz dosya tipi: '.$fileExtension]);

            return;
        }

        $details = FFMpegServices::getMediaDetails(file: $filePath);

        if (!$details['status']) {
            Log::error("Bir hata oluştu: ".json_encode($details));
            return;
        }

        $data = [
            "type" => $fileMeta['metadata']['type'],
            "name" => $fileMeta['metadata']['originalName'],
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
