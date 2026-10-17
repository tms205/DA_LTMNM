<div class="row g-3">
    <div class="col-md-8">
        <label for="ten_tac_gia" class="form-label fw-semibold text-dark">
            Tên tác giả <span class="text-danger">*</span>
        </label>
        <div class="input-icon-group">
            <span class="input-icon"><i class="bi bi-person"></i></span>
            <input type="text" 
                   id="ten_tac_gia" 
                   name="ten_tac_gia" 
                   value="{{ old('ten_tac_gia', $tacGia->ten_tac_gia ?? '') }}" 
                   class="form-control @error('ten_tac_gia') is-invalid @enderror" 
                   maxlength="150" 
                   placeholder="Nhập họ và tên tác giả..."
                   required>
        </div>
        @error('ten_tac_gia')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="nam_sinh" class="form-label fw-semibold text-dark">Năm sinh</label>
        <input type="number" 
               id="nam_sinh" 
               name="nam_sinh" 
               value="{{ old('nam_sinh', $tacGia->nam_sinh ?? '') }}" 
               class="form-control @error('nam_sinh') is-invalid @enderror" 
               min="1000" 
               max="{{ now()->year }}"
               placeholder="VD: 1980">
        @error('nam_sinh')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="quoc_tich" class="form-label fw-semibold text-dark">Quốc tịch</label>
        <input type="text" 
               id="quoc_tich" 
               name="quoc_tich" 
               value="{{ old('quoc_tich', $tacGia->quoc_tich ?? 'Việt Nam') }}" 
               class="form-control @error('quoc_tich') is-invalid @enderror" 
               maxlength="100"
               placeholder="VD: Việt Nam, Anh, Nhật Bản...">
        @error('quoc_tich')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="tieu_su" class="form-label fw-semibold text-dark">Tiểu sử &amp; Sự nghiệp</label>
        <textarea id="tieu_su" 
                  name="tieu_su" 
                  rows="5" 
                  class="form-control @error('tieu_su') is-invalid @enderror" 
                  placeholder="Tóm tắt cuộc đời, phong cách sáng tác, các giải thưởng đạt được...">{{ old('tieu_su', $tacGia->tieu_su ?? '') }}</textarea>
        @error('tieu_su')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary px-4 shadow-sm">
        <i class="bi bi-check-lg me-1"></i> {{ $submitLabel }}
    </button>
    <a href="{{ route('admin.tac-gias.index') }}" class="btn btn-outline-secondary">
        Hủy bỏ
    </a>
</div>
