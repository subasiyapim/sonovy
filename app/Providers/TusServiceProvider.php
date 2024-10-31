<?php

namespace App\Providers;

use App\Enums\SongTypeEnum;
use App\Services\FFMpegServices;
use App\Services\SongServices;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use TusPhp\Tus\Server as TusServer;

class TusServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('tus-server', function ($app) {
            $server = new TusServer();

            // storage/songs dizini mevcut değilse oluştur ve izinleri ayarla
            if (!File::exists(storage_path('songs'))) {
                File::makeDirectory(storage_path('songs'), 0775, true, true);
            }

            $server->setApiPath('/tus');
            $server->setUploadDir(storage_path('songs'));  // Upload dizinini storage_path ile ayarla

            $server->event()->addListener('tus-server.upload.complete', function (\TusPhp\Events\TusEvent $event) {
                $fileMeta = $event->getFile()->details();
                Log::info("Upload tamamlandı: ".json_encode($fileMeta));

                // Dosya türüne göre işlemleri başlat
                $details = ['status' => false];
                $filePath = storage_path('songs/'.$fileMeta['name']);

                if ($fileMeta['metadata']['type'] == SongTypeEnum::SOUND->value) {
                    $details = FFMpegServices::getAudioDetails($filePath);
                } elseif ($fileMeta['metadata']['type'] == SongTypeEnum::VIDEO->value) {
                    $details = FFMpegServices::getVideoDetails($filePath);
                }

                $data = [
                    "type" => $fileMeta['metadata']['type'],
                    "name" => $fileMeta['metadata']['orignalName'],
                    "path" => 'songs/'.$fileMeta['name'],
                    "mime_type" => $fileMeta['metadata']['mime_type'],
                    "size" => $fileMeta['metadata']['size'],
                    "details" => $details,
                    "added_by" => auth()->id()
                ];

                SongServices::create($data);
            });

            return $server;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
