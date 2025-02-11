<?php

namespace App\Http\Requests\User;

use App\Models\System\City;
use App\Models\System\Country;
use App\Models\System\District;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

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
            'name' => ['required', 'string', 'max:255'],
            'country_id' => ['required', Rule::exists(Country::class, 'id')],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'language_id' => ['required', Rule::exists(Country::class, 'id')],
            'city_id' => ['required', Rule::exists(City::class, 'id')],
            'district_id' => ['required', Rule::exists(District::class, 'id')],
            'address' => ['required', 'string', 'max:255'],
            'commission_rate' => ['nullable', 'numeric', 'min:0.1', 'max:100'],
            'is_company' => ['required', 'boolean'],
            'company_info' => ['required_if:is_company,1', 'array'],
            'company_info.name' => ['required_if:is_company,1', 'string'],
            'company_info.tax_number' => ['required_if:is_company,1', 'integer'],
            'company_info.tax_office' => ['required_if:is_company,1', 'string'],
            'company_info.phone' => ['required_if:is_company,1', 'string'],
            'password' => [
                'required',
                'string',
                Password::min(8)
            ],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('control.user.fields.name'),
            'country_id' => __('control.user.fields.country_id'),
            'email' => __('control.user.fields.email'),
            'language_id' => __('control.user.fields.language_id'),
            'city_id' => __('control.user.fields.city_id'),
            'district_id' => __('control.user.fields.district_id'),
            'address' => __('control.user.fields.address'),
            'commission_rate' => __('control.user.fields.commission_rate'),
            'is_company' => __('control.user.fields.is_company'),
            'company_info.name' => __('control.user.fields.company_info.name'),
            'company_info.tax_number' => __('control.user.fields.company_info.tax_number'),
            'company_info.tax_office' => __('control.user.fields.company_info.tax_office'),
            'company_info.phone' => __('control.user.fields.company_info.phone'),
            'password' => __('control.user.fields.password.label'),
            'password_confirmation' => __('control.user.fields.password_confirmation'),
        ];
    }
}
