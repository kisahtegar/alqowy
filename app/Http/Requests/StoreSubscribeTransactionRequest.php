<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreSubscribeTransactionRequest
 * 
 * Handles validation for storing a subscription transaction. This request ensures
 * that the input data for creating a subscription transaction meets the validation rules.
 * 
 * @package App\Http\Requests
 */
class StoreSubscribeTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Authorization is checked to ensure only users with the 'student' role can make this request
        return $this->user()->hasAnyRole(['student']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'proof' => 'required|image|mimes:png,jpg',
        ];
    }
}
