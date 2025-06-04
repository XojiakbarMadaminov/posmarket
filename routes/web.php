<?php

use App\Models\Debtor;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/admin/dashboard-unlock', 'filament.components.auth')
    ->name('dashboard.unlock')
    ->middleware('web'); // Session ishlashi uchun
Route::post('/admin/dashboard-unlock', fn () => null)
    ->name('dashboard.unlock')
    ->middleware('web', 'dashboard.password');


// debtor uchun
Route::get('/debtor/{debtor}/check-pdf', function (Debtor $debtor) {
    $debtor->load('transactions');

    $base = 300;
    $extra = 20 * $debtor->transactions->count();
    $height = min(396, $base + $extra); // max 140mm

    return Pdf::loadView('debtor-check', compact('debtor'))
        ->setPaper([0, 0, 176, $height], 'portrait')  // 62mm × height
        ->stream('check.pdf');
})->name('debtor.check.pdf');


// 1. Bitta product uchun
Route::get('/products/{product}/barcode-pdf', function (Product $product) {
    return Pdf::loadView('product-barcode', ['products' => collect([$product])])
        ->setPaper([0, 0, 136, 85.0]) // 50mm x 30mm → 1mm = 2.834pt
        ->setOptions(['defaultFont' => 'sans-serif'])
        ->stream("barcode-{$product->id}.pdf");
})->name('product.barcode.pdf');

// 2. Ko‘p product uchun (masalan, tanlanganlar)
Route::get('/products/barcodes/bulk', function () {
    $productIds = request()->input('ids', []); // ?ids[]=1&ids[]=3&ids[]=5
    $products = Product::whereIn('id', $productIds)->get();

    return Pdf::loadView('product-barcode', compact('products'))
        ->setPaper([0, 0, 136, 85.0]) // ko‘proq sahifali variant uchun A4 mos
        ->stream("barcodes.pdf");
})->name('product.barcodes.bulk');
