@props(['sach', 'class' => ''])

@if ($sach->hinh_anh)
    <img src="{{ Storage::disk('public')->url($sach->hinh_anh) }}" 
         alt="Bìa sách {{ $sach->ten_sach }}" 
         class="book-cover {{ $class }}"
         loading="lazy">
@else
    <div class="book-cover book-cover-placeholder {{ $class }}" role="img" aria-label="Bìa sách: {{ $sach->ten_sach }}">
        <span class="book-placeholder-badge">Thư viện</span>
        <i class="bi bi-journal-bookmark-fill book-placeholder-icon" aria-hidden="true"></i>
        <span class="book-placeholder-text px-2">{{ $sach->ten_sach }}</span>
    </div>
@endif
