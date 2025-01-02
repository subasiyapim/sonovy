<?php

namespace App\Http\Requests\BankAccount;

use App\Models\System\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BankAccountStoreRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:255',
            'country_id' => ['required', Rule::exists(Country::class, 'id')],
            'name' => 'required|string|max:255',
            'iban' => 'required|string|max:255|unique:bank_accounts,iban',
            'swift' => 'nullable|string|max:255',
        ];
    }

    public function attributes()
    {
        return [
            'title' => __('control.bank_account.form.title'),
            'country_id' => __('control.bank_account.form.country'),
            'name' => __('control.bank_account.form.name'),
            'iban' => __('control.bank_account.form.iban'),
            'swift' => __('control.bank_account.form.swift'),
        ];

    }
}
