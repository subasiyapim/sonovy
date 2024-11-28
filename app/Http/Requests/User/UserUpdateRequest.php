<?php

namespace App\Http\Requests\User;

use App\Models\System\City;
use App\Models\System\Country;
use App\Models\System\District;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
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
            'name' => ['required', 'string', 'max:255'],
            'country_id' => ['required', Rule::exists(Country::class, 'id')],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$this->user->id],
            'language_id' => ['required', Rule::exists(Country::class, 'id')],
            'city_id' => ['required', Rule::exists(City::class, 'id')],
            'district_id' => ['required', Rule::exists(District::class, 'id')],
            'address' => ['required', 'string', 'max:255'],
            'commission_rate' => ['nullable', 'numeric', 'min:0.1', 'max:99.99'],
            'is_company' => ['required', 'boolean'],
            'company_info' => ['required_if:is_company,1', 'array'],
            'company_info.name' => ['required_if:is_company,1', 'string'],
            'company_info.tax_number' => ['required_if:is_company,1', 'integer'],
            'company_info.tax_office' => ['required_if:is_company,1', 'string'],
            'company_info.phone' => ['required_if:is_company,1', 'string'],
            'password' => [
                'required', 'string',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('control.user.form.name'),
            'country_id' => __('control.user.form.country_id'),
            'email' => __('control.user.form.email'),
            'language_id' => __('control.user.form.language_id'),
            'city_id' => __('control.user.form.city_id'),
            'district_id' => __('control.user.form.district_id'),
            'address' => __('control.user.form.address'),
            'commission_rate' => __('control.user.form.commission_rate'),
            'is_company' => __('control.user.form.is_company'),
            'company_info.name' => __('control.user.form.company_info.name'),
            'company_info.tax_number' => __('control.user.form.company_info.tax_number'),
            'company_info.tax_office' => __('control.user.form.company_info.tax_office'),
            'company_info.phone' => __('control.user.form.company_info.phone'),
            'password' => __('control.user.form.password.label'),
            'password_confirmation' => __('control.user.form.password_confirmation'),
        ];
    }
}
