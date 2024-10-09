<?php

namespace App\Http\Requests\Title;

use App\Services\LocaleService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class TitleStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return Gate::allows('title_create');
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
            'translations' => 'required|array',
            'translations.*.title' => 'required|string',
            'translations.*.locale' => 'nullable|string|in:'.implode(',', LocaleService::getLocalizationList()),
        ];
    }

    public function attributes(): array
    {
        return [
            'translations.*.title' => __('control.title.form.title'),
        ];
    }
}
