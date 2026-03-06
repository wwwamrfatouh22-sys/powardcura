<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ElectronicSignatureRequest extends FormRequest
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
    public function rules()
    {
        return [
            'signature'   => 'required|string',
            'document_id' => 'required|integer|exists:documents,id',
        ];
    }

    public function messages()
    {
        return [
            'signature.required' => 'Signature is required.',
            'document_id.required' => 'Document ID is required.',
            'document_id.exists' => 'Document not found.',
        ];
    }
}
