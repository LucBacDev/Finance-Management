<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
        $id = $this->route('id'); // Get the category ID from the route parameter

        return [
            'name' => 'required|string|max:255' . $id,
            'description' => 'required|min:10',
            'type_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.string' => 'Tên danh mục phải là một chuỗi.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'description.required' => 'Mô tả là bắt buộc.',
            'description.min' => 'Mô tả phải có ít nhất 10 ký tự.',
            'type_id.required' => 'Kiểu danh mục là bắt buộc.',
        ];
    }
}
