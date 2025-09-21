<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Services\FontteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        
        // Path logo untuk PDF
        $logoPath = public_path('logo.webp');

        $pdf = Pdf::loadView('kuitansi.template', compact('pembayaran', 'tanggal_cetak', 'terbilang', 'logoPath'))->setPaper('a5', 'landscape');

        // Kirim WhatsApp otomatis jika pembayaran baru lunas dan ada nomor HP
        if ($pembayaran->wasChanged('status') && $pembayaran->status === 'lunas' && $pembayaran->siswa->no_hp) {
            $this->kirimWhatsApp($pembayaran, $pdf);
        }

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

    /**
     * Kirim kuitansi via WhatsApp
     */
    private function kirimWhatsApp($pembayaran, $pdf)
    {
        try {
            $fontteService = new FontteService();
            
            // Format nomor HP (pastikan format 628xxx)
            $noHp = $pembayaran->siswa->no_hp;
            if (substr($noHp, 0, 1) === '0') {
                $noHp = '62' . substr($noHp, 1);
            } elseif (substr($noHp, 0, 2) !== '62') {
                $noHp = '62' . $noHp;
            }

            // Simpan PDF sementara untuk upload
            $fileName = "Kuitansi-{$pembayaran->nomor_kuitansi}.pdf";
            $filePath = storage_path("app/temp/{$fileName}");
            
            // Pastikan direktori temp ada
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }
            
            // Simpan PDF ke file sementara
            file_put_contents($filePath, $pdf->output());
            
            // Generate URL file yang bisa diakses
            $fileUrl = url("storage/temp/{$fileName}");
            
            // Copy file ke public storage
            $publicPath = public_path("storage/temp/{$fileName}");
            if (!file_exists(dirname($publicPath))) {
                mkdir(dirname($publicPath), 0755, true);
            }
            copy($filePath, $publicPath);

            // Kirim via WhatsApp
            $result = $fontteService->sendKuitansi($noHp, [
                'siswa' => $pembayaran->siswa,
                'bulan' => $pembayaran->bulan,
                'tahun_akademik' => $pembayaran->tahunAkademik,
                'jumlah' => $pembayaran->jumlah,
                'nomor_kuitansi' => $pembayaran->nomor_kuitansi
            ], $fileUrl);

            if ($result['success']) {
                Log::info("WhatsApp kuitansi berhasil dikirim", [
                    'pembayaran_id' => $pembayaran->id,
                    'nomor_hp' => $noHp,
                    'nomor_kuitansi' => $pembayaran->nomor_kuitansi
                ]);
            } else {
                Log::error("Gagal mengirim WhatsApp kuitansi", [
                    'pembayaran_id' => $pembayaran->id,
                    'nomor_hp' => $noHp,
                    'error' => $result['error']
                ]);
            }

            // Hapus file sementara
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Hapus file public setelah beberapa waktu (opsional)
            // Bisa dijadwalkan dengan job queue untuk cleanup

        } catch (\Exception $e) {
            Log::error("Error saat mengirim WhatsApp kuitansi", [
                'pembayaran_id' => $pembayaran->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
