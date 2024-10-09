<?php

namespace App\Http\Requests\Feature;

use App\Services\LocaleService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class FeatureStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('feature_create');
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
            'item' => 'required|array',
            'period' => 'required|in:1,2,3',
            'limit' => 'required_if:item.type,number|integer',
            'is_active' => 'required|boolean',
            'amount' => 'required|numeric',
            'title' => 'required|array',
            'description' => 'required|array',
        ];
        foreach ($langs as $lang) {
            $arr['title.'.$lang] = ['required', 'string', 'max:150', 'min:1'];
            $arr['description.'.$lang] = ['required', 'string', 'max:500', 'min:1'];
        }

        return $arr;
        /*return [
            'period' => 'required|in:1,2,3',
            'limit' => 'required|integer',
            'is_active' => 'required|boolean',
            'amount' => 'required|numeric',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string',
            'translations.*.name' => 'required|string',
            'translations.*.description' => 'required|string'
        ];*/
    }

    public function attributes()
    {
        return [
            'period' => __('control.feature.form.period'),
            'limit' => __('control.feature.form.limit'),
            'is_active' => __('control.feature.form.is_active'),
            'amount' => __('control.feature.form.amount'),
            'translations.*.name' => __('control.feature.form.name'),
            'translations.*.description' => __('control.feature.form.description'),
        ];
    }
}
