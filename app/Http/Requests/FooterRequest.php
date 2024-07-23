<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FooterRequest extends FormRequest
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
        'footerType' => 'required|integer|between:1,2',
        'footer' => 'nullable|string|max:255',
        'footerBgColor' => 'required|string|max:7',
        'footerTextColor' => 'required|string|max:7',
        ];
    }

    public function messages()
    {
        return [
        'footerType.required' => __('validation.required', ['attribute' => 'footer type']),
        'footerType.integer' => __('validation.integer', ['attribute' => 'footer type']),
        'footerType.between' => __('validation.between', ['attribute' => 'footer type', 'min' => 1, 'max' => 2]),
        'footer.required' => __('validation.required', ['attribute' => 'footer content 1']),
        'footer.string' => __('validation.string', ['attribute' => 'footer content']),
        'footer.max' => __('validation.max.string', ['attribute' => 'footer content', 'max' => 255]),
        'footerBgColor.required' => __('validation.required', ['attribute' => 'footer background color']),
        'footerBgColor.string' => __('validation.string', ['attribute' => 'footer background color']),
        'footerBgColor.max' => __('validation.max.string', ['attribute' => 'footer background color', 'max' => 7]),
        'footerTextColor.required' => __('validation.required', ['attribute' => 'footer text color']),
        'footerTextColor.string' => __('validation.string', ['attribute' => 'footer text color']),
        'footerTextColor.max' => __('validation.max.string', ['attribute' => 'footer text color', 'max' => 7]),
        ];
    }
}
