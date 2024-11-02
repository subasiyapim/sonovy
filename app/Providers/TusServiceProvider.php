<?php

namespace App\Providers;

use App\Enums\SongTypeEnum;
use App\Services\FFMpegServices;
use App\Services\SongServices;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use TusPhp\Tus\Server as TusServer;

class TusServiceProvider extends ServiceProvider
{

    public $song;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('tus-server', function ($app) {
            $server = new TusServer();
            $storagePath = storage_path('app/public/tenant_'.tenant('id').'/songs');

            // storage/songs dizini mevcut değilse oluştur ve izinleri ayarla
            if (!File::exists($storagePath)) {
                File::makeDirectory($storagePath, 0775, true, true);
            }

            $server->setApiPath('/control/tus');
            $server->setUploadDir($storagePath);

            $server->event()->addListener(
                'tus-server.upload.complete',
                function (\TusPhp\Events\TusEvent $event) use ($storagePath) {
                    $fileMeta = $event->getFile()->details();
                    Log::info("Upload tamamlandı: ".json_encode($fileMeta));

                    // Dosya türüne göre işlemleri başlat
                    $details = ['status' => false];
                    $filePath = $storagePath.'/'.$fileMeta['name'];

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
                        "created_by" => auth()->id()
                    ];

                    //TODO Product ID BURADN GELİYOR
                    // $fileMeta['metadata']['product_id'];
                    $file = SongServices::create($data);
                    $file->products()->attach([$fileMeta['metadata']['product_id']]);

                    $event->getResponse()->setHeaders(['Upload-Info' => $file->id]);

                    Cache::put('last_uploaded_song', $file, now()->addMinutes(10));
                    // $createdSong = SongServices::create($data);
                }
            );

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
