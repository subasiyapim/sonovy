<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => [__('validation.email', ['attribute' => 'email'])],
            ]);
        }

        $this->verificationCode($request->email);

        return redirect()->route('password.request.pin');
    }

    public function createPin()
    {
        $email = Session::get('forget_email');
        $masked_email = Session::get('forget_masked_email');
        $code = Session::get('forget_code');
        Session::put('forget_token', Str::random(60));

        if (!$email || !$masked_email || !$code) {
            return redirect()->route('password.request');
        }

        return Inertia::render('Auth/ForgotPasswordPin', compact('masked_email', 'email'));
    }

    public function storePin(Request $request)
    {
        $code = Session::get('forget_code');
        $email = Session::get('forget_email');

        if ($request->code != $code) {
            return redirect()->back()->withErrors(['code' => __('client.forgot_password_pin.code_incorrect')]);
        }

        $token = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$token) {
            return redirect()->route('password.request');
        }

        //token expired
        if (Carbon::parse($token->created_at)->addMinutes(1)->isPast()) {
            return redirect()->back()->withErrors(['code' => __('client.forgot_password_pin.code_expired')]);
        }

        return redirect()->route('password.reset',
            ['token' => $token->token, 'email' => $email]);
    }

    /**
     * @throws ValidationException
     */
    public function resetPin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $this->verificationCode($request->email);

        return redirect()->back();
    }


    /**
     * @throws ValidationException
     */
    private function verificationCode($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => [__('validation.email', ['attribute' => 'email'])],
            ]);
        }

        $code = random_int(100000, 999999);

        Session::put('forget_code', $code);
        Session::put('forget_email', $email);
        Session::put('forget_masked_email', emailMasking($email));

        DB::table('password_reset_tokens')
            ->updateOrInsert(
                ['email' => $user->email],
                ['token' => Session::get('forget_token'), 'created_at' => Carbon::now()]
            );

        $user->notify(new CustomResetPasswordNotification($code));

        return $code;

    }
}
