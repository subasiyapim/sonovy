<?php

namespace App\Http\Requests\MailTemplate;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class MailTemplateUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('mail_template_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'translations.*.subject' => ['required_if:app.locale,'.app()->getLocale()],
            'translations.*.body' => ['required_if:app.locale,'.app()->getLocale()],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => __('panel.mail_template.form.code'),
            'name' => __('panel.mail_template.form.name'),
            'translations.*.subject' => __('panel.mail_template.form.subject'),
            'translations.*.body' => __('panel.mail_template.form.body'),
        ];
    }
}
