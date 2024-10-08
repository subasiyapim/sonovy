<?php

namespace App\Jobs;

use App\Models\Artist;
use App\Models\Platform;
use App\Services\SpotifyServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SpotifyImageUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Artist $artist;

    /**
     * Create a new job instance.
     */
    public function __construct(Artist $artist)
    {
        $this->artist = $artist;
        $this->artist->load('platforms');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $spotifyArtistId = $this->getSpotifyArtistId();

        if (!$spotifyArtistId) {
            return; // Spotify ID bulunamadıysa işlemi sonlandır
        }

        $spotifyData = SpotifyServices::artist($spotifyArtistId);

        if (!isset($spotifyData['images'][0]['url'])) {
            return; // Resim URL'si bulunamadıysa işlemi sonlandır
        }

        $image = !empty($spotifyData['images']) ? $spotifyData['images'][0]['url'] : null;

        if (!$image) {
            return; // Resim URL'si bulunamadıysa işlemi sonlandır
        }

        $fileName = $this->generateFileName();
        $filePath = $this->downloadImage($image, $fileName);

        if (!$filePath) {
            return; // Resim indirme başarısız olduysa işlemi sonlandır
        }

        $this->uploadImageToMediaLibrary($filePath, $fileName);
    }

    /**
     * Spotify Artist ID'yi döndürür.
     */
    private function getSpotifyArtistId(): ?string
    {
        $platformUrl = DB::table('artist_platform')
            ->where('artist_id', $this->artist->id)
            ->where('platform_id', Platform::where('name', 'Spotify')->first()->id)
            ->value('url');

        return $platformUrl ? last(explode('/', $platformUrl)) : null;
    }

    /**
     * Dosya adını oluşturur.
     */
    private function generateFileName(): string
    {
        return Str::slug($this->artist->name.'-'.time()).'.jpg';
    }

    /**
     * Resmi indirir ve dosya yolunu döner.
     */
    private function downloadImage(string $imageUrl, string $fileName): ?string
    {
        $ch = curl_init($imageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

        $imageContent = curl_exec($ch);
        curl_close($ch);

        if ($imageContent === false) {
            return null;
        }

        $filePath = storage_path('app/public/'.$fileName);
        file_put_contents($filePath, $imageContent);

        return $filePath;
    }

    /**
     * Resmi medya kütüphanesine yükler.
     */
    private function uploadImageToMediaLibrary(string $filePath, string $fileName): void
    {
        $this->artist->addMedia($filePath)
            ->usingFileName($fileName)
            ->usingName($this->artist->name)
            ->toMediaCollection('artists', 'artists');
    }
}

