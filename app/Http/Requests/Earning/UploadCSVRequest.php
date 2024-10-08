<?php

namespace App\Http\Requests\Earning;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\File;

class UploadCSVRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('earning_report_create');
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
            'file' => [
                'required',
                'file',
                //'mimes:csv',
                'max:5120',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Kazanç Rapor adı',
            'file' => 'Rapor Excel Dosyası',
        ];
    }
}
