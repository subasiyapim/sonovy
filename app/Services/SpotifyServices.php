<?php

namespace App\Services;

use App\Models\Integration;
use App\Models\Platform;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class SpotifyServices
{

    protected static function platform()
    {
        return Cache::remember('platform', 60 * 60 * 24, function () {
            return Integration::where('code', 'spotify')->first();
        });

    }

    private static function getToken(): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        $platform = self::platform();

        return Http::asForm()
            ->post(
                'https://accounts.spotify.com/api/token',
                [
                    "grant_type" => "client_credentials",
                    "client_id" => $platform->key,
                    "client_secret" => $platform->secret
                ]
            );
    }

    private static function getBearerToken(): string
    {
        $tokenResponse = self::getToken();

        if ($tokenResponse->successful()) {
            $tokenData = $tokenResponse->json();

            if (isset($tokenData['access_token'])) {
                return $tokenData['access_token'];
            } else {
                throw new \Exception('Access token bulunamadı.');
            }
        } else {
            throw new \Exception('Token alımı başarısız: '.$tokenResponse->body());
        }
    }


    public static function search($q, $type, $market = 'TR')
    {
        $token = self::getToken();
        
        $result = Http::withToken($token->json()['access_token'])
            ->get('https://api.spotify.com/v1/search',
                ["q" => Str::lower($q), "type" => $type, 'market' => $market]);

        return json_decode($result->body());
    }

    public static function artist($id)
    {
        $token = self::getBearerToken();

        $result = Http::withToken($token)
            ->get("https://api.spotify.com/v1/artists/{$id}");

        return $result->json();
    }

}
