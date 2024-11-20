<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\Setting;
use App\Services\ACRServices;
use App\Services\FFMpegServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class CheckACRJob
 *
 * This job is responsible for processing audio and video files related to a product.
 * It trims the media files based on predefined settings and identifies the content
 * using an external service (ACR).
 */
class CheckACRJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const CACHE_KEY_SETTINGS = 'settings';
    private const CACHE_DURATION = 3600;
    private const PATH_SONGS = '/storage/tenant_';
    private const PATH_SAMPLES = '/samples';
    private const DIRECTORY_PERMISSIONS = 0777;

    public Product $product;
    private $audioStartTime;
    private $audioEndTime;
    private $videoStartTime;
    private $videoEndTime;

    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->loadSettings();
    }

    private function loadSettings(): void
    {
        $settings = Cache::remember(self::CACHE_KEY_SETTINGS, self::CACHE_DURATION, function () {
            return Setting::whereIn('key', [
                'trim_video_start_time',
                'trim_video_end_time',
                'trim_audio_start_time',
                'trim_audio_end_time'
            ])->get()->pluck('value', 'key');
        });
        $this->audioStartTime = $settings['trim_audio_start_time'];
        $this->audioEndTime = $settings['trim_audio_end_time'];
        $this->videoStartTime = $settings['trim_video_start_time'];
        $this->videoEndTime = $settings['trim_video_end_time'];
    }

    public function handle(): void
    {
        Log::info('handle edildi');
        $songPath = public_path().self::PATH_SONGS.tenant('domain').'_songs';
        $this->createDirectoryIfNotExists($songPath.self::PATH_SAMPLES);

        foreach ($this->product->songs as $song) {
            $this->processSong($song, $songPath);
        }
    }

    private function createDirectoryIfNotExists(string $path): void
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, self::DIRECTORY_PERMISSIONS, true, true);
        }
    }

    private function processSong($song, $songPath): void
    {
        $filePath = $songPath.'/'.$song->path;
        $outputPath = $songPath.self::PATH_SAMPLES.'/'.$song->path;

        $trimmed = $this->trimMediaBasedOnType($song, $filePath, $outputPath);

        Log::info('trimmed: '.json_encode($trimmed));
        if ($trimmed['status'] !== false) {
            $this->identifyAndSaveACRResponse($song, $outputPath);
        }
    }

    private function trimMediaBasedOnType($song, $filePath, $outputPath)
    {
        if ($song->type == 1) {
            return FFMpegServices::trimMedia($filePath, $outputPath, $this->audioStartTime, $this->audioEndTime, true);
        } elseif ($song->type == 2) {
            return FFMpegServices::trimMedia($filePath, $outputPath, $this->videoStartTime, $this->videoEndTime, false);
        } else {
            return FFMpegServices::trimMedia($filePath, $outputPath, $this->audioStartTime, $this->audioEndTime, true);
        }
    }

    private function identifyAndSaveACRResponse($song, $outputPath): void
    {
        Log::info('ACR işlemine gönderilen dosya yolu: '.$outputPath);
        if (!File::exists($outputPath)) {
            Log::error('Dosya ACR işlemine başlamadan önce bulunamadı: '.$outputPath);
            throw new \Exception('Sample file not found at location '.$outputPath);
        }
        
        $acrResponse = ACRServices::identify($outputPath);
        if ($acrResponse->ok()) {
            $song->acr_response = $acrResponse->json();
            $song->save();
        }
    }
}
