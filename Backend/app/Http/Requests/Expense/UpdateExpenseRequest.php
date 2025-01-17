<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow the request to proceed
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'user_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Tên chi tiêu là bắt buộc.',
            'title.unique' => 'Tên chi tiêu là bắt buộc.',
            'title.string' => 'Tên chi tiêu phải là một chuỗi.',
            'title.max' => 'Tên chi tiêu không được vượt quá 255 ký tự.',
            'amount.required' => 'Số tiền là bắt buộc.',
            'amount.numeric' => 'Số tiền phải là một số.',
            'amount.min' => 'Số tiền phải lớn hơn hoặc bằng 0.',
            'category_id.required' => 'Danh mục là bắt buộc.',
            'category_id.exists' => 'Danh mục không hợp lệ.',
            'date.required' => 'Ngày là bắt buộc.',
            'date.date' => 'Ngày không hợp lệ.',
            'description.string' => 'Mô tả phải là một chuỗi.',
            'user_id.required' => 'ID người dùng là bắt buộc.',
        ];
    }
}
