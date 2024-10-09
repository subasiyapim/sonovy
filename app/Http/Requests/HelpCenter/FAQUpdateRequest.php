<?php

namespace App\Http\Requests\HelpCenter;

use App\Services\LocaleService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class FAQUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('help_center_edit');
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
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg'],
            'title' => 'required|array',
            'subtitle' => 'required|array',
            'description' => 'required|array',
            'question' => 'required|array',
            'answer' => 'required|array'
        ];

        foreach ($langs as $lang) {
            $arr['title.'.$lang] = ['required', 'string', 'max:255'];
            $arr['subtitle.'.$lang] = ['required', 'string', 'max:255'];
            $arr['description.'.$lang] = ['required', 'string', 'max:5000'];
            $arr['question.'.$lang] = ['required', 'string', 'max:255'];
            $arr['answer.'.$lang] = ['required', 'string', 'max:1000'];
        }
        //dd($arr, $this->all());
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
            'title' => __('control.help_center.form.question'),
            'subtitle' => __('control.help_center.form.answer'),
            'description' => __('control.help_center.form.question'),
            'question' => __('control.help_center.form.question'),
            'answer' => __('control.help_center.form.answer')
        ];
    }
}
