<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PhoneVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        $isVerified  = $request->user()->is_phone_verified ?? false;
        return !$isVerified
            ? redirect()->intended(route('control.dashboard', absolute: false))
            : Inertia::render('Auth/VerifyPhone', ['status' => session('status')]);
    }
}
