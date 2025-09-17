<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\FontteService;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Log;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $fillable = ['siswa_id', 'tahun_akademik_id', 'bulan', 'jumlah', 'status', 'tanggal_bayar', 'metode', 'nomor_kuitansi'];
    protected $dates = ['tanggal_bayar'];



    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }
}
