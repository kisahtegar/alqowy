<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateCourseRequest
 * 
 * Handles validation for updating an existing course. This request ensures
 * that the input data for updating a course meets the specified validation rules.
 * 
 * @package App\Http\Requests
 */
class UpdateCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Authorization is checked to ensure only users with the 'teacher' or 'owner' roles can make this request
        return $this->user()->hasAnyRole(['teacher', 'owner']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'path_trailer' => 'required|string|max:255',
            'about' => 'required|string',
            'category_id' => 'required|integer',
            'thumbnail' => 'sometimes|image|mimes:png,jpg,svg',
            'course_keypoints.*' => 'required|string|max:255',
        ];
    }
}
