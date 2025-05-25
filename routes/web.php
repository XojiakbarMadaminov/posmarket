<?php

use App\Models\Debtor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/debtor/{debtor}/check-pdf', function (Debtor $debtor) {
    $pdf = Pdf::loadView('debtor-check', ['debtor' => $debtor])
        ->setPaper([0, 0, 227, 800], 'portrait'); // 80mm x 280mm

    return $pdf->stream('qarzdorlik-cheki.pdf');
})->name('debtor.check.pdf');
