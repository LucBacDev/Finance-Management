<?php

namespace App\Http\Requests\Income;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncomeRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow the request to proceed
    }

    public function rules()
    {
        return [
            'source' => 'required|string|max:255|unique:incomes,source,' . $this->route('id'), // Unique except the current record
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'category_id' => 'required|integer',
            'description' => 'nullable|string',
            'user_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'source.required' => 'Nguồn thu nhập là bắt buộc.',
            'source.string' => 'Nguồn thu nhập phải là một chuỗi.',
            'source.max' => 'Nguồn thu nhập không được vượt quá 255 ký tự.',
            'source.unique' => 'Nguồn thu nhập này đã tồn tại. Vui lòng chọn một nguồn khác.',
            'amount.required' => 'Số tiền là bắt buộc.',
            'amount.numeric' => 'Số tiền phải là một số.',
            'date.required' => 'Ngày là bắt buộc.',
            'date.date' => 'Ngày không hợp lệ.',
            'category_id.required' => 'Danh mục là bắt buộc.',
            'category_id.integer' => 'Danh mục phải là một số nguyên hợp lệ.',
            'description.string' => 'Mô tả phải là một chuỗi.',
            'user_id.required' => 'ID người dùng là bắt buộc.',
        ];
    }
}
