<?php

namespace App\Http\Requests\Integration;

use App\Enums\IntegrationTypeEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class IntegrationUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('integration_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'type' => ['required', 'in:1,2,3,4,5'],
            'class_name' => ['required', 'string'],
            'url' => ['required', 'string'],
            'key' => ['nullable', 'string'],
            'secret' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
            'description' => ['required', 'string'],
        ];
    }
}
