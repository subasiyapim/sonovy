<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\UserVerifyService;

class PhoneVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {

        $hasVerifiedEmail = $request->user()->is_verified;
        if (!$hasVerifiedEmail) {
            UserVerifyService::sendVerifySms($request->user());
        }
        return $hasVerifiedEmail
            ? redirect()->intended(route('control.dashboard', absolute: false))
            : Inertia::render('Auth/VerifyPhone', ['status' => session('status')]);
    }
}
