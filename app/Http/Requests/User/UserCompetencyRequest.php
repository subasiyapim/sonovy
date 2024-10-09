<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserCompetencyRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'acr_cloud_analysis' => ['required', 'in:1,2'],
            'auto_publish' => ['required', 'in:1,2'],
        ];
    }

    public function attributes(): array
    {
        return [
            'acr_cloud_analysis' => __('control.user.competency.fields.acr_cloud_analysis'),
            'auto_publish' => __('control.user.competency.fields.auto_publish')
        ];
    }
}
