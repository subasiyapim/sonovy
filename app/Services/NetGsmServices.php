<?php

namespace App\Services;

use App\Models\Integration;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class NetGsmServices
{
    public static function sendSms($phone, $message): void
    {
        $integrasion = Integration::where('id', 1)->first();
        $usercode = $integrasion->key;
        $password = $integrasion->secret;
        $msgheader = 'MUZIKDAGITM';
        $orjinator = 'MUZIKDAGITM';
        //dd($usercode, $password, $msgheader, $orjinator, $phone, $message);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.netgsm.com.tr/sms/send/get',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'usercode' => $usercode,
                'password' => $password,
                'gsmno' => $phone,
                'message' => $message,
                'msgheader' => $msgheader,
                'orjinator' => $orjinator
            ]
        ));

        curl_exec($curl);

        curl_close($curl);

    }

}
