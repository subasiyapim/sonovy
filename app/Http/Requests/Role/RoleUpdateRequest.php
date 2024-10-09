<?php

namespace App\Http\Requests\Role;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Gate;

class RoleUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('role_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|unique:roles,code,'.$this->route('role')->id,
            'translations.*.name' => 'required|string',
            'permissions' => 'required|array',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('control.role.form.name'),
            'permissions' => __('control.role.form.permissions')
        ];
    }

}
