<?php

namespace App\Services;

use App\Models\Integration;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


class ACRServices
{
    public static function identify($sample)
    {

        $http_method = "POST";
        $http_uri = "/v1/identify";
        $data_type = "audio";
        $signature_version = "1";
        $timestamp = time();

        /*Cache::forget('settings');

        $settings = Cache::rememberForever('settings', function () {
            return Setting::pluck('value', 'key')->all();
        });

        config()->set('settings', $settings);

        $req_url = 'https://' . config('settings.acr_host') . '/v1/identify';
        $access_key = config('settings.acr_access_key');
        $access_secret = config('settings.acr_access_secret');*/

        $integrasion = Integration::where('id', 2)->first();

        $req_url = 'https://' . $integrasion->url . '/v1/identify';
        $access_key = $integrasion->key;
        $access_secret = $integrasion->secret;

        $string_to_sign = $http_method . "\n" .
            $http_uri . "\n" .
            $access_key . "\n" .
            $data_type . "\n" .
            $signature_version . "\n" .
            $timestamp;
        $signature = hash_hmac("sha1", $string_to_sign, $access_secret, true);

        $signature = base64_encode($signature);

        if (!Storage::exists($sample)) {
            throw new \Exception('Sample file not found at location ' . $sample);
        }
        
        $sample_bytes = Storage::size($sample);

        $post_fields = array(
            //"sample" => $sample_file,
            "sample_bytes" => $sample_bytes,
            "access_key" => $access_key,
            "data_type" => $data_type,
            "signature" => $signature,
            "signature_version" => $signature_version,
            "timestamp" => $timestamp
        );

        return Http::attach('sample', Storage::get($sample))->post($req_url, $post_fields);
    }

}
