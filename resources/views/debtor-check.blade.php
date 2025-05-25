<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: monospace;
            font-size: 12px;
            margin: 0;
            padding: 10px;
            width: 226.79pt; /* 80mm */
        }

        .line {
            margin-bottom: 6px;
        }

        .center {
            text-align: center;
            margin: 12px 0;
        }

        .divider {
            border-top: 1px dashed black;
            margin: 12px 0;
        }
    </style>
</head>
<body>
<div class="center">*** QARZDORLIK CHEKI ***</div>

<div class="line">Sana:      {{ now()->format('Y-m-d') }}</div>
<div class="line">Ism:       {{ $debtor->full_name }}</div>
<div class="line">Tel:       {{ $debtor->phone }}</div>
<div class="line">Valyuta:   {{ strtoupper($debtor->currency) }}</div>
<div class="line">Qarz:      {{ number_format($debtor->amount, 0, '.', ' ') }}</div>

@if ($debtor->note)
    <div class="line">Izoh:</div>
    <div class="line">{{ $debtor->note }}</div>
@endif

<div class="divider"></div>
<div class="center">POSMARKET DASTURI ORQALI YARATILDI</div>
</body>
</html>
