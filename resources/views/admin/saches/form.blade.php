@php
    $selectedTacGiaIds = old('tac_gia_ids', isset($sach) ? $sach->tacGias->pluck('id')->all() : []);
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label for="ten_sach" class="form-label fw-semibold text-dark">
            Tên sách <span class="text-danger">*</span>
        </label>
        <div class="input-icon-group">
            <span class="input-icon"><i class="bi bi-book"></i></span>
            <input type="text" 
                   id="ten_sach" 
                   name="ten_sach" 
                   value="{{ old('ten_sach', $sach->ten_sach ?? '') }}" 
                   class="form-control @error('ten_sach') is-invalid @enderror" 
                   maxlength="200" 
                   placeholder="Nhập tên đầu sách..."
                   required>
        </div>
        @error('ten_sach')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="the_loai_id" class="form-label fw-semibold text-dark">
            Thể loại <span class="text-danger">*</span>
        </label>
        <select id="the_loai_id" name="the_loai_id" class="form-select @error('the_loai_id') is-invalid @enderror" required>
            <option value="">-- Chọn thể loại --</option>
            @foreach ($theLoais as $theLoai)
                <option value="{{ $theLoai->id }}" @selected((int) old('the_loai_id', $sach->the_loai_id ?? 0) === $theLoai->id)>
                    {{ $theLoai->ten_the_loai }}
                </option>
            @endforeach
        </select>
        @error('the_loai_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="tac_gia_ids" class="form-label fw-semibold text-dark">
            Tác giả (chọn 1 đến tối đa 2 tác giả) <span class="text-danger">*</span>
        </label>
        <select id="tac_gia_ids" name="tac_gia_ids[]" class="form-select @error('tac_gia_ids') is-invalid @enderror" size="5" multiple required>
            @foreach ($tacGias as $tacGia)
                <option value="{{ $tacGia->id }}" @selected(in_array($tacGia->id, $selectedTacGiaIds))>
                    {{ $tacGia->ten_tac_gia }} {{ $tacGia->nam_sinh ? '('.$tacGia->nam_sinh.')' : '' }}
                </option>
            @endforeach
        </select>
        <div class="form-text small text-muted">
            <i class="bi bi-info-circle me-1"></i> Giữ phím <kbd class="bg-light text-dark border">Ctrl</kbd> (trên Windows) hoặc <kbd class="bg-light text-dark border">Cmd</kbd> (trên Mac) để chọn nhiều tác giả.
        </div>
        @error('tac_gia_ids')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        @error('tac_gia_ids.*')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="nam_xuat_ban" class="form-label fw-semibold text-dark">Năm xuất bản</label>
        <input type="number" 
               id="nam_xuat_ban" 
               name="nam_xuat_ban" 
               value="{{ old('nam_xuat_ban', $sach->nam_xuat_ban ?? '') }}" 
               class="form-control @error('nam_xuat_ban') is-invalid @enderror" 
               min="1000" 
               max="{{ now()->year }}"
               placeholder="VD: {{ now()->year }}">
        @error('nam_xuat_ban')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="nha_xuat_ban" class="form-label fw-semibold text-dark">Nhà xuất bản</label>
        <input type="text" 
               id="nha_xuat_ban" 
               name="nha_xuat_ban" 
               value="{{ old('nha_xuat_ban', $sach->nha_xuat_ban ?? '') }}" 
               class="form-control @error('nha_xuat_ban') is-invalid @enderror" 
               maxlength="150"
               placeholder="VD: NXB Trẻ, NXB Giáo dục...">
        @error('nha_xuat_ban')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="so_luong" class="form-label fw-semibold text-dark">
            Tổng số lượng bản in <span class="text-danger">*</span>
        </label>
        <input type="number" 
               id="so_luong" 
               name="so_luong" 
               value="{{ old('so_luong', $sach->so_luong ?? 0) }}" 
               class="form-control @error('so_luong') is-invalid @enderror" 
               min="0" 
               max="10000" 
               required>
        @error('so_luong')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="hinh_anh" class="form-label fw-semibold text-dark">Ảnh bìa sách</label>
        <input type="file" 
               id="hinh_anh" 
               name="hinh_anh" 
               class="form-control @error('hinh_anh') is-invalid @enderror" 
               accept="image/jpeg,image/png,image/webp">
        <div class="form-text small text-muted">Định dạng hỗ trợ: JPG, PNG hoặc WebP. Dung lượng tối đa: 2 MB.</div>
        @error('hinh_anh')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    @if (isset($sach) && $sach->hinh_anh)
        <div class="col-12">
            <div class="p-3 bg-light rounded-3 border d-inline-flex align-items-center gap-3">
                <div style="width: 60px;">
                    <x-book-cover :sach="$sach" class="rounded" />
                </div>
                <div class="small">
                    <span class="badge badge-subtle-primary mb-1">Ảnh bìa hiện tại</span>
                    <div class="text-muted">Tải lên file mới để thay thế ảnh bìa này.</div>
                </div>
            </div>
        </div>
    @endif

    <div class="col-12">
        <label for="mo_ta" class="form-label fw-semibold text-dark">Mô tả / Tóm tắt nội dung</label>
        <textarea id="mo_ta" 
                  name="mo_ta" 
                  rows="5" 
                  class="form-control @error('mo_ta') is-invalid @enderror" 
                  placeholder="Nhập nội dung tóm tắt, mục lục chính của tác phẩm...">{{ old('mo_ta', $sach->mo_ta ?? '') }}</textarea>
        @error('mo_ta')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary px-4 shadow-sm">
        <i class="bi bi-check-lg me-1"></i> {{ $submitLabel }}
    </button>
    <a href="{{ route('admin.saches.index') }}" class="btn btn-outline-secondary">
        Hủy bỏ
    </a>
</div>
