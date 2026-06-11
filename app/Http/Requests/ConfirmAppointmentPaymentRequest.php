<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmAppointmentPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'in:fawry_card,fawry_wallet,instapay,pay_at_hospital'],
            'booking_token' => ['nullable', 'string', 'uuid'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'Please choose a payment method before completing the booking.',
        ];
    }
}
