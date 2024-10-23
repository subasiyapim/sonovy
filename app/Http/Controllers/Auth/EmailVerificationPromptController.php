<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Random\RandomException;
use App\Models\User;
use App\Services\UserServices;
use App\Services\UserVerifyService;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        $hasVerifiedEmail = $request->user()->hasVerifiedEmail();

        if (!$hasVerifiedEmail) {
            UserVerifyService::sendVerifyEmail($request->user());
        }

        if ($hasVerifiedEmail) {
            return redirect()->intended(route('control.dashboard', absolute: false));
        }
        return Inertia::render('Auth/VerifyEmail', ['status' => session('status')]);
    }
}
