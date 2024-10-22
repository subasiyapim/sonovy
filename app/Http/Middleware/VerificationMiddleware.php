<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (Setting::where(
        //     'key',
        //     'email_verification'
        // )->first()->value == 1 && auth()->user()->email_verified_at == null) {
        //     return redirect()->route('verification.notice');
        // } elseif (Setting::where(
        //     'key',
        //     'otp_verification'
        // )->first()->value == 1 && auth()->user()->is_verified == 0) {
        //     return redirect()->route('verification.phone');
        // }

        return $next($request);
    }
}
