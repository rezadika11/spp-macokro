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
        
        $pdf = Pdf::loadView('kuitansi.template', compact('pembayaran'));
        
        return $pdf->stream("Kuitansi-{$pembayaran->nomor_kuitansi}.pdf");
    }
}