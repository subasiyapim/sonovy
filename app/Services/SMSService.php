<?php

namespace App\Services;

use App\Facades\SMS\TwilioServices;
use App\Models\Setting;

class SMSService
{
    public static function sendSMS($number, $message) {

        //$will_netgsm_be_used = Setting::where('key', 'sms_message_netgsm')->first();
        $will_twillo_be_used = Setting::where('key', 'sms_message_twilio')->first();

        $phoneArr = explode(" ", $number);

        if ($will_twillo_be_used->value == 1 && $phoneArr[0] != '+90') {

            TwilioServices::send($phoneArr[0] . $phoneArr[1], $message);
        } else {

            NetGsmServices::sendSms($phoneArr[1], $message);
        }
    }
}
