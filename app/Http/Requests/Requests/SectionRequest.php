<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
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
    public function rules()
    {
        return [
            'type' => 'required|integer|max:2|min:1',
            'title' => 'required|string',
            'content1' => 'nullable|string',
            'content2' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'type.required' => __('validation.required'),
            'title.required' => __('validation.required'),
        ];
    }
}
