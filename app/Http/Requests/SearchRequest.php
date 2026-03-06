<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'national_id' => ['required', 'digits:14'],
        ];
    }

    public function messages(): array
    {
        return [
            'national_id.required' => 'Please enter your National ID.',
            'national_id.digits'   => 'National ID must be 14 digits.',
        ];
    }
}
