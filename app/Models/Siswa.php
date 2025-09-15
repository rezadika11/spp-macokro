<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;


    protected $table = 'siswa';
    protected $fillable = ['nis', 'nama', 'kelas', 'no_hp', 'alamat'];


    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }
}
