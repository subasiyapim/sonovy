<?php

namespace App\Http\Requests\Payment;

use App\Services\EarningService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class RequestPaymentRequest extends FormRequest
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
        $earning = number_format(EarningService::balance(), 2, '.', '');

        return match ($this->process_type) {
            1 => [
                'process_type' => 'required',
                'amount' => 'required|numeric|max:' . $earning . '|not_in:0',
                'account_id' => 'required|exists:bank_accounts,id',
            ],
            default => [
                'process_type' => 'required',
                'amount' => 'required|numeric|not_in:0',
            ],
        };
    }

    public function attributes()
    {
        return [
            'amount' => __('panel.payment.form.amount'),
            'account_id' => __('panel.payment.form.account_id'),
        ];

    }
}
