<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\UserCode;
use App\Models\User;
use App\Notifications\CustomVerifyEmailNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\SMSService;

// SMSService sınıfınızın doğru namespace'ini kullanın
use Illuminate\Support\Facades\DB;
use Random\RandomException;

class UserVerifyService
{
    /**
     * Generate and send verification codes for email and phone.
     *
     * @param  User  $user
     * @return void
     */
    public static function sendVerifyEmail(User $user)
    {

        $settings = Cache::remember('verification_settings', 60, function () {
            return Setting::whereIn('key', ['email_verification', 'otp_verification'])
                ->pluck('value', 'key');
        });
        if (self::isEmailVerificationEnabled($settings) && !$user->hasVerifiedEmail()) {
            try {
                $verificationCodeEmail = self::makeCode($user, 'email');
                $user->notify(new CustomVerifyEmailNotification($verificationCodeEmail));
                Log::info("Email verification code sent to user ID {$user->id}");
            } catch (\Exception $e) {
                Log::error("Email gönderimi başarısız: {$e->getMessage()} for user ID {$user->id}");
            }
        }
    }

    public static function sendVerifySms(User $user)
    {

        $settings = Cache::remember('verification_settings', 60, function () {
            return Setting::whereIn('key', ['email_verification', 'otp_verification'])
                ->pluck('value', 'key');
        });

        if (self::isOtpVerificationEnabled($settings) && !$user->is_verified) {
            try {
                $verificationCodePhone = self::makeCode($user, 'phone');

                $smsMessage = __('auth.phone_confirm_sms_message').': '.$verificationCodePhone;
                SMSService::sendSMS($user->phone, $smsMessage);
                Log::info("SMS verification code sent to user ID {$user->id}");
            } catch (\Exception $e) {
                Log::error("SMS gönderimi başarısız: {$e->getMessage()} for user ID {$user->id}");
            }
        }
    }


    /**
     * Create or update a verification code for the user.
     *
     * @param  User  $user
     * @param  string  $type  ('email' or 'phone')
     * @return string
     * @throws RandomException
     */
    protected static function makeCode(User $user, string $type)
    {
        $code = random_int(100000, 999999);

        $verification_code_expire = Setting::where('key', 'verification_code_expire')->first()->value ?? 1;

        DB::transaction(function () use ($user, $type, $code, $verification_code_expire) {
            UserCode::updateOrCreate(
                ['user_id' => $user->id, 'type' => $type],
                ['code' => $code, 'expire_at' => Carbon::now()->addMinutes(intval($verification_code_expire))]
            );
        });

        return $code;
    }

    /**
     * Check if email verification is enabled in settings.
     *
     * @param  \Illuminate\Support\Collection  $settings
     * @return bool
     */
    protected static function isEmailVerificationEnabled($settings): bool
    {
        return isset($settings['email_verification']) && $settings['email_verification'] == 1;
    }

    /**
     * Check if OTP (SMS) verification is enabled in settings.
     *
     * @param  \Illuminate\Support\Collection  $settings
     * @return bool
     */
    protected static function isOtpVerificationEnabled($settings): bool
    {
        return isset($settings['otp_verification']) && $settings['otp_verification'] == 1;
    }
}
