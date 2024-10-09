<?php

namespace App\Http\Requests\MailTemplate;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class MailTemplateStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('mail_template_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'unique:mail_templates,code'],
            'name' => ['required', 'string'],
            'translations.*.subject' => ['required_if:app.locale,'.app()->getLocale()],
            'translations.*.body' => ['required_if:app.locale,'.app()->getLocale()],
        ];
    }


    public function attributes(): array
    {
        return [
            'code' => __('control.mail_template.form.code'),
            'name' => __('control.mail_template.form.name'),
            'translations.*.subject' => __('control.mail_template.form.subject'),
            'translations.*.body' => __('control.mail_template.form.body'),
        ];
    }
}
