<?php

namespace App\Filament\Widgets;

use App\Models\Siswa;
use App\Models\Pembayaran;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    /**
     * Check if widget can be displayed based on user role
     */
    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && ($user->isKeuangan() || $user->isKepsek());
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        // Hitung total siswa
        $totalSiswa = Siswa::count();

        // Hitung pembayaran untuk bulan sekarang
        $bulanSekarang = now()->format('F'); // Format: January, February, etc.
        $bulanIndonesia = [
            'January' => 'Januari',
            'February' => 'Februari', 
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
        ];
        
        $bulanIni = $bulanIndonesia[$bulanSekarang] ?? $bulanSekarang;
        
        // Hitung pembayaran lunas untuk bulan sekarang
        $pembayaranLunas = Pembayaran::where('status', 'lunas')
            ->where('bulan', $bulanIni)
            ->count();
        
        // Hitung pembayaran belum bayar untuk bulan sekarang
        $pembayaranBelumBayar = Pembayaran::where('status', 'belum_bayar')
            ->where('bulan', $bulanIni)
            ->count();

        // Hitung total pendapatan dari pembayaran lunas
        $totalPendapatan = Pembayaran::where('status', 'lunas')->sum('jumlah');

        $stats = [
            Stat::make('Total Siswa', $totalSiswa)
                ->description('Jumlah siswa terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Pembayaran Lunas', $pembayaranLunas)
                ->description('SPP ' . now()->format('F Y') . ' yang sudah dibayar')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Belum Bayar Bulan Ini', $pembayaranBelumBayar)
                ->description('SPP ' . now()->format('F Y') . ' yang belum dibayar')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger'),
        ];

        $stats[] = Stat::make('Total Pendapatan', 'Rp ' . number_format($totalPendapatan, 0, ',', '.'))
            ->description('Dari pembayaran lunas')
            ->descriptionIcon('heroicon-m-banknotes')
            ->color('success');

        return $stats;
    }
}
