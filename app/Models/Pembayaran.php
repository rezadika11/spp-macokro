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

    /**
     * Generate nomor kuitansi otomatis dengan format KWT-YYYYMMXXXX
     */
    public function generateNomorKuitansi()
    {
        if ($this->nomor_kuitansi) {
            return $this->nomor_kuitansi;
        }

        // Format: KWT-YYYYMMXXXX
        $tahun = now()->format('Y');
        $bulan = now()->format('m');
        
        // Cari nomor urut terakhir untuk bulan ini
        $lastKuitansi = self::where('nomor_kuitansi', 'like', "KWT-{$tahun}{$bulan}%")
            ->orderBy('nomor_kuitansi', 'desc')
            ->first();
        
        if ($lastKuitansi) {
            // Ambil 4 digit terakhir dan tambah 1
            $lastNumber = (int) substr($lastKuitansi->nomor_kuitansi, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        // Format nomor urut menjadi 4 digit dengan leading zero
        $nomorUrut = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        // Gabungkan menjadi nomor kuitansi lengkap
        $nomorKuitansi = "KWT-{$tahun}{$bulan}{$nomorUrut}";
        
        // Update nomor kuitansi ke database
        $this->update(['nomor_kuitansi' => $nomorKuitansi]);
        
        return $nomorKuitansi;
    }

    /**
     * Boot method untuk event listeners
     */
    protected static function booted()
    {
        // Auto generate nomor kuitansi saat status berubah menjadi lunas
        static::updating(function ($pembayaran) {
            if ($pembayaran->isDirty('status') && $pembayaran->status === 'lunas' && !$pembayaran->nomor_kuitansi) {
                $pembayaran->generateNomorKuitansi();
            }
        });

        // Auto generate nomor kuitansi saat pembayaran dibuat dengan status lunas
        static::creating(function ($pembayaran) {
            if ($pembayaran->status === 'lunas' && !$pembayaran->nomor_kuitansi) {
                // Akan di-generate setelah record disimpan
            }
        });

        static::created(function ($pembayaran) {
            if ($pembayaran->status === 'lunas' && !$pembayaran->nomor_kuitansi) {
                $pembayaran->generateNomorKuitansi();
            }
        });
    }
}
