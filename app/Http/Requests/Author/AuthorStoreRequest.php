<?php

namespace App\Http\Requests\Author;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class AuthorStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('author_create');
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
            'birth_date' => ['required', 'date'],
            'is_registered_pro' => ['required', 'boolean'],
            'pro_id' => ['required_if:is_registered_pro,true'],
            'cae_ipi_number' => ['required_if:is_registered_pro,true'],
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
            'name' => __('panel.author.form.name'),
            'birth_date' => __('panel.author.form.birth_date'),
            'is_registered_pro' => __('panel.author.form.is_registered_pro'),
            'pro_id' => __('panel.author.form.pro_id'),
            'cae_ipi_number' => __('panel.author.form.cae_ipi_number'),
        ];
    }
}
