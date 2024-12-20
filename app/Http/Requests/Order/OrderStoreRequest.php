<?php

namespace App\Http\Requests\Order;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric',
            'plan_id' => 'nullable|exists:plans,id',
            'features' => 'nullable|array',
            'features.*.id' => 'required|exists:features,id',
            'payment_type' => 'required|in:1,2',
            'card_holder' => 'required_if:payment_type,2',
            'expiration_date' => 'required_if:payment_type,2',
            'card_number' => 'required_if:payment_type,2|nullable|numeric|digits:16',
            'cvv' => 'required_if:payment_type,2',
            'payment_period' => 'nullable|in:monthly,annual,',
        ];
    }


    public function attributes(): array
    {
        return [
            'amount' => __('control.order.form.amount'),
            'plan_id' => __('control.order.form.plan_id'),
            'payment_type' => __('control.order.form.payment_type'),
            'card_holder' => __('control.order.form.card_holder'),
            'expiration_date' => __('control.order.form.expiration_date'),
            'card_number' => __('control.order.form.card_number'),
            'cvv' => __('control.order.form.cvv'),
            'payment_period' => __('control.order.form.payment_period'),
        ];
    }

}
