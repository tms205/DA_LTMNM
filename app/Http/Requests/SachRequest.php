<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SachRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ten_sach' => ['required', 'string', 'max:200'],
            'the_loai_id' => ['required', 'integer', 'exists:the_loais,id'],
            'nam_xuat_ban' => ['nullable', 'integer', 'between:1000,'.now()->year],
            'nha_xuat_ban' => ['nullable', 'string', 'max:150'],
            'so_luong' => ['required', 'integer', 'min:0', 'max:10000'],
            'hinh_anh' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'mo_ta' => ['nullable', 'string', 'max:5000'],
            'tac_gia_ids' => ['required', 'array', 'min:1', 'max:2'],
            'tac_gia_ids.*' => ['required', 'integer', 'distinct', 'exists:tac_gias,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'ten_sach' => 'tên sách',
            'the_loai_id' => 'thể loại',
            'nam_xuat_ban' => 'năm xuất bản',
            'nha_xuat_ban' => 'nhà xuất bản',
            'so_luong' => 'số lượng',
            'hinh_anh' => 'ảnh bìa',
            'mo_ta' => 'mô tả',
            'tac_gia_ids' => 'tác giả',
            'tac_gia_ids.*' => 'tác giả',
        ];
    }
}
