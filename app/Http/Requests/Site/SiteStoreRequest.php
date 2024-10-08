<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;

class SiteStoreRequest extends FormRequest
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
            'logo' => ['nullable', 'image', 'max:2048'], // 2MB
            'domain' => ['required', 'string', 'unique:sites,domain,' . auth()->id()],
            'title' => ['required', 'string'],
            'dns' => ['required', 'string'],
            'email' => ['required', 'email'],
            'copyright_email' => ['required', 'email'],
            'phone' => ['nullable', 'string'],
            'has_contact_form' => ['nullable', 'boolean'],
            'language' => ['required', 'string'],
            'timezone_id' => ['required', 'integer', 'exists:timezones,id']
        ];
    }

    public function attributes()
    {
        return [
            'logo' => __('panel.site.form.logo'),
            'domain' => __('panel.site.form.domain'),
            'title' => __('panel.site.form.title'),
            'dns' => __('panel.site.form.dns'),
            'email' => __('panel.site.form.email'),
            'copyright_email' => __('panel.site.form.email'),
            'phone' => __('panel.site.form.phone'),
            'has_contact_form' => __('panel.site.form.has_contact_form'),
            'user_id' => __('panel.site.form.user_id'),
            'language_id' => __('panel.site.form.language_id'),
            'timezone_id' => __('panel.site.form.timezone_id'),
        ];
    }
}
