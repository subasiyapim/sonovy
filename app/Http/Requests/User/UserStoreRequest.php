<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;
use Laravel\Jetstream\Jetstream;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('user_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'role_id' => ['required', 'exists:roles,id'],
            'commission_rate' => ['nullable', 'numeric', 'min:0.1', 'max:99.99'],
            'gender' => ['nullable', 'in:1,0'],
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'birth_date' => ['nullable', 'date'],

            'access_all_artist' => ['nullable', 'boolean'],
            'artists' => ['required_if:access_all_artist,0', 'array'],
            'artists.*.id' => ['required_if:access_all_artist,0', 'exists:artists,id'],

            'access_all_labels' => ['nullable', 'boolean'],
            'labels' => ['required_if:access_all_labels,0', 'array'],
            'labels.*.id' => ['required_if:access_all_labels,0', 'exists:labels,id'],

            'access_all_platforms' => ['nullable', 'boolean'],
            'platforms' => ['required_if:access_all_platforms,0', 'array'],
            'platforms.*.id' => ['required_if:access_all_platforms,0', 'exists:platforms,id'],

            'phone' => 'required|string|unique:users,phone',
            'password' => ['required', 'string',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()],

            //'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('panel.user.form.name.label'),
            'email' => __('panel.user.form.email.label'),
            'role_id' => __('panel.user.form.role_id.label'),
            'commission_rate' => __('panel.user.form.commission_rate.label'),
            'gender' => __('panel.user.form.gender.label'),
            'country_id' => __('panel.user.form.country_id.label'),
            'state_id' => __('panel.user.form.state_id.label'),
            'city_id' => __('panel.user.form.city_id.label'),
            'birth_date' => __('panel.user.form.birth_date.label'),
            'access_all_artist' => __('panel.user.form.access_all_artist.label'),
            'artists' => __('panel.user.form.artists.label'),
            'access_all_labels' => __('panel.user.form.access_all_labels.label'),
            'labels' => __('panel.user.form.labels.label'),
            'access_all_platforms' => __('panel.user.form.access_all_platforms.label'),
            'platforms' => __('panel.user.form.platforms.label'),
            'phone' => __('panel.user.form.phone.label'),
            'password' => __('panel.user.form.password.label'),
        ];
    }
}
