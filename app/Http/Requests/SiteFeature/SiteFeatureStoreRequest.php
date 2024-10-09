<?php

namespace App\Http\Requests\SiteFeature;

use App\Services\LocaleService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class SiteFeatureStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('site_feature_create');
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
            'icon' => ['required', 'string'],
            'title' => 'required|array',
            'text' => 'required|array'
        ];

        foreach ($langs as $lang) {
            $arr['title.'.$lang] = ['required', 'string', 'max:255'];
            $arr['text.'.$lang] = ['required', 'string', 'max:1000'];
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
            'icon' => __('control.site_features.form.icon'),
            'title' => __('control.site_features.form.title'),
            'text' => __('control.site_features.form.text'),
        ];
    }
}
