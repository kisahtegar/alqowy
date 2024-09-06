<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreCourseVideoRequest
 * 
 * Handles validation for storing a new course video. This request ensures that
 * the input data for adding a video to a course meets specific validation rules.
 * 
 * @package App\Http\Requests
 */
class StoreCourseVideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Authorization is checked to ensure only users with 'teacher' or 'owner' roles can make this request
        return $this->user()->hasAnyRole(['teacher','owner']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:225',
            'path_video' => 'required|string|max:225',
        ];
    }
}
