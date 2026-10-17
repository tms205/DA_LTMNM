<div class="row g-3">
    <div class="col-12">
        <label for="ten_the_loai" class="form-label fw-semibold text-dark">
            Tên thể loại sách <span class="text-danger">*</span>
        </label>
        <div class="input-icon-group">
            <span class="input-icon"><i class="bi bi-tag"></i></span>
            <input type="text" 
                   id="ten_the_loai" 
                   name="ten_the_loai" 
                   value="{{ old('ten_the_loai', $theLoai->ten_the_loai ?? '') }}" 
                   class="form-control @error('ten_the_loai') is-invalid @enderror" 
                   maxlength="100" 
                   placeholder="VD: Công nghệ thông tin, Văn học hiện đại..."
                   required>
        </div>
        @error('ten_the_loai')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="mo_ta" class="form-label fw-semibold text-dark">Mô tả thể loại</label>
        <textarea id="mo_ta" 
                  name="mo_ta" 
                  rows="4" 
                  class="form-control @error('mo_ta') is-invalid @enderror" 
                  placeholder="Mô tả phạm vi đề tài, định hướng bạn đọc của thể loại này...">{{ old('mo_ta', $theLoai->mo_ta ?? '') }}</textarea>
        @error('mo_ta')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary px-4 shadow-sm">
        <i class="bi bi-check-lg me-1"></i> {{ $submitLabel }}
    </button>
    <a href="{{ route('admin.the-loais.index') }}" class="btn btn-outline-secondary">
        Hủy bỏ
    </a>
</div>
