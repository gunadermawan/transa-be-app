<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'in:CASH,CREDIT_CARD,DEBIT_CARD,EWALLET,MEMBER_DEPOSIT'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'Payment method is required',
            'payment_method.in' => 'Invalid payment method',
            'amount_paid.required' => 'Amount paid is required',
            'amount_paid.numeric' => 'Amount paid must be a valid number',
            'amount_paid.min' => 'Amount paid must be at least 0',
        ];
    }
}
