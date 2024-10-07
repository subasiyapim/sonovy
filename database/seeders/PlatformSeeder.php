<?php

namespace Database\Seeders;

use App\Enums\PlatformTypeEnum;
use App\Models\Platform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "type" => PlatformTypeEnum::DOWNLOADABLE->value,
                "name" => 'Amazon',
                "visible_name" => 'Amazon Prime Video',
                "code" => 'amazon',
                "url" => 'https://www.amazon.com',
                "status" => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::DOWNLOADABLE->value,
                'name' => 'Spotify',
                'visible_name' => 'Spotify',
                'code' => 'spotify',
                'url' => 'https://www.spotify.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::DOWNLOADABLE->value,
                'name' => 'Spotify Permium',
                'visible_name' => 'Spotify Premium',
                'code' => 'spotify_premium',
                'url' => 'https://www.spotify.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::DOWNLOADABLE->value,
                'name' => 'Netflix',
                'visible_name' => 'Netflix Premium',
                'code' => 'netflix',
                'url' => 'https://www.netflix.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::DOWNLOADABLE->value,
                'name' => 'Apple',
                'visible_name' => 'Apple Music',
                'code' => 'apple',
                'url' => 'https://www.apple.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::DOWNLOADABLE->value,
                'name' => 'Google',
                'visible_name' => 'Google Play Music',
                'code' => 'google',
                'url' => 'https://www.google.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::STREAMING->value,
                'name' => 'Youtube',
                'visible_name' => 'Youtube Premium',
                'code' => 'youtube',
                'url' => 'https://www.youtube.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::STREAMING->value,
                'name' => 'Tiktok',
                'visible_name' => 'Tiktok Premium',
                'code' => 'tiktok',
                'url' => 'https://www.tiktok.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::SOCIAL_MEDIA->value,
                'name' => 'Facebook',
                'visible_name' => 'Facebook Premium',
                'code' => 'facebook',
                'url' => 'https://www.facebook.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::SOCIAL_MEDIA->value,
                'name' => 'Instagram',
                'visible_name' => 'Instagram Premium',
                'code' => 'instagram',
                'url' => 'https://www.instagram.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::SOCIAL_MEDIA->value,
                'name' => 'Twitter',
                'visible_name' => 'Twitter Premium',
                'code' => 'twitter',
                'url' => 'https://www.twitter.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::SOCIAL_MEDIA->value,
                'name' => 'LinkedIn',
                'visible_name' => 'LinkedIn Premium',
                'code' => 'linkedin',
                'url' => 'https://www.linkedin.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::SOCIAL_MEDIA->value,
                'name' => 'Snapchat',
                'visible_name' => 'Snapchat Premium',
                'code' => 'snapchat',
                'url' => 'https://www.snapchat.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::SOCIAL_MEDIA->value,
                'name' => 'Pinterest',
                'visible_name' => 'Pinterest Premium',
                'code' => 'pinterest',
                'url' => 'https://www.pinterest.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::SOCIAL_MEDIA->value,
                'name' => 'Reddit',
                'visible_name' => 'Reddit Premium',
                'code' => 'reddit',
                'url' => 'https://www.reddit.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::SOCIAL_MEDIA->value,
                'name' => 'Tumblr',
                'visible_name' => 'Tumblr Premium',
                'code' => 'tumblr',
                'url' => 'https://www.tumblr.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::SOCIAL_MEDIA->value,
                'name' => 'Viber',
                'visible_name' => 'Viber Premium',
                'code' => 'viber',
                'url' => 'https://www.viber.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::SOCIAL_MEDIA->value,
                'name' => 'WeChat',
                'visible_name' => 'WeChat Premium',
                'code' => 'wechat',
                'url' => 'https://www.wechat.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
            [
                'type' => PlatformTypeEnum::SOCIAL_MEDIA->value,
                'name' => 'Line',
                'visible_name' => 'Line Premium',
                'code' => 'line',
                'url' => 'https://www.line.com',
                'status' => 1,
                "authenticators" => [json_decode('{"key": "password", "value": "34343434"}'), json_decode('{"key": "username", "value": "3847837834"}')]
            ],
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Platform::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($data as $item) {
            Platform::updateOrCreate(
                [
                    'name' => $item['name'],
                ],
                $item);
        }
    }
}
