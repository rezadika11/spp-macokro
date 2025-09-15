<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAkademik extends Model
{
    protected $table = 'tahun_akademik';
    protected $fillable = ['nama', 'aktif'];

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            // Jika ada tahun akademik aktif, cegah tambah aktif baru
            if ($model->aktif) {
                static::where('aktif', true)->update(['aktif' => false]);
            }
        });

        static::updating(function ($model) {
            // Kalau set aktif, nonaktifkan yang lain
            if ($model->isDirty('aktif') && $model->aktif) {
                static::where('id', '!=', $model->id)
                    ->where('aktif', true)
                    ->update(['aktif' => false]);
            }
        });
    }
}
