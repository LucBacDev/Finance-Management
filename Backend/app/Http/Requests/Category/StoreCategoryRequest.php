<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow the request to proceed
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name',
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
            'name.unique' => 'Tên danh mục đã tồn tại. Vui lòng chọn tên khác.',
            'description.required' => 'Mô tả là bắt buộc.',
            'description.min' => 'Mô tả phải có ít nhất 10 ký tự.',
            'type_id.required' => 'Kiểu danh mục là bắt buộc.',
        ];
    }
}
