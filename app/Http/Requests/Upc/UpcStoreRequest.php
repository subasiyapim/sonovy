<?php

namespace App\Http\Requests\Upc;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpcStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('upc_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->input('type') == 'upc_file') {
            return [
                'file' => 'required|file|mimes:csv,xlsx,xls',
            ];
        }

        return [
            'upcs' => 'required|string|max:255|unique:upcs,upc',
        ];

    }

    public function attributes(): array
    {
        return [
            'file' => 'Excel File',
            'upc' => 'UPC',
        ];
    }
}
