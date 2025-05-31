<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: 40mm 30mm;
            margin: 0;
        }
        body {
            margin: 0;
            font-family: sans-serif;
        }
        .label {
            width: 100%;
            height: 100%;
            text-align: center;
            padding: 2mm;
        }
        .product-name {
            font-size: 12px;
            margin-top: 2mm;
        }
    </style>
</head>
<body>
@foreach($products as $product)
    <div class="label">
        <div class="product-name">{{ $product->name }}</div>
        {!! DNS1D::getBarcodeHTML($product->barcode, 'EAN13', 1.4, 40) !!}
        <div class="product-name">{{ $product->barcode }}</div>

    </div>
@endforeach
</body>
</html>
