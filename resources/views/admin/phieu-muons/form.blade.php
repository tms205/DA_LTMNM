<div class="row g-3">
    <div class="col-md-6">
        <label for="user_id" class="form-label fw-semibold text-dark">
            Độc giả mượn sách <span class="text-danger">*</span>
        </label>
        <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror" required onchange="capNhatThongTinDocGia(this)">
            <option value="">-- Chọn tài khoản độc giả --</option>
            @foreach ($users as $user)
                @php
                    $lyLich = $user->danhGiaLyLich();
                @endphp
                <option value="{{ $user->id }}" 
                        @selected((int) old('user_id', $phieuMuon->user_id ?? 0) === $user->id)
                        data-status="{{ $lyLich['muc_do'] }}"
                        data-label="{{ $lyLich['nhan_hien_thi'] }}"
                        data-can-borrow="{{ $lyLich['co_the_muon'] ? '1' : '0' }}"
                        data-reason="{{ $lyLich['ly_do_khong_the_muon'] ?? '' }}"
                        data-desc="{{ $lyLich['mo_ta'] ?? '' }}"
                        data-note="{{ $user->ghi_chu_thu_thu ?? '' }}"
                        class="@if(!$lyLich['co_the_muon']) text-danger fw-bold @endif">
                    {{ $user->name }} — [{{ $lyLich['nhan_hien_thi'] }}] ({{ $user->email }})
                </option>
            @endforeach
        </select>
        <div id="reader-credit-preview" class="mt-2 d-none"></div>
        @error('user_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="sach_id" class="form-label fw-semibold text-dark">
            Sách cần mượn <span class="text-danger">*</span>
        </label>
        <select id="sach_id" name="sach_id" class="form-select @error('sach_id') is-invalid @enderror" required>
            <option value="">-- Chọn cuốn sách --</option>
            @foreach ($saches as $sach)
                @php 
                    $isCurrentBook = isset($phieuMuon) && $phieuMuon->sach_id === $sach->id; 
                @endphp
                <option value="{{ $sach->id }}" 
                        @selected((int) old('sach_id', $phieuMuon->sach_id ?? request('sach_id')) === $sach->id) 
                        @disabled($sach->so_luong_con_lai <= 0 && ! $isCurrentBook)>
                    {{ $sach->ten_sach }} (Còn: {{ $sach->so_luong_con_lai }} / {{ $sach->so_luong }})
                </option>
            @endforeach
        </select>
        @error('sach_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="ngay_muon" class="form-label fw-semibold text-dark">
            Ngày mượn <span class="text-danger">*</span>
        </label>
        <div class="input-icon-group">
            <span class="input-icon"><i class="bi bi-calendar3"></i></span>
            <input type="date" 
                   id="ngay_muon" 
                   name="ngay_muon" 
                   value="{{ old('ngay_muon', isset($phieuMuon) ? $phieuMuon->ngay_muon->format('Y-m-d') : today()->format('Y-m-d')) }}" 
                   class="form-control @error('ngay_muon') is-invalid @enderror" 
                   required>
        </div>
        @error('ngay_muon')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="ngay_tra_du_kien" class="form-label fw-semibold text-dark">
            Hạn trả dự kiến <span class="text-danger">*</span>
        </label>
        <div class="input-icon-group">
            <span class="input-icon"><i class="bi bi-calendar-check"></i></span>
            <input type="date" 
                   id="ngay_tra_du_kien" 
                   name="ngay_tra_du_kien" 
                   value="{{ old('ngay_tra_du_kien', isset($phieuMuon) ? $phieuMuon->ngay_tra_du_kien->format('Y-m-d') : today()->addDays(14)->format('Y-m-d')) }}" 
                   class="form-control @error('ngay_tra_du_kien') is-invalid @enderror" 
                   required>
        </div>
        <div class="form-text small text-muted">Quy định mặc định của thư viện là 14 ngày kể từ ngày mượn.</div>
        @error('ngay_tra_du_kien')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    @if (isset($phieuMuon))
        <div class="col-md-6">
            <label for="trang_thai" class="form-label fw-semibold text-dark">Trạng thái phiếu</label>
            <select id="trang_thai" name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror">
                <option value="dang_muon" @selected(old('trang_thai', $phieuMuon->trangThaiHienThi()) === 'dang_muon')>Đang mượn</option>
                <option value="qua_han" @selected(old('trang_thai', $phieuMuon->trangThaiHienThi()) === 'qua_han')>Quá hạn</option>
                <option value="da_tra" @selected(old('trang_thai', $phieuMuon->trangThaiHienThi()) === 'da_tra')>Đã trả</option>
            </select>
            @error('trang_thai')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="ngay_tra_thuc_te" class="form-label fw-semibold text-dark">Ngày trả thực tế</label>
            <div class="input-icon-group">
                <span class="input-icon"><i class="bi bi-calendar2-check"></i></span>
                <input type="date" 
                       id="ngay_tra_thuc_te" 
                       name="ngay_tra_thuc_te" 
                       value="{{ old('ngay_tra_thuc_te', $phieuMuon->ngay_tra_thuc_te?->format('Y-m-d')) }}" 
                       class="form-control @error('ngay_tra_thuc_te') is-invalid @enderror">
            </div>
            @error('ngay_tra_thuc_te')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    @endif

    <div class="col-12">
        <label for="ghi_chu" class="form-label fw-semibold text-dark">Ghi chú lưu ý</label>
        <textarea id="ghi_chu" 
                  name="ghi_chu" 
                  rows="3" 
                  class="form-control @error('ghi_chu') is-invalid @enderror" 
                  placeholder="Ghi chú tình trạng sách khi bàn giao, thông tin liên lạc thêm...">{{ old('ghi_chu', $phieuMuon->ghi_chu ?? '') }}</textarea>
        @error('ghi_chu')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary px-4 shadow-sm">
        <i class="bi bi-check-lg me-1"></i> {{ $submitLabel }}
    </button>
    <a href="{{ route('admin.phieu-muons.index') }}" class="btn btn-outline-secondary">
        Hủy bỏ
    </a>
</div>

<script>
function capNhatThongTinDocGia(selectEl) {
    const preview = document.getElementById('reader-credit-preview');
    if (!preview) return;

    const opt = selectEl.options[selectEl.selectedIndex];
    if (!opt || !opt.value) {
        preview.classList.add('d-none');
        preview.innerHTML = '';
        return;
    }

    const canBorrow = opt.getAttribute('data-can-borrow') === '1';
    const label = opt.getAttribute('data-label') || '';
    const desc = opt.getAttribute('data-desc') || '';
    const reason = opt.getAttribute('data-reason') || '';
    const note = opt.getAttribute('data-note') || '';

    preview.classList.remove('d-none');

    if (!canBorrow) {
        preview.innerHTML = `
            <div class="alert alert-danger border-0 p-3 rounded-3 small mb-0">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-octagon-fill fs-5 text-danger flex-shrink-0"></i>
                    <div>
                        <strong class="d-block text-danger">ĐỘC GIẢ KHÔNG ĐỦ ĐIỀU KIỆN MƯỢN SÁCH (${label})</strong>
                        <div class="mt-1">${reason}</div>
                        ${note ? `<div class="mt-1 fst-italic text-dark border-top pt-1"><strong>Lưu ý từ thủ thư:</strong> ${note}</div>` : ''}
                    </div>
                </div>
            </div>
        `;
    } else {
        preview.innerHTML = `
            <div class="alert alert-info border-0 p-2 px-3 rounded-3 small mb-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="bi bi-shield-check text-info me-1"></i>
                        <strong>Lý lịch mượn:</strong> ${label} - ${desc}
                    </div>
                </div>
                ${note ? `<div class="mt-1 fst-italic text-secondary border-top pt-1"><strong>Lưu ý từ thủ thư:</strong> ${note}</div>` : ''}
            </div>
        `;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const selectEl = document.getElementById('user_id');
    if (selectEl && selectEl.value) {
        capNhatThongTinDocGia(selectEl);
    }
});
</script>
