<?php

namespace App\Http\Requests\Statistics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class MonthlyStreamsRequest extends FormRequest
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
            'period' => ['required', Rule::in(['annual', 'weekly', 'monthly'])],
        ];
    }

    public function attributes()
    {
        return [
            'period' => __('control.statistics.form.period'),
        ];
    }
}
