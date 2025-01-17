<?php

namespace App\Http\Requests\Income;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncomeRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow the request to proceed
    }

    public function rules()
    {
        return [
            'source' => 'required|string|max:255|',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',  // Validate category_id exists
        ];
    }

    public function messages()
    {
        return [
            'source.required' => 'Nguồn thu nhập là bắt buộc.',
            'source.string' => 'Nguồn thu nhập phải là một chuỗi.',
            'source.max' => 'Nguồn thu nhập không được vượt quá 255 ký tự.',
            'amount.required' => 'Số tiền là bắt buộc.',
            'amount.numeric' => 'Số tiền phải là một số.',
            'date.required' => 'Ngày là bắt buộc.',
            'date.date' => 'Ngày không hợp lệ.',
            'category_id.required' => 'Danh mục là bắt buộc.',
            'category_id.exists' => 'Danh mục không hợp lệ.',
            'description.string' => 'Mô tả phải là một chuỗi.',
        ];
    }
}
