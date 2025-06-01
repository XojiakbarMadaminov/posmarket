<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: 23mm 30mm;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: sans-serif;
        }

        .label {
            width: 100%;
            height: 100%;
            padding: 1mm;
            box-sizing: border-box;
            text-align: center;
        }

        .product-name {
            font-size: 8px;
            margin-bottom: 1mm;
            word-wrap: break-word;
        }

    </style>
</head>
<body>
@foreach($products as $product)
    <div class="label">
        <div class="product-name">{{ $product->name }}</div>
        {!! DNS1D::getBarcodeHTML($product->barcode, 'EAN13', 1.0, 24) !!}

        <div class="product-name">{{ $product->barcode }}</div>
    </div>
@endforeach
</body>
</html>
