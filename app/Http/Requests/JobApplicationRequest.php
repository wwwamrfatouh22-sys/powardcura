<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobApplicationRequest extends FormRequest
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
            'job_id'      => 'required|exists:jobs_training,id',
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'required|string|max:20',
            'national_id' => 'required|string|max:20',
            'cv'          => 'required|file|mimes:pdf,doc,docx|max:2048',
        ];
    }
    public function messages(): array
    {
        return [
            'cv.mimes' => 'CV must be a PDF or Word file.',
            'cv.max'   => 'CV size must not exceed 2MB.',
        ];
    }
}
