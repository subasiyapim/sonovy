<?php

namespace App\Http\Requests\Feature;

use App\Services\LocaleService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class FeatureUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('feature_edit');
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
            'period' => 'required|in:1,2,3',
            'limit' => 'required|integer',
            'is_active' => 'required|boolean',
            'amount' => 'required|numeric',
            'title' => 'required|array',
            'description' => 'required|array',
        ];
        foreach ($langs as $lang) {
            $arr['title.' . $lang] = ['required', 'string', 'max:255'];
            $arr['description.' . $lang] = ['required', 'string', 'max:5000'];
        }
        return $arr;
    }

    public function attributes(): array
    {
        return [
            /*'period' => __('panel.feature.form.period'),
            'limit' => __('panel.feature.form.limit'),*/
            'is_active' => __('panel.feature.form.is_active'),
            'amount' => __('panel.feature.form.amount'),
            'title.*.name' => __('panel.feature.form.title'),
            'description.*.description' => __('panel.feature.form.description'),
        ];
    }
}
