<?php

namespace App\Http\Requests\Payment;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use App\Services\EarningService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
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
        $pending_payment = Payment::where('status', PaymentStatusEnum::PENDING->value)
            ->where('user_id', Auth::id())
            ->first();

        return match ($this->process_type) {
            1 => [
                'process_type' => 'required',
                'amount' => [
                    'required', 'numeric', 'max:'.$earning, 'not_in:0',
                    function ($attribute, $value, $fail) use ($pending_payment) {
                        if ($pending_payment) {
                            $fail('Bekleyen ödeme talebiniz olduğu için işlem yapamazsınız.');
                        }
                    }
                ],
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
            'amount' => __('control.payment.form.amount'),
            'account_id' => __('control.payment.form.account_id'),
        ];

    }
}
