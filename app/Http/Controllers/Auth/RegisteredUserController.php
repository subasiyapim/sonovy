<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\CountryServices;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\UserVerifyService;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {

        $countryCodes = CountryServices::getCountryPhoneCodes();

        return Inertia::render('Auth/Register', ['countryCodes' => $countryCodes]);
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




        Auth::login($user);

        return redirect(route('control.dashboard', absolute: false));
    }
}
