<?php

namespace App\Filament\Resources\Siswas\Pages;

use App\Filament\Resources\Siswas\SiswaResource;
use App\Models\Pembayaran;
use App\Models\TahunAkademik;
use Filament\Resources\Pages\CreateRecord;

class CreateSiswa extends CreateRecord
{
    protected static string $resource = SiswaResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $siswa = $this->record;
        $bulanPembayaran = $this->data['bulan_pembayaran'];
        $tahunAkademik = TahunAkademik::where('is_active', true)->first();

        if ($tahunAkademik && !empty($bulanPembayaran)) {
            foreach ($bulanPembayaran as $bulan) {
                Pembayaran::create([
                    'siswa_id' => $siswa->id,
                    'tahun_akademik_id' => $tahunAkademik->id,
                    'bulan' => $bulan,
                    'status' => 'belum bayar',
                ]);
            }
        }
    }
}
