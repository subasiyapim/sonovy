<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class    ReportStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('report_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_date' => ['required_with:end_date', 'regex:/^(0?[1-9]|1[0-2])-\d{4}$/'],
            'end_date' => ['required_with:start_date', 'regex:/^(0?[1-9]|1[0-2])-\d{4}$/'],
            'report_type' => [
                'required', 'string',
                'in:all,artists,multiple_artists,products,multiple_products,songs,multiple_songs,platforms,multiple_platforms,countries,multiple_countries,labels,multiple_labels',
            ],
            'ids' => ['array'],
        ];
    }
}
