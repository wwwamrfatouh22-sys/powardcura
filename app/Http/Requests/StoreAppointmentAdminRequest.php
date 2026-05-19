<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentAdminRequest extends FormRequest
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
            'patient_id' => 'nullable|exists:patients,id',
            'doctor_id'  => 'required|exists:doctors,id',
            'department_id' => 'nullable|exists:departments,id',
            'first_name' => 'required_without:patient_id|nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'reason' => 'nullable|string|max:1000',
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'required|in:Pending,Confirmed,Completed,Canceled',
            'type' => 'nullable|in:hospital,private',
            'payment_method' => 'nullable|string|max:100',
            'payment_status' => 'nullable|in:pending,confirmed,paid,failed,canceled',
        ];
    }
}
