<?php

namespace App\Services;

use App\Facades\SMS\TwilioServices;
use App\Models\Setting;
use App\Models\UserCode;
use App\Models\User;


class UserVerifyService
{
    public static function generate(User $user): void
    {
        UserCode::where(['user_id' => $user->id])->delete();

        $six_digit_random_number = random_int(100000, 999999);

        UserCode::create([
            "user_id" => $user->id,
            "code" => $six_digit_random_number
        ]);

        SMSService::sendSMS($user->phone,  __('auth.phone_confirm_sms_message') .': ' . $six_digit_random_number);
    }

    public static function verify(int $id, string $code): mixed
    {
        $userCode = UserCode::where([
            "user_id" => $id,
            "code" => $code
        ])->first();

        if ($userCode) {
            $userCode->delete();
            return true;
        }
        return false;
    }


}
