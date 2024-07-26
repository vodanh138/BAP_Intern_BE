<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HeaderRequest extends FormRequest
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
        'headerType' => 'required|integer|between:1,3',
        'title' => 'nullable|string|max:255',
        'headerBgColor' => 'required|string|max:7',
        'headerTextColor' => 'required|string|max:7',
        ];
    }

    public function messages()
    {
        return [
        'headerType.required' => __('validation.required', ['attribute' => 'header type']),
        'headerType.integer' => __('validation.integer', ['attribute' => 'header type']),
        'headerType.between' => __('validation.between', ['attribute' => 'header type', 'min' => 1, 'max' => 3]),
        'title.string' => __('validation.string', ['attribute' => 'title']),
        'title.max' => __('validation.max.string', ['attribute' => 'title', 'max' => 255]),
        'headerBgColor.required' => __('validation.required', ['attribute' => 'header background color']),
        'headerBgColor.string' => __('validation.string', ['attribute' => 'header background color']),
        'headerBgColor.max' => __('validation.max.string', ['attribute' => 'header background color', 'max' => 7]),
        'headerTextColor.required' => __('validation.required', ['attribute' => 'header text color']),
        'headerTextColor.string' => __('validation.string', ['attribute' => 'header text color']),
        'headerTextColor.max' => __('validation.max.string', ['attribute' => 'header text color', 'max' => 7]),
        ];
    }
}
