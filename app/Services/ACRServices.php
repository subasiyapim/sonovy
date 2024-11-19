<?php

namespace App\Services;

use App\Models\Integration;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class ACRServices
{
    public static function identify($sample)
    {
        $http_method = "POST";
        $http_uri = "/v1/identify";
        $data_type = "audio";
        $signature_version = "1";
        $timestamp = time();

        // ACR servisi için gerekli entegrasyon bilgilerini çekiyoruz
        $integration = Integration::where('code', 'acr')->firstOrFail();

        $req_url = 'https://'.$integration->url.'/v1/identify';
        $access_key = $integration->key;
        $access_secret = $integration->secret;

        // İmza oluşturma
        $string_to_sign = $http_method."\n".
            $http_uri."\n".
            $access_key."\n".
            $data_type."\n".
            $signature_version."\n".
            $timestamp;
        $signature = base64_encode(hash_hmac("sha1", $string_to_sign, $access_secret, true));

        // Dosya varlık kontrolü
        if (!File::exists($sample)) {
            throw new \Exception('Sample file not found at location '.$sample);
        }

        // Dosya boyutunu alıyoruz
        $sample_bytes = File::size($sample);

        // HTTP isteği için alanlar
        $post_fields = [
            "sample_bytes" => $sample_bytes,
            "access_key" => $access_key,
            "data_type" => $data_type,
            "signature" => $signature,
            "signature_version" => $signature_version,
            "timestamp" => $timestamp
        ];

        // HTTP isteği ve dosya gönderimi
        return Http::attach('sample', File::get($sample), basename($sample))
            ->post($req_url, $post_fields);
    }
}