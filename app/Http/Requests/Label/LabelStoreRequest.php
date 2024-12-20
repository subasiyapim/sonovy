<?php

namespace App\Http\Requests\Label;

use App\Models\System\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

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
            'country_id' => ['required', Rule::exists(Country::class, 'id')],
            'address' => ['nullable', 'string'],
            'image' => [
                'nullable', 'image',
                'dimensions:ratio=1, min_width=1000, min_height=1000', 'max:2048'
            ],
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
            'name' => __('control.label.fields.name'),
            'country_id' => __('control.label.fields.country_id'),
            'address' => __('control.label.fields.address'),
            'image' => __('control.label.fields.image'),
            'phone' => __('control.label.fields.phone'),
            'email' => __('control.label.fields.email'),
            'web' => __('control.label.fields.web'),
        ];
    }
}
