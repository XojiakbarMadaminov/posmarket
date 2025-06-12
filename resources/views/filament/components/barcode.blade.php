@php
    $barcode = $getRecord()->barcode;
@endphp

@if(preg_match('/^[\w\-]+$/', $barcode))
    {{-- Harf/raqam/– bo‘lsa: CODE128 bilan ko‘rsatiladi --}}
    <div>{!! DNS1D::getBarcodeHTML($barcode, 'C128', 2, 40) !!}</div>
@else
    {{-- Yaroqsiz bo‘lsa: oddiy matn --}}
    <div class="text-gray-700 font-semibold">{{ $barcode }}</div>
@endif
