<?php

namespace App\Services;

use App\Models\Integration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


class MusicBrainzServices
{
    public static function lookupISRC($isrc)
    {

        try {

            $integrasion = Integration::where('id', 7)->first();

            $result = Http::get($integrasion->url . '/isrc/' . $isrc . '?fmt=json');

            return ['status' => true, 'data' => $result->json()];
        }
        catch (\Exception $e){

            return ['status' => false, 'data' => $e->getMessage()];
        }
    }

}
