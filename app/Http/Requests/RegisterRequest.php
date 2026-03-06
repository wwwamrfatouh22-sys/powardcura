<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class RegisterRequest extends FormRequest
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
            'national_id' => 'required|digits:14|unique:patients,national_id',
            'full_name'   => 'required|string|min:10|max:255',
            'dob' => 'required|date_format:Y-m-d|before:today',
            'phone' => 'required|regex:/^01[0-9]{9}$/',
            'password' => 'required|min:8|string'

        ];
    }


}
