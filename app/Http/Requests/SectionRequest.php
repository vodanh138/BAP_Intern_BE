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
        'type' => 'required|integer|between:1,2',
        'title' => 'required|string|max:255',
        'content1' => 'required|string',
        'content2' => 'nullable|string',
        'bgColor' => 'required|string|max:7',
        'textColor' => 'required|string|max:7',
        ];
    }

    public function messages()
    {
        return [
        'type.required' => __('validation.required', ['attribute' => 'type']),
        'type.integer' => __('validation.integer', ['attribute' => 'type']),
        'type.between' => __('validation.between', ['attribute' => 'type', 'min' => 1, 'max' => 2]),
        'title.required' => __('validation.required', ['attribute' => 'title']),
        'title.string' => __('validation.string', ['attribute' => 'title']),
        'title.max' => __('validation.max.string', ['attribute' => 'title', 'max' => 255]),
        'content1.required' => __('validation.required', ['attribute' => 'content1']),
        'content1.string' => __('validation.string', ['attribute' => 'content1']),
        'content2.string' => __('validation.string', ['attribute' => 'content2']),
        'bgColor.required' => __('validation.required', ['attribute' => 'bgColor']),
        'bgColor.string' => __('validation.string', ['attribute' => 'bgColor']),
        'bgColor.max' => __('validation.max.string', ['attribute' => 'bgColor', 'max' => 7]),
        'textColor.required' => __('validation.required', ['attribute' => 'textColor']),
        'textColor.string' => __('validation.string', ['attribute' => 'textColor']),
        'textColor.max' => __('validation.max.string', ['attribute' => 'textColor', 'max' => 7]),
        ];
    }
}
