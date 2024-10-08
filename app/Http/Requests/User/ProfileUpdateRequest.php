<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->tab) {
            'profile' => [
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
                'company_name' => ['nullable', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'customer_number' => ['nullable'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
                'payment_mail' => ['nullable', 'email', 'max:255', 'unique:users,payment_mail,' . auth()->id()],
                'title' => ['nullable', 'numeric'],
                'gender' => ['nullable', 'numeric'],
                'country_id' => ['required', 'exists:countries,id'],
                'city_id' => ['nullable', 'exists:cities,id'],
                'address' => ['nullable', 'string', 'max:255'],
                'zip_code' => ['nullable', 'string', 'max:255'],
                'birth_date' => ['nullable', 'date'],
                'subscribe_newsletter' => ['nullable', 'boolean'],
                'phone' => ['nullable', 'string', 'max:255', 'unique:users,phone,' . auth()->id()],
                'theme' => ['nullable', 'string', 'in:light,dark'],
                'interface_language' => ['nullable'],
                'timezone_id' => ['nullable', 'exists:timezones,id'],
            ],
            'change-password' => [
                'password' => ['required', 'string', 'confirmed',
                    Password::min(8)
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                        ->uncompromised(),],
            ],
            'bill-information', => [
                //
            ],

            default => [],
        };
    }

    public function attributes()
    {
        return [
            'image' => __('panel.profile.form.image'),
            'company_name' => __('panel.profile.form.company_name'),
            'name' => __('panel.profile.form.name'),
            'customer_number' => __('panel.profile.form.customer_number'),
            'email' => __('panel.profile.form.email'),
            'payment_mail' => __('panel.profile.form.payment_mail'),
            'title' => __('panel.profile.form.title'),
            'gender' => __('panel.profile.form.gender'),
            'birth_place_id' => __('panel.profile.form.birth_place_id'),
            'birth_date' => __('panel.profile.form.birth_date'),
            'subscribe_newsletter' => __('panel.profile.form.subscribe_newsletter'),
            'phone' => __('panel.profile.form.phone'),
            'theme' => __('panel.profile.form.theme'),
            'interface_language' => __('panel.profile.form.interface_language'),
            'timezone_id' => __('panel.profile.form.timezone_id'),
            'password' => __('panel.profile.form.password'),
        ];
    }
}
