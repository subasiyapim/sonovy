<?php

namespace App\Http\Requests\HelpCenter;

use App\Services\LocaleService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ArticleStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('help_center_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $langs = LocaleService::getLocalizationList();
        $arr = [
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg'],
            'title' => 'required|array',
            'subtitle' => 'required|array',
            'description' => 'required|array',
        ];

        foreach ($langs as $lang) {
            $arr['title.'.$lang] = ['required', 'string', 'max:255'];
            $arr['subtitle.'.$lang] = ['required', 'string', 'max:255'];
            $arr['description.'.$lang] = ['required', 'string', 'max:5000'];
        }

        return $arr;
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'image' => __('control.help_center.form.image'),
            'title' => __('panel.help_center.form.question'),
            'subtitle' => __('panel.help_center.form.answer'),
            'description' => __('panel.help_center.form.question'),
        ];
    }
}
