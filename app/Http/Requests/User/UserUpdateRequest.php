<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Gate;
use Laravel\Jetstream\Jetstream;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('user_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,'.$this->route('user')->id,
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

            'phone' => 'required|string|unique:users,phone,'.$this->route('user')->id,
            'password' => 'nullable|string|min:6',

            //'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',

        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('control.user.form.name.label'),
            'email' => __('control.user.form.email.label'),
            'country_id' => __('control.user.form.country_id.label'),
            'phone' => __('control.user.form.phone.label'),
            'password' => __('control.user.form.password.label'),
            'role_id' => __('control.user.form.role_id.label'),
        ];
    }
}
