<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
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

            'doctor_id' => 'required|exists:doctors,id',

            'start_date' => 'required|date|before_or_equal:today',

            'end_date' => 'required|date|after_or_equal:start_date',

            'reason' => 'required|string|max:1000',

        ];

    }

    public function messages(): array
    {
        return [

            'doctor_id.required' => 'Please select a doctor.',

            'doctor_id.exists' => 'Invalid doctor selection.',

            'start_date.before_or_equal' => 'Start date cannot be after today.',

            'end_date.after_or_equal' => 'End date must be after or equal to start date.',

        ];
    }
}
