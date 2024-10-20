<?php

namespace App\Jobs;

use App\Models\Artist;
use App\Models\Platform;
use App\Services\SpotifyServices;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class SpotifyImageUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Artist $artist;
    public $tries = 3;

    //sleep 5 seconds between attempts
    public $backoff = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(Artist $artist)
    {
        $this->artist = $artist->load('platforms');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $spotifyArtistId = $this->getSpotifyArtistId();

            if (!$spotifyArtistId) {
                Log::warning("Spotify Artist ID bulunamadı: Artist ID {$this->artist->id}");
                return; // Spotify ID bulunamadıysa işlemi sonlandır
            }

            $spotifyData = SpotifyServices::artist($spotifyArtistId);

            if (empty($spotifyData['images'][0]['url'])) {
                Log::warning("Spotify resim URL'si bulunamadı: Spotify Artist ID {$spotifyArtistId}");
                return;
            }

            $imageUrl = $spotifyData['images'][0]['url'];

            $fileName = $this->generateFileName();
            $filePath = $this->downloadImage($imageUrl, $fileName);

            if (!$filePath) {
                Log::error("Resim indirme başarısız: URL {$imageUrl}");
                return;
            }

            $this->uploadImageToMediaLibrary($filePath, $fileName);

            if (file_exists($filePath)) {
                unlink($filePath);
            }

        } catch (Exception $e) {
            Log::error("SpotifyImageUploadJob hatası: ".$e->getMessage());
            $this->fail($e);
        }
    }

    /**
     * Spotify Artist ID'yi döndürür.
     */
    private function getSpotifyArtistId(): ?string
    {
        $spotifyPlatform = Platform::where('name', 'Spotify')->first();

        if (!$spotifyPlatform) {
            Log::error("Spotify platformu bulunamadı.");
            return null;
        }

        $platformUrl = DB::table('artist_platform')
            ->where('artist_id', $this->artist->id)
            ->where('platform_id', $spotifyPlatform->id)
            ->value('url');

        if (!$platformUrl) {
            Log::warning("Artist için Spotify URL bulunamadı: Artist ID {$this->artist->id}");
            return null;
        }

        return Arr::last(explode('/', rtrim($platformUrl, '/')));
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
        try {
            $response = Http::withOptions([
                'verify' => false, // SSL doğrulamasını kapatmak gerekli değilse kaldırabilirsiniz
            ])->get($imageUrl);

            if (!$response->successful()) {
                Log::error("Resim indirilemedi: HTTP Status {$response->status()} URL {$imageUrl}");
                return null;
            }

            $imageContent = $response->body();

            $tenantId = tenant('id');
            $directory = "tenant_{$tenantId}/temporary/images";

            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            $filePath = "{$directory}/{$fileName}";
            Storage::disk('public')->put($filePath, $imageContent);

            return storage_path("app/public/{$filePath}");
        } catch (Exception $e) {
            Log::error("Resim indirirken hata oluştu: ".$e->getMessage());
            return null;
        }
    }

    /**
     * Resmi medya kütüphanesine yükler.
     */
    private function uploadImageToMediaLibrary(string $filePath, string $fileName): void
    {
        try {
            $tenantId = tenant('id');
            $this->artist->addMedia($filePath)
                ->usingFileName($fileName)
                ->usingName($this->artist->name)
                ->toMediaCollection('artists', 'tenant_'.$tenantId);
        } catch (Exception $e) {
            Log::error("Medya kütüphanesine yükleme hatası: ".$e->getMessage());
            throw $e;
        }
    }
}