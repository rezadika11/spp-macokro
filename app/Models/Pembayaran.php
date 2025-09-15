<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;


    protected $table = 'pembayaran';
    protected $fillable = ['siswa_id', 'tahun_akademik_id', 'bulan', 'jumlah', 'status', 'jatuh_tempo', 'tanggal_bayar', 'metode', 'nomor_kuitansi'];
    protected $dates = ['jatuh_tempo', 'tanggal_bayar'];


    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }


    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }
}
