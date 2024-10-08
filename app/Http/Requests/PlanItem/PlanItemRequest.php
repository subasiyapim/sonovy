<?php

namespace App\Http\Requests\PlanItem;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class PlanItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('plan_item_create') || Gate::allows('plan_item_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category' => ['required'],
            'type' => ['required'],
            'translations' => ['required', 'array'],
            'translations.*.name' => ['required', 'string'],
        ];
    }

    public function attributes()
    {
        return [
            'category' => __('panel.plan_item.form.category'),
            'type' => __('panel.plan_item.form.type'),
            'translations.*.name' => __('panel.plan_item.form.name'),
        ];
    }

}
