<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PhieuMuonRequest extends FormRequest
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
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn (Builder $query): Builder => $query->where('role', 'user')),
            ],
            'sach_id' => ['required', 'integer', 'exists:saches,id'],
            'ngay_muon' => ['required', 'date'],
            'ngay_tra_du_kien' => ['required', 'date', 'after_or_equal:ngay_muon'],
            'ngay_tra_thuc_te' => ['nullable', 'date', 'after_or_equal:ngay_muon'],
            'trang_thai' => ['nullable', Rule::in(['dang_muon', 'da_tra', 'qua_han'])],
            'ghi_chu' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'độc giả',
            'sach_id' => 'sách',
            'ngay_muon' => 'ngày mượn',
            'ngay_tra_du_kien' => 'ngày trả dự kiến',
            'ngay_tra_thuc_te' => 'ngày trả thực tế',
            'trang_thai' => 'trạng thái',
            'ghi_chu' => 'ghi chú',
        ];
    }
}
