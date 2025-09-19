<?php

namespace App\Filament\Resources\Pembayarans\Pages;

use App\Filament\Resources\Pembayarans\PembayaranResource;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPembayarans extends ListRecords
{
    protected static string $resource = PembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_tagihan')
                ->label('Generate Tagihan Bulan Ini')
                ->icon('heroicon-o-bolt')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Generate Tagihan Massal')
                ->modalDescription('Anda akan membuat tagihan SPP untuk bulan ini bagi semua siswa yang belum memilikinya. Proses ini tidak dapat diurungkan.')
                ->modalSubmitActionLabel('Ya, Generate Sekarang')
                ->action(function () {
                    $bulanIni = Carbon::now()->format('m');
                    $namaBulanIni = Carbon::now()->translatedFormat('F');
                    $tahunAkademik = TahunAkademik::where('is_active', true)->first();

                    if (!$tahunAkademik) {
                        Notification::make()
                            ->title('Aksi Gagal')
                            ->body('Tidak ada tahun akademik yang aktif. Silakan atur terlebih dahulu.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $siswaTanpaTagihan = Siswa::whereDoesntHave('pembayaran', function ($query) use ($bulanIni, $tahunAkademik) {
                        $query->where('bulan', $bulanIni)
                              ->where('tahun_akademik_id', $tahunAkademik->id);
                    })->get();

                    if ($siswaTanpaTagihan->isEmpty()) {
                        Notification::make()
                            ->title('Tidak Ada Aksi')
                            ->body('Semua siswa sudah memiliki tagihan untuk bulan ' . $namaBulanIni . '.')
                            ->info()
                            ->send();
                        return;
                    }

                    foreach ($siswaTanpaTagihan as $siswa) {
                        Pembayaran::create([
                            'siswa_id' => $siswa->id,
                            'tahun_akademik_id' => $tahunAkademik->id,
                            'bulan' => $bulanIni,
                            'status' => 'belum bayar',
                        ]);
                    }

                    Notification::make()
                        ->title('Generate Tagihan Berhasil')
                        ->body('Berhasil membuat ' . $siswaTanpaTagihan->count() . ' tagihan baru untuk bulan ' . $namaBulanIni . '.')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
