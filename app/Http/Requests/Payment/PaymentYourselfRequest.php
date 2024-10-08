<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class PaymentYourselfRequest extends FormRequest
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
        return [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'receipt' => 'nullable|file',
            'payment_date' => 'required|date_format:Y-m-d',
            'payment_time' => 'required|date_format:H:i',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'Kullanıcı',
            'amount' => 'Tutar',
            'receipt' => 'Makbuz',
            'payment_date' => 'Ödeme Tarihi',
            'payment_time' => 'Ödeme Saati',
        ];
    }
}
