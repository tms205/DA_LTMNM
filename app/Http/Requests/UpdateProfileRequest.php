<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'so_dien_thoai' => ['nullable', 'string', 'max:20', 'regex:/^(0[3|5|7|8|9])[0-9]{8}$/'],
            'dia_chi' => ['nullable', 'string', 'max:255'],
            'ngay_sinh' => ['nullable', 'date', 'before:today'],
        ];
    }

    /**
     * Custom Vietnamese error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'so_dien_thoai.regex' => 'Số điện thoại không đúng định dạng di động Việt Nam (gồm 10 số, bắt đầu bằng 03, 05, 07, 08 hoặc 09).',
            'so_dien_thoai.max' => 'Số điện thoại không hợp lệ.',
            'dia_chi.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'ngay_sinh.date' => 'Ngày sinh không hợp lệ.',
            'ngay_sinh.before' => 'Ngày tháng năm sinh phải trước ngày hôm nay.',
        ];
    }
}
