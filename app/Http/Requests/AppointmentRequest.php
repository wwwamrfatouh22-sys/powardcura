<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
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
                'first_name' => 'required|string|max:100',
               'last_name' => 'required|string|max:100',
              'email' => 'required|email|max:255',
             'phone' => 'nullable|digits:11',
             'reason' => 'nullable|string|max:1000',
             'time' => 'required',
            'type' => 'required|in:hospital,private',
            'payment_method' => 'required'
        ];
    }
}
