<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ComplaintRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'required|string|max:20',
            'subject'    => 'required|string|max:255',
            'type'       => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'details'    => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Name is required.',
            'email.required'      => 'Email is required.',
            'email.email'         => 'Please enter a valid email.',
            'phone.required'      => 'Phone number is required.',
            'subject.required'    => 'Complaint subject is required.',
            'type.required'       => 'Type of complaint is required.',
            'department.required' => 'Department is required.',
            'details.required'    => 'Details are required.',
        ];
    }
}
