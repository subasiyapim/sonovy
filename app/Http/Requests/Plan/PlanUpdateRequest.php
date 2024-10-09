<?php

namespace App\Http\Requests\Plan;

use App\Models\PlanItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class PlanUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('plan_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $planItems = PlanItem::active()->pluck('id')->toArray();

        return [
            'monthly_price' => ['required', 'numeric'],
            'annual_price' => ['required', 'numeric'],
            'sort_order' => ['nullable', 'numeric'],
            'is_active' => ['required', 'boolean'],
            'translations' => ['required', 'array'],
            'translations.*.name' => ['required', 'string', 'max:255'],
            'translations.*.description' => ['required', 'string', 'max:255'],
            'translations.*.note' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'in:'.implode(',', $planItems)],
            'items.*.value' => [
                'nullable', function ($attribute, $value, $fail) {
                    $index = $this->parseIndex($attribute);
                    $itemId = $this->input("items.$index.id");
                    $item = PlanItem::find($itemId);

                    if (!$item) {

                        return $fail(__('control.plan.form.validation.invalid_item', ['attribute' => $attribute]));
                    }


                    if ($item->type === 'number' && $value != 'Limitsiz' && !is_numeric($value)) {
                        return $fail(__('control.plan.form.validation.number', ['attribute' => $attribute]));
                    }

                    if ($item->type === 'boolean' && !in_array($value, ['0', '1'])) {
                        return $fail(__('control.plan.form.validation.boolean', ['attribute' => $attribute]));
                    }
                }
            ],
            'items.*.note' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'monthly_price' => __('control.plan.form.monthly_price'),
            'annual_price' => __('control.plan.form.annual_price'),
            'sort_order' => __('control.plan.form.sort_order'),
            'is_active' => __('control.plan.form.is_active'),
            'translations.*.locale' => __('control.plan.form.locale'),
            'translations.*.name' => __('control.plan.form.name'),
            'translations.*.description' => __('control.plan.form.description'),
            'translations.*.note' => __('control.plan.form.note'),
            'items.*.id' => __('control.plan.form.item_id'),
            'items.*.value' => __('control.plan.form.item_value'),
        ];
    }

    /**
     * Parse the index from the attribute.
     *
     * @param  string  $attribute
     * @return int
     */
    protected function parseIndex(string $attribute): int
    {
        if (preg_match('/items\.(\d+)\.value/', $attribute, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }

    public function attribute()
    {
        return [
            'monthly_price' => __('control.plan.form.monthly_price'),
            'annual_price' => __('control.plan.form.annual_price'),
            'sort_order' => __('control.plan.form.sort_order'),
            'is_active' => __('control.plan.form.is_active'),
            'translations.*.locale' => __('control.plan.form.locale'),
            'translations.*.name' => __('control.plan.form.name'),
            'translations.*.description' => __('control.plan.form.description'),
            'translations.*.note' => __('control.plan.form.note'),
            'items.*.id' => __('control.plan.form.item_id'),
            'items.*.value' => __('control.plan.form.item_value'),
        ];
    }
}
