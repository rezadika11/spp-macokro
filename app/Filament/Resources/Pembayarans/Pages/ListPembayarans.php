<?php

namespace App\Filament\Resources\Pembayarans\Pages;

use App\Filament\Resources\Pembayarans\PembayaranResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListPembayarans extends ListRecords
{
    protected static string $resource = PembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_tagihan_otomatis')
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
                }),
            CreateAction::make(),
        ];
    }
}
