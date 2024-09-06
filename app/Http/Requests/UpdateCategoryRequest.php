<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateCategoryRequest
 * 
 * Handles validation for updating an existing category. This request ensures
 * that the input data for updating a category meets the specified validation rules.
 * 
 * @package App\Http\Requests
 */
class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Authorization is checked to ensure only users with the 'owner' role can make this request
        return $this->user()->hasAnyRole(['owner']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['sometimes', 'image', 'mimes:png,jpg,jpeg'],
        ];
    }
}
