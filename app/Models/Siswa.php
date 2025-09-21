<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;


    protected $table = 'siswa';
    protected $fillable = ['nis', 'nama', 'kelas_id', 'no_hp', 'alamat'];

    /**
     * Relationship dengan Kelas
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    protected static function booted()
    {
        static::created(function ($siswa) {
            $tahunAkademik = TahunAkademik::where('aktif', true)->first();
            if ($tahunAkademik) {
                $start = \Carbon\Carbon::parse($tahunAkademik->mulai)->startOfMonth();
                $end   = \Carbon\Carbon::parse($tahunAkademik->selesai)->endOfMonth();

                while ($start <= $end) {
                    Pembayaran::create([
                        'siswa_id' => $siswa->id,
                        'tahun_akademik_id' => $tahunAkademik->id,
                        'bulan' => $start->translatedFormat('F'), // Format nama bulan dalam bahasa Indonesia
                        'jumlah' => 125000, // Jumlah SPP tetap 125rb
                        'status' => 'belum_bayar',
                    ]);

                    $start->addMonth();
                }
            }
        });
    }
}
