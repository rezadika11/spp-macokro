<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KuitansiController extends Controller
{
    public function cetak($id)
    {
        $pembayaran = Pembayaran::with(['siswa', 'tahunAkademik'])->findOrFail($id);

        // Generate nomor kuitansi jika belum ada
        if (!$pembayaran->nomor_kuitansi) {
            $pembayaran->generateNomorKuitansi();
        }

        // Update status menjadi lunas jika belum
        if ($pembayaran->status !== 'lunas') {
            $pembayaran->update([
                'status' => 'lunas',
                'tanggal_bayar' => now()
            ]);
        }

        // Definisikan tanggal cetak sebelum membuat PDF
        $tanggal_cetak = now()->format('d/m/Y H:i');
        
        // Generate terbilang untuk jumlah pembayaran
        $terbilang = $this->terbilang($pembayaran->jumlah);

        $pdf = Pdf::loadView('kuitansi.template', compact('pembayaran', 'tanggal_cetak', 'terbilang'))->setPaper('a4', 'landscape');

        return $pdf->stream("Kuitansi-{$pembayaran->nomor_kuitansi}.pdf");
    }

    private function terbilang($angka)
    {
        $angka = abs($angka);
        $baca = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas',
        ];
        $terbilang = '';

        if ($angka < 12) {
            $terbilang = $baca[$angka];
        } elseif ($angka < 20) {
            $terbilang = $this->terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            $terbilang = $this->terbilang($angka / 10) . ' puluh' . (($angka % 10) != 0 ? ' ' . $this->terbilang($angka % 10) : '');
        } elseif ($angka < 200) {
            $terbilang = 'seratus' . (($angka - 100) != 0 ? ' ' . $this->terbilang($angka - 100) : '');
        } elseif ($angka < 1000) {
            $terbilang = $this->terbilang($angka / 100) . ' ratus' . (($angka % 100) != 0 ? ' ' . $this->terbilang($angka % 100) : '');
        } elseif ($angka < 2000) {
            $terbilang = 'seribu' . (($angka - 1000) != 0 ? ' ' . $this->terbilang($angka - 1000) : '');
        } elseif ($angka < 1000000) {
            $terbilang = $this->terbilang($angka / 1000) . ' ribu' . (($angka % 1000) != 0 ? ' ' . $this->terbilang($angka % 1000) : '');
        } elseif ($angka < 1000000000) {
            $terbilang = $this->terbilang($angka / 1000000) . ' juta' . (($angka % 1000000) != 0 ? ' ' . $this->terbilang($angka % 1000000) : '');
        }

        return ucwords(trim($terbilang));
    }
}
