<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentAttemptRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'teacher';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom message definitions.
     */
    public function messages(): array
    {
        return [
            'answers.required' => 'Please provide answers before submitting your assessment.',
            'answers.*.required' => 'Each question must have a chosen option.',
            'answers.*.string' => 'Each answer must be valid text or an option selection.',
        ];
    }
}
