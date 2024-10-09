<?php

namespace App\Http\Requests\BankAccount;

use Illuminate\Foundation\Http\FormRequest;

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
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'iban' => 'required|string|max:255|unique:bank_accounts,iban',
            'swift' => 'nullable|string|max:255',
        ];
    }

    public function attributes()
    {
        return [
            'title' => __('control.bank_account.form.title'),
            'country_id' => __('panel.bank_account.form.country'),
            'name' => __('panel.bank_account.form.name'),
            'iban' => __('panel.bank_account.form.iban'),
            'swift' => __('panel.bank_account.form.swift'),
        ];

    }
}
