<?php

namespace App\Http\Requests\HelpCenter;

use App\Services\LocaleService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class VideoStoreRequest extends FormRequest
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
            'video' => ['required', 'file', 'mimes:mov,mp4,ogg,qt'],
            'title' => 'required|array',
        ];

        foreach ($langs as $lang) {
            $arr['title.' . $lang] = ['required', 'string', 'max:255'];
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
            'title' => __('panel.faq.form.question'),
        ];
    }
}
