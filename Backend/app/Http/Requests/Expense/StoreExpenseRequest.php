<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense;

class StoreExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow the request to proceed
    }

    public function rules()
    {
        $user_id = auth::id();

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('expenses')->where(function ($query) use ($user_id) {
                    return $query->where('user_id', $user_id);
                }),
            ],
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Tên chi tiêu là bắt buộc.',
            'title.string' => 'Tên chi tiêu phải là một chuỗi.',
            'title.max' => 'Tên chi tiêu không được vượt quá 255 ký tự.',
            'title.unique' => 'Tên chi tiêu này đã tồn tại. Vui lòng chọn tên khác.',
            'amount.required' => 'Số tiền là bắt buộc.',
            'amount.numeric' => 'Số tiền phải là một số.',
            'amount.min' => 'Số tiền phải lớn hơn hoặc bằng 0.',
            'category_id.required' => 'Danh mục là bắt buộc.',
            'category_id.exists' => 'Danh mục không hợp lệ.',
            'date.required' => 'Ngày là bắt buộc.',
            'date.date' => 'Ngày không hợp lệ.',
            'description.string' => 'Mô tả phải là một chuỗi.',
        ];
    }
}
