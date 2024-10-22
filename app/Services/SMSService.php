<?php

namespace App\Services;

use App\Facades\SMS\TwilioServices;
use App\Models\Setting;

class SMSService
{
    public static function sendSMS($number, $message)
    {
        $will_twilio_be_used = Setting::where('key', 'sms_message_twilio')->first();
        $will_netgsm_be_used = Setting::where('key', 'sms_message_netgsm')->first();

        $phoneArr = explode(" ", $number);

        if ($will_twilio_be_used->value == 1 && $phoneArr[0] != '+90') {
            TwilioServices::send($phoneArr[0].$phoneArr[1], $message);
        } elseif ($will_netgsm_be_used->value == 1) {
            NetGsmServices::sendSms(count($phoneArr) >= 2 ? $phoneArr[1] : $number, $message);
        }
    }
}
