<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required|exists:password_reset_tokens,token',
            'email' => 'required|email',
            'password' => ['required', 'confirmed'],
        ]);


        $token = $request->get('token');
        $email = $request->get('email');
        $password = $request->get('password');

        $passwordResetToken = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)->first();

        if (!$passwordResetToken) {
            throw ValidationException::withMessages([
                'token' => [__('validation.exists', ['attribute' => 'token'])],
            ]);
        }

        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => [__('validation.email', ['attribute' => 'email'])],
            ]);
        }

        $user->password = Hash::make($password);
        $user->save();

        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->delete();

        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Password reset successfully');

    }
}
