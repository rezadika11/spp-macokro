<?php

namespace App\Console\Commands;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateTagihanSpp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spp:generate-tagihan {--siswa-id= : Generate tagihan untuk siswa tertentu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate tagihan SPP otomatis berdasarkan periode tahun akademik aktif';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai generate tagihan SPP...');

        // Ambil tahun akademik yang aktif
        $tahunAkademik = TahunAkademik::where('aktif', true)->first();
        
        if (!$tahunAkademik) {
            $this->error('Tidak ada tahun akademik yang aktif!');
            return 1;
        }

        $this->info("Tahun Akademik Aktif: {$tahunAkademik->nama}");
        $this->info("Periode: {$tahunAkademik->mulai} s/d {$tahunAkademik->selesai}");

        // Tentukan siswa yang akan diproses
        $siswaId = $this->option('siswa-id');
        if ($siswaId) {
            $siswaList = Siswa::where('id', $siswaId)->get();
            if ($siswaList->isEmpty()) {
                $this->error("Siswa dengan ID {$siswaId} tidak ditemukan!");
                return 1;
            }
        } else {
            $siswaList = Siswa::all();
        }

        $this->info("Memproses {$siswaList->count()} siswa...");

        $totalGenerated = 0;
        $totalSkipped = 0;

        foreach ($siswaList as $siswa) {
            $this->line("Memproses siswa: {$siswa->nama} (NIS: {$siswa->nis})");
            
            $generated = $this->generateTagihanForSiswa($siswa, $tahunAkademik);
            $totalGenerated += $generated['created'];
            $totalSkipped += $generated['skipped'];
        }

        $this->info("=== HASIL GENERATE TAGIHAN ===");
        $this->info("Total tagihan dibuat: {$totalGenerated}");
        $this->info("Total tagihan dilewati (sudah ada): {$totalSkipped}");
        $this->info("Proses selesai!");

        return 0;
    }

    private function generateTagihanForSiswa(Siswa $siswa, TahunAkademik $tahunAkademik)
    {
        $start = Carbon::parse($tahunAkademik->mulai)->startOfMonth();
        $end = Carbon::parse($tahunAkademik->selesai)->endOfMonth();
        
        $created = 0;
        $skipped = 0;

        while ($start <= $end) {
            $bulanNama = $start->translatedFormat('F');
            
            // Cek apakah tagihan untuk bulan ini sudah ada
            $existingTagihan = Pembayaran::where('siswa_id', $siswa->id)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->where('bulan', $bulanNama)
                ->first();

            if ($existingTagihan) {
                $this->line("  - {$bulanNama}: sudah ada");
                $skipped++;
            } else {
                Pembayaran::create([
                    'siswa_id' => $siswa->id,
                    'tahun_akademik_id' => $tahunAkademik->id,
                    'bulan' => $bulanNama,
                    'jumlah' => 125000,
                    'status' => 'belum_bayar',
                ]);
                
                $this->line("  - {$bulanNama}: dibuat (Rp 125.000)");
                $created++;
            }

            $start->addMonth();
        }

        return ['created' => $created, 'skipped' => $skipped];
    }
}
