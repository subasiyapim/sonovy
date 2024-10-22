<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Setting;
use App\Models\User;
use App\Services\SMSService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        $verified_method = Setting::whereIn('key', ['sms_message_twilio', 'sms_message_netgsm'])->get();
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated()['name'],
            'surname' => $request->validated()['surname'],
            'email' => $request->validated()['email'],
            'password' => Hash::make($request->validated()['password']),
            'phone' => $request->validated()['phone'],
        ]);


        event(new Registered($user));

        SMSService::sendSMS($user->phone, __('auth.phone_confirm_sms_message'));

        Auth::login($user);

        return redirect(route('control.dashboard', absolute: false));
    }
}
