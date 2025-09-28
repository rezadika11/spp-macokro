<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KuitansiController;
use App\Http\Controllers\LaporanPembayaranController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/kuitansi/{id}', [KuitansiController::class, 'cetak'])->name('kuitansi.cetak');
Route::get('/laporan/pembayaran/cetak', [LaporanPembayaranController::class, 'cetakLaporan'])->name('laporan.pembayaran.cetak');
