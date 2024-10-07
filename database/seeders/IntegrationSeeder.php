<?php

namespace Database\Seeders;

use App\Models\Integration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IntegrationSeeder extends Seeder
{
    protected static array $integrations = [
        [
            'name' => 'NetGSM',
            'code' => 'netgsm',
            'type' => 1, //SMS
            'class_name' => 'NetGsmServices',
            'url' => 'https://api.netgsm.com.tr/sms/send/get',
            'key' => '8503046767',
            'secret' => '89DE578',
            'status' => 1,
            'description' => 'NetGSM is a Turkish company that provides SMS services.',
        ],
        [
            'name' => 'ACR',
            'code' => 'acr',
            'type' => 4, //Other
            'class_name' => 'ACRServices',
            'url' => 'identify-ap-southeast-1.acrcloud.com',
            'key' => '1556323d8724105f67c9399209429811',
            'secret' => 'nfIS8DiU9YGiqtmsMJTbdPohuRJ2lMhuPsavWIKz',
            'status' => 1,
            'description' => 'ACRCloud is a music recognition platform.',
        ],
        [
            'name' => 'Iyzico',
            'code' => 'iyzico',
            'type' => 3, //Payment
            'class_name' => 'IyzicoServices',
            'url' => 'https://sandbox-api.iyzipay.com',
            'key' => 'sandbox-BpOzIk53Rt7KjobLwHIwzJpgjIYzQCaE',
            'secret' => 'sandbox-8qEr4Q9ZHcBKlyvGaCMTKtmTefa3VA5g',
            'status' => 1,
            'description' => 'Iyzico is a Turkish payment gateway.',
        ],
        [
            'name' => 'Twilio',
            'code' => 'twilio',
            'type' => 1, //SMS
            'class_name' => 'TwilioServices',
            'url' => 'NUMBER',
            'key' => 'SID',
            'secret' => 'TOKEN',
            'status' => 1,
            'description' => 'Twilio is a cloud communications platform.',
        ],
        [
            'name' => 'Spotify',
            'code' => 'spotify',
            'type' => 5, //Platform Dsp (Digital Service Provider)
            'class_name' => 'SpotifyServices',
            'url' => 'https://accounts.spotify.com/api/token',
            'key' => '7ca605687fc64884b30c289184f75ccb',
            'secret' => '9ec729e3fcb548fcaefb5c193b943545',
            'status' => 1,
            'description' => 'Spotify is a digital music service and artists can provide information about their music on Spotify.
            Artist data; image, lspotify link etc. can be obtained from this gateway.',
        ],
        [
            'name' => 'iTunes',
            'code' => 'itunes',
            'type' => 4, //Other
            'class_name' => 'iTunesServices',
            'url' => 'https://itunes.apple.com/lookup',
            'key' => '',
            'secret' => '',
            'status' => 1,
            'description' => 'iTunes is a media player, media library, Internet radio broadcaster, and mobile device management application developed by Apple Inc.',
        ],
        [
            'name' => 'MusicBrainz',
            'code' => 'musicbrainz',
            'type' => 4, //Other
            'class_name' => 'MusicBrainzServices',
            'url' => 'https://musicbrainz.org/ws/2',
            'key' => '',
            'secret' => '',
            'status' => 1,
            'description' => 'MusicBrainz, freedb projesine benzer bir açık veri müzik veritabanı oluşturmayı amaçlayan bir projedir. Buradan ISRC kodu analizi yapılmaktadır.',
        ],
        [
            'name' => 'MusicMatch',
            'code' => 'musixmatch',
            'type' => 4, //Other
            'class_name' => 'MusicMatchServices',
            'url' => 'https://api.musixmatch.com/ws/1.1/',
            'key' => '',
            'secret' => 'e29832af0d880e4840b581bf64c5e718',
            'status' => 1,
            'description' => 'Musixmatch API, şarkı sözlerini ve şarkılara dair meta verileri almak için kullanılan bir servistir.',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Integration::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach (self::$integrations as $integration) {
            Integration::updateOrCreate(
                [
                    'name' => $integration['name'],
                    'code' => $integration['code'],
                ],
                $integration
            );
        }
    }
}
