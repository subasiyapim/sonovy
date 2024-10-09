<?php

namespace App\Http\Requests\Label;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class LabelStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('label_create');
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
            'country_id' => ['required', 'exists:countries,id'],
            'address' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg'],
            'phone' => ['nullable'],
            'email' => ['nullable', 'email'],
            'web' => ['nullable'],
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
            'country_id' => __('control.label.form.country_id'),
            'address' => __('control.label.form.address'),
            'image' => __('control.label.form.image'),
            'phone' => __('control.label.form.phone'),
            'email' => __('control.label.form.email'),
            'web' => __('control.label.form.web'),
        ];
    }
}
