<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\UserCode;
use Illuminate\Support\Facades\Auth;

//use App\Facades\SMS\TwilioServices;

class SMSService
{
    public static function sendSMS($number, $message)
    {
        $twilioSetting = Setting::where('key', 'sms_message_twilio')->first()->value;
        $netgsmSetting = Setting::where('key', 'sms_message_netgsm')->first()->value;

        $userCodeCount = UserCode::where('user_id', Auth::id())->where('type', 'phone')->count();
        $phoneArr = preg_split('/\s+/', $number);

        if ($twilioSetting == 1 && $phoneArr[0] != '+90') {
            //Todo Twilio services test edilecek.
            
            // TwilioServices::send(count($phoneArr) >= 2 ? $phoneArr[1] : $number, $message);
        } elseif ($netgsmSetting == 1 && $userCodeCount > 0) {
            NetGsmServices::sendSms(count($phoneArr) >= 2 ? $phoneArr[1] : $number, $message);
        }
    }

}