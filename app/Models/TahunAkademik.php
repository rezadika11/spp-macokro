<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAkademik extends Model
{
    protected $table = 'tahun_akademik';
    protected $fillable = ['nama', 'mulai', 'selesai', 'aktif'];

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }
}
