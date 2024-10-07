<?php

namespace App\Services;

use App\Models\Integration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


class iTunesServices
{

    public static function search($term)
    {
        $response = Http::withOptions([
            'curl' => [
                CURLOPT_VERBOSE => true,
                CURLOPT_STDERR => fopen('php://temp', 'w+'), // Hata ayıklama bilgilerini yazmak için
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1',
            ]
        ])->get('https://itunes.apple.com/search', [
            "term" => $term,
            "entity" => "musicArtist"
        ]);

        if ($response->status() === 200) {
            $data = $response->json(); // Yanıtı dizi olarak al
            if ($data['resultCount'] > 0) {
                return $data;
            } else {
                return "No results found.";
            }
        } else {
            // cURL verbose çıktısını okuyup hata ayıklama için kullanın
            $verboseHandle = fopen('php://temp', 'r+');
            rewind($verboseHandle);
            $verboseLog = stream_get_contents($verboseHandle);
            fclose($verboseHandle);

            dd($response->status(), $response->body(), $verboseLog);
        }
    }


    public static function lookup($q, $type)
    {

        try {

            $integrasion = Integration::where('id', 6)->first();
            $result = Http::get($integrasion->url . '?' . $type . '=' . $q);

            return ['status' => true, 'data' => $result->json()];
        } catch (\Exception $e) {

            return ['status' => false, 'data' => $e->getMessage()];
        }
    }

}
