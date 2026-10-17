<div class="row g-4">
    @forelse ($saches as $sach)
        @php
            $percentage = $sach->so_luong > 0 ? min(100, max(0, round(($sach->so_luong_con_lai / $sach->so_luong) * 100))) : 0;
            $stockClass = match(true) {
                $sach->so_luong_con_lai === 0 => 'badge-subtle-danger',
                $sach->so_luong_con_lai <= 2 => 'badge-subtle-warning',
                default => 'badge-subtle-success',
            };
            $meterClass = match(true) {
                $sach->so_luong_con_lai === 0 => 'bg-danger',
                $sach->so_luong_con_lai <= 2 => 'bg-warning',
                default => 'bg-success',
            };
        @endphp
        <div class="col-sm-6 col-lg-4">
            <article class="card book-card h-100 border">
                <div class="row g-0 h-100">
                    <div class="col-5">
                        <div class="book-cover-container h-100">
                            <x-book-cover :sach="$sach" class="h-100" />
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="card-body d-flex flex-column h-100 p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge badge-subtle-primary small rounded-pill">
                                    {{ $sach->theLoai->ten_the_loai }}
                                </span>
                            </div>

                            <h3 class="h6 fw-bold mb-1 text-dark text-truncate" title="{{ $sach->ten_sach }}">
                                <a href="{{ route('catalog.show', $sach) }}" class="text-dark text-decoration-none">
                                    {{ $sach->ten_sach }}
                                </a>
                            </h3>

                            <p class="small text-muted mb-2 text-truncate" title="{{ $sach->tacGias->pluck('ten_tac_gia')->join(', ') }}">
                                <i class="bi bi-person me-1" aria-hidden="true"></i>
                                {{ $sach->tacGias->pluck('ten_tac_gia')->join(', ') ?: 'Chưa rõ tác giả' }}
                            </p>

                            <div class="mt-auto pt-2">
                                <div class="d-flex justify-content-between align-items-center small mb-1">
                                    <span class="text-muted">Khả dụng</span>
                                    <span class="badge {{ $stockClass }} rounded-pill">
                                        {{ $sach->so_luong_con_lai }}/{{ $sach->so_luong }} cuốn
                                    </span>
                                </div>
                                <div class="stock-meter mb-3" role="progressbar" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100" aria-label="Tỷ lệ sách còn lại">
                                    <div class="stock-meter-bar {{ $meterClass }}" style="width: {{ $percentage }}%"></div>
                                </div>
                                
                                <a href="{{ route('catalog.show', $sach) }}" class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                                    <span>Xem chi tiết</span>
                                    <i class="bi bi-arrow-right small" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-dashed">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-journal-x" aria-hidden="true"></i>
                    </div>
                    <h3 class="h5 fw-bold text-dark mb-2">Không tìm thấy sách phù hợp</h3>
                    <p class="text-muted small mb-4" style="max-width: 480px; margin-inline: auto;">
                        Không có cuốn sách nào khớp với từ khóa tìm kiếm hoặc bộ lọc hiện tại. Bạn vui lòng thử lại với từ khóa khác hoặc xóa bộ lọc.
                    </p>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="const q=document.getElementById('q'); const cat=document.getElementById('the_loai_id'); if(q) q.value=''; if(cat) cat.value=''; document.getElementById('catalog-search').dispatchEvent(new Event('submit'));">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Đặt lại tìm kiếm
                    </button>
                </div>
            </div>
        </div>
    @endforelse
</div>

@if ($saches->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $saches->links() }}
    </div>
@endif
