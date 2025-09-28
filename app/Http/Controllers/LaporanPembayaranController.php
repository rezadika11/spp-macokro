<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanPembayaranController extends Controller
{
    public function cetakLaporan(Request $request)
    {
        // Validasi input dari query parameters
        $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'bulan' => 'nullable|string',
            'kelas_id' => 'nullable|string',
        ]);

        $tahunAkademikId = $request->get('tahun_akademik_id');
        $bulan = $request->get('bulan');
        $kelasId = $request->get('kelas_id');

        // Ambil data tahun akademik
        $tahunAkademik = TahunAkademik::findOrFail($tahunAkademikId);

        // Query pembayaran
        $query = Pembayaran::with(['siswa.kelas', 'tahunAkademik'])
            ->where('tahun_akademik_id', $tahunAkademikId);

        // Filter berdasarkan bulan jika dipilih
        if ($bulan && $bulan !== 'semua') {
            $query->where('bulan', $bulan);
        }

        // Filter berdasarkan kelas jika dipilih
        if ($kelasId && $kelasId !== 'semua') {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $pembayarans = $query->orderBy('siswa_id')
            ->orderBy('bulan')
            ->get();

        // Hitung statistik
        $totalPembayaran = $pembayarans->count();
        $totalLunas = $pembayarans->where('status', 'lunas')->count();
        $totalBelumBayar = $pembayarans->where('status', 'belum_bayar')->count();
        $totalNominal = $pembayarans->where('status', 'lunas')->sum('jumlah');

        // Ambil nama kelas jika dipilih
        $namaKelas = 'Semua Kelas';
        if ($kelasId && $kelasId !== 'semua') {
            $kelas = \App\Models\Kelas::find($kelasId);
            $namaKelas = $kelas ? $kelas->nama : 'Kelas Tidak Ditemukan';
        }

        // Data untuk PDF
        $data = [
            'pembayarans' => $pembayarans,
            'tahunAkademik' => $tahunAkademik,
            'bulan' => $bulan,
            'kelasId' => $kelasId,
            'namaKelas' => $namaKelas,
            'totalPembayaran' => $totalPembayaran,
            'totalLunas' => $totalLunas,
            'totalBelumBayar' => $totalBelumBayar,
            'totalNominal' => $totalNominal,
            'tanggalCetak' => Carbon::now()->format('d/m/Y'),
            'logoPath' => public_path('logo.webp'),
            'petugas' => auth()->user()->name ?? 'Petugas Keuangan',
            'lokasi' => 'Karangkobar',
        ];

        // Generate PDF
        $pdf = Pdf::loadView('laporan.pembayaran', $data);
        $pdf->setPaper('A4', 'portrait');

        // Nama file - bersihkan karakter yang tidak diizinkan
        $tahunAkademikClean = preg_replace('/[\/\\\\:*?"<>|]/', '_', $tahunAkademik->nama);
        $namaFile = 'Laporan_Pembayaran_SPP_' . $tahunAkademikClean;
        if ($bulan && $bulan !== 'semua') {
            $bulanClean = preg_replace('/[\/\\\\:*?"<>|]/', '_', $bulan);
            $namaFile .= '_' . $bulanClean;
        }
        if ($kelasId && $kelasId !== 'semua') {
            $kelasClean = preg_replace('/[\/\\\\:*?"<>|]/', '_', str_replace(' ', '_', $namaKelas));
            $namaFile .= '_' . $kelasClean;
        }
        $namaFile .= '_' . Carbon::now()->format('Y-m-d') . '.pdf';

        return $pdf->stream($namaFile);
    }
}
