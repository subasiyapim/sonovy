<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Services\UserVerifyService;

class VerificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $settings = Setting::whereIn('key', ['email_verification', 'otp_verification'])
            ->pluck('value', 'key');

        $user = auth()->user();

        // Email doğrulama kontrolü
        if (
            isset($settings['email_verification']) &&
            $settings['email_verification'] == 1 &&
            $user->email_verified_at === null
        ) {
            return redirect()->route('verification.notice');
        }

        // OTP (SMS) doğrulama kontrolü
        if (
            isset($settings['otp_verification']) &&
            $settings['otp_verification'] == 1 &&
            $user->is_verified == 0
        ) {
            return redirect()->route('verification.phone');
        }

        return $next($request);
    }
}
