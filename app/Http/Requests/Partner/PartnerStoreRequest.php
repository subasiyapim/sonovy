<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class PartnerStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('partner_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'logo' => ['required', 'image', 'mimes:jpeg,png,jpg'],
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('control.label.form.name'),
        ];
    }
}
