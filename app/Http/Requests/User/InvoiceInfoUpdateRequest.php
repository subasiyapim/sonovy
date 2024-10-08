<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class InvoiceInfoUpdateRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invoice_type' => ['required', 'in:1,2'],
            'tax_office' => ['required', 'string', 'max:150'],
            'tax_number' => ['required', 'string', 'max:150'],
            'country_id' => ['required', 'exists:countries,id'],
            'zip_code' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'commercial_number' => ['required', 'string', 'max:150'],
        ];
    }

    public function attributes()
    {
        return [
            'invoice_type' => __('panel.invoice.form.type'),
            'tax_office' => __('panel.invoice.form.tax_office'),
            'tax_number' => __('panel.invoice.form.tax_number'),
            'registration_number' => __('panel.invoice.form.registration_number'),
            'country_id' => __('panel.invoice.form.country_id'),
            'zip_code' => __('panel.invoice.form.zipcode'),
            'address' => __('panel.invoice.form.address'),
            'commercial_number' => __('panel.invoice.form.commercial_number'),
        ];
    }
}
