<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainingRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'training_id' => ['required', 'integer', 'exists:training_programs,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'national_id' => ['required', 'string', 'max:30'],
            'age' => ['nullable', 'integer', 'min:16', 'max:120'],
            'gender' => ['nullable', 'in:male,female'],
            'university' => ['nullable', 'string', 'max:255'],
            'gpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:4096'],
        ];
    }
}
