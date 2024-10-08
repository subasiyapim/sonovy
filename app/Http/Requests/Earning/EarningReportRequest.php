<?php

namespace App\Http\Requests\Earning;

use Illuminate\Foundation\Http\FormRequest;

class EarningReportRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'isrc_code' => ['required', 'exists:songs,isrc'],
            'platform_id' => ['required', 'exists:platforms,id'],
            'country_id' => ['required', 'exists:countries,id'],
            'report_date' => ['required', 'date'],
            'sales_date' => ['required', 'date'],
            'sales_type' => ['required', 'string'],
            'net_revenue' => ['required', 'numeric', 'max:9999'],
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Name',
            'isrc_code' => 'ISRC Code',
            'platform_id' => 'Platform',
            'country_id' => 'Country',
            'report_date' => 'Rapor Tarihi',
            'sales_date' => 'Satış Tarihi',
            'sales_type' => 'Satış Türü',
            'net_revenue' => 'Kazanç',
        ];
    }

}
