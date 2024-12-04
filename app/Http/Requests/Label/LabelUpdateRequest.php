<?php

namespace App\Http\Requests\Label;

use App\Models\System\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class LabelUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('label_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //dd($this->all()['image']);
        return [
            'name' => ['required', 'string'],
            'country_id' => ['required', Rule::exists(Country::class, 'id')],
            'address' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'dimensions:ratio=1, min_width=1000, min_height=1000', 'max:2048'],
            'phone' => ['nullable'],
            'web' => ['nullable'],
            'email' => ['nullable', 'email'],
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
            'web' => __('control.label.fields.web'),
            'email' => __('control.label.fields.email'),
        ];
    }
}
