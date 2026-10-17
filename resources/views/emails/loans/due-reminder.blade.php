<x-mail::message>
# Nhắc hạn trả sách

Xin chào **{{ $reader->name }}**,

Thư viện HUIT gửi bạn thông tin các sách sắp đến hạn hoặc đến hạn trả.

@if ($dueTodayLoans->isNotEmpty())
## Đến hạn hôm nay

@foreach ($dueTodayLoans as $loan)
- **{{ $loan->sach->ten_sach }}** - Phiếu #{{ $loan->id }}, hạn trả {{ $loan->ngay_tra_du_kien->format('d/m/Y') }}
@endforeach
@endif

@if ($dueTomorrowLoans->isNotEmpty())
## Sắp đến hạn ngày mai

@foreach ($dueTomorrowLoans as $loan)
- **{{ $loan->sach->ten_sach }}** - Phiếu #{{ $loan->id }}, hạn trả {{ $loan->ngay_tra_du_kien->format('d/m/Y') }}
@endforeach
@endif

<x-mail::button :url="$loanHistoryUrl">
Xem lịch sử mượn
</x-mail::button>

Vui lòng hoàn trả sách đúng hạn để tránh phát sinh phiếu phạt.

Trân trọng,<br>
Thư viện HUIT
</x-mail::message>
