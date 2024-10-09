<?php

namespace App\Http\Requests\Copyright;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CopyrightStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        return Gate::allows('copyright_create');

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'broadcast_type' => ['required', 'numeric'],
            'platform' => ['required', 'numeric'],
            'type' => ['nullable', 'numeric'],
            'product_id' => ['required', 'numeric'],
            'songs' => ['nullable', 'array'],
            'songs.*.id' => ['required', 'exists:songs,id'],
            'songs.*.url' => ['required', 'string'],
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
            'broadcast_type' => __('control.copyright.fields.broadcast_type'),
            'platform' => __('control.copyright.fields.platform'),
            'type' => __('control.copyright.fields.type'),
            'product_id' => __('control.copyright.fields.product_id'),
            'songs' => __('control.copyright.fields.songs'),

        ];
    }
}
