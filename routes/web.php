<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KuitansiController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/kuitansi/{id}', [KuitansiController::class, 'cetak'])->name('kuitansi.cetak');
