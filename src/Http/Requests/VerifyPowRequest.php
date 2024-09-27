<?php

namespace Mohamedahmed01\LaravelPow\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Adjust this if you need specific authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'challenge' => 'required|string',
            'proof' => 'required|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'challenge.required' => 'A challenge is required.',
            'challenge.string' => 'The challenge must be a string.',
            'proof.required' => 'A proof is required.',
            'proof.string' => 'The proof must be a string.',
        ];
    }
}