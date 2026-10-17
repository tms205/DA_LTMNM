<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TacGiaRequest extends FormRequest
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
            'ten_tac_gia' => ['required', 'string', 'max:150'],
            'nam_sinh' => ['nullable', 'integer', 'between:1000,'.now()->year],
            'quoc_tich' => ['nullable', 'string', 'max:100'],
            'tieu_su' => ['nullable', 'string', 'max:3000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'ten_tac_gia' => 'tên tác giả',
            'nam_sinh' => 'năm sinh',
            'quoc_tich' => 'quốc tịch',
            'tieu_su' => 'tiểu sử',
        ];
    }
}
