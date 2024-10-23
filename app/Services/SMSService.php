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
        $will_twilio_be_used = Setting::where('key', 'sms_message_twilio')->first();
        $will_netgsm_be_used = Setting::where('key', 'sms_message_netgsm')->first();

        $user_code = UserCode::where('user_id', Auth::id())->where('type', 'phone')->count();
        $phoneArr = explode(" ", $number);

        if ($will_twilio_be_used->value == 1 && $phoneArr[0] != '+90') {
            // TwilioServices::send(count($phoneArr) >= 2 ? $phoneArr[1] : $number, $message);
        } elseif ($will_netgsm_be_used->value == 1 && $user_code == 0) {
            NetGsmServices::sendSms(count($phoneArr) >= 2 ? $phoneArr[1] : $number, $message);
        }
    }
}