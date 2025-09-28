<?php

namespace App\Filament\Resources\Pembayarans\Pages;

use App\Filament\Resources\Pembayarans\PembayaranResource;
use App\Models\TahunAkademik;
use App\Models\Kelas;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class ListPembayarans extends ListRecords
{
    protected static string $resource = PembayaranResource::class;

    protected function getHeaderActions(): array
    {
        $user = Auth::user();
        $actions = [];

        // Button Cetak Laporan PDF untuk role keuangan dan kepsek
        if ($user && ($user->isKeuangan() || $user->isKepsek())) {
            $actions[] = Action::make('cetak_laporan')
                ->label('Cetak Laporan PDF')
                ->icon('heroicon-o-document-text')
                ->color('success')
                ->form([
                    Select::make('tahun_akademik_id')
                        ->label('Tahun Akademik')
                        ->options(TahunAkademik::all()->pluck('nama', 'id'))
                        ->default(TahunAkademik::where('aktif', true)->first()?->id)
                        ->required(),
                    
                    Select::make('bulan')
                        ->label('Bulan')
                        ->options([
                            'semua' => 'Semua Bulan',
                            'Januari' => 'Januari',
                            'Februari' => 'Februari',
                            'Maret' => 'Maret',
                            'April' => 'April',
                            'Mei' => 'Mei',
                            'Juni' => 'Juni',
                            'Juli' => 'Juli',
                            'Agustus' => 'Agustus',
                            'September' => 'September',
                            'Oktober' => 'Oktober',
                            'November' => 'November',
                            'Desember' => 'Desember',
                        ])
                        ->default('semua')
                        ->required(),
                    
                    Select::make('kelas_id')
                        ->label('Kelas')
                        ->options(['semua' => 'Semua Kelas'] + Kelas::all()->pluck('nama', 'id')->toArray())
                        ->default('semua')
                        ->required(),
                ])
                ->action(function (array $data) {
                    // Buat URL untuk laporan
                    $url = route('laporan.pembayaran.cetak', [
                        'tahun_akademik_id' => $data['tahun_akademik_id'],
                        'bulan' => $data['bulan'],
                        'kelas_id' => $data['kelas_id'],
                    ]);
                    
                    // Buka URL di tab baru menggunakan JavaScript
                    $this->js("window.open('$url', '_blank')");
                })
                ->modalHeading('Filter Laporan Pembayaran')
                ->modalSubmitActionLabel('Cetak PDF')
                ->modalWidth('md');
        }

        // Button Generate Tagihan Otomatis hanya untuk role keuangan
        if ($user && $user->isKeuangan()) {
            $actions[] = Action::make('generate_tagihan_otomatis')
                ->label('Generate Tagihan Otomatis')
                ->icon('heroicon-o-bolt')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Generate Tagihan SPP Otomatis')
                ->modalDescription('Sistem akan membuat tagihan SPP untuk semua siswa berdasarkan periode tahun akademik aktif dengan jumlah Rp 125.000 per bulan. Tagihan yang sudah ada tidak akan dibuat ulang.')
                ->modalSubmitActionLabel('Ya, Generate Sekarang')
                ->action(function () {
                    try {
                        // Jalankan command generate tagihan
                        Artisan::call('spp:generate-tagihan');
                        $output = Artisan::output();

                        Notification::make()
                            ->title('Generate Tagihan Berhasil')
                            ->body('Tagihan SPP telah berhasil dibuat berdasarkan periode tahun akademik aktif.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Generate Tagihan Gagal')
                            ->body('Terjadi kesalahan: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                });
        }

        return $actions;
    }
}
