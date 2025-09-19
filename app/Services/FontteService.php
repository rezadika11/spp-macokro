<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class FontteService
{
    protected $client;
    protected $apiUrl;
    protected $token;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiUrl = config('services.fontte.url', 'https://api.fonnte.com');
        $this->token = config('services.fontte.token');
    }

    /**
     * Kirim pesan WhatsApp
     *
     * @param string $target Nomor WhatsApp tujuan (format: 628xxxxxxxxx)
     * @param string $message Pesan yang akan dikirim
     * @param string|null $filename Nama file untuk attachment (optional)
     * @param string|null $url URL file untuk attachment (optional)
     * @return array
     */
    public function sendMessage($target, $message, $filename = null, $url = null)
    {
        try {
            $data = [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Indonesia
            ];

            // Jika ada attachment
            if ($filename && $url) {
                $data['url'] = $url;
                $data['filename'] = $filename;
            }

            $response = $this->client->post($this->apiUrl . '/send', [
                'headers' => [
                    'Authorization' => $this->token,
                ],
                'form_params' => $data
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Fonnte WhatsApp sent successfully', [
                'target' => $target,
                'response' => $result
            ]);

            return [
                'success' => true,
                'data' => $result
            ];

        } catch (RequestException $e) {
            Log::error('Fonnte WhatsApp send failed', [
                'target' => $target,
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Kirim kuitansi pembayaran via WhatsApp
     *
     * @param string $target Nomor WhatsApp
     * @param array $pembayaran Data pembayaran
     * @param string $kuitansiUrl URL file kuitansi PDF
     * @return array
     */
    public function sendKuitansi($target, $pembayaran, $kuitansiUrl)
    {
        $message = $this->generateKuitansiMessage($pembayaran);
        
        return $this->sendMessage(
            $target, 
            $message, 
            "Kuitansi-{$pembayaran['nomor_kuitansi']}.pdf",
            $kuitansiUrl
        );
    }

    /**
     * Generate pesan kuitansi
     *
     * @param array $pembayaran
     * @return string
     */
    private function generateKuitansiMessage($pembayaran)
    {
        return "*KUITANSI PEMBAYARAN SPP*\n\n" .
               "Yth. Orang Tua/Wali Siswa\n\n" .
               "Berikut adalah kuitansi pembayaran SPP:\n\n" .
               "📋 *Detail Pembayaran:*\n" .
               "• Nama Siswa: {$pembayaran['siswa']['nama']}\n" .
               "• NIS: {$pembayaran['siswa']['nis']}\n" .
               "• Kelas: {$pembayaran['siswa']['kelas']}\n" .
               "• Bulan: {$pembayaran['bulan']} {$pembayaran['tahun_akademik']['nama']}\n" .
               "• Jumlah: Rp " . number_format($pembayaran['jumlah'], 0, ',', '.') . "\n" .
               "• Status: *LUNAS*\n" .
               "• No. Kuitansi: {$pembayaran['nomor_kuitansi']}\n\n" .
               "✅ Pembayaran telah diterima dan dicatat dalam sistem.\n\n" .
               "Terima kasih atas pembayaran tepat waktu.\n\n" .
               "_Pesan ini dikirim otomatis oleh sistem._";
    }

    /**
     * Cek status device Fonnte
     *
     * @return array
     */
    public function checkDevice()
    {
        try {
            $response = $this->client->post($this->apiUrl . '/device', [
                'headers' => [
                    'Authorization' => $this->token,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            
            return [
                'success' => true,
                'data' => $result
            ];

        } catch (RequestException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Kirim tagihan pembayaran SPP via WhatsApp
     *
     * @param string $target Nomor WhatsApp
     * @param array $pembayaran Data pembayaran
     * @return array
     */
    public function kirimTagihan($target, $pembayaran)
    {
        $message = $this->generateTagihanMessage($pembayaran);
        
        return $this->sendMessage($target, $message);
    }

    /**
     * Kirim notifikasi pembayaran lunas via WhatsApp
     *
     * @param string $target Nomor WhatsApp
     * @param array $pembayaran Data pembayaran
     * @return array
     */
    public function kirimNotifikasiLunas($target, $pembayaran)
    {
        $message = $this->generateNotifikasiLunasMessage($pembayaran);
        
        return $this->sendMessage($target, $message);
    }

    /**
     * Generate pesan tagihan
     *
     * @param array $pembayaran
     * @return string
     */
    private function generateTagihanMessage($pembayaran)
    {
        $jatuhTempo = \Carbon\Carbon::parse($pembayaran['jatuh_tempo'])->format('d/m/Y');
        
        return "*TAGIHAN SPP*\n\n" .
               "Yth. Orang Tua/Wali Siswa\n\n" .
               "Kami informasikan bahwa terdapat tagihan SPP yang belum dibayar:\n\n" .
               "📋 *Detail Tagihan:*\n" .
               "• Nama Siswa: {$pembayaran['siswa']['nama']}\n" .
               "• NIS: {$pembayaran['siswa']['nis']}\n" .
               "• Kelas: {$pembayaran['siswa']['kelas']}\n" .
               "• Bulan: {$pembayaran['bulan']} {$pembayaran['tahun_akademik']['nama']}\n" .
               "• Jumlah: Rp " . number_format($pembayaran['jumlah'], 0, ',', '.') . "\n" .
               "• Jatuh Tempo: {$jatuhTempo}\n" .
               "• Status: *BELUM LUNAS*\n\n" .
               "⚠️ Mohon segera melakukan pembayaran sebelum jatuh tempo.\n\n" .
               "Untuk informasi lebih lanjut, silakan hubungi bagian keuangan sekolah.\n\n" .
               "_Pesan ini dikirim otomatis oleh sistem._";
    }

    /**
     * Generate pesan notifikasi lunas
     *
     * @param array $pembayaran
     * @return string
     */
    private function generateNotifikasiLunasMessage($pembayaran)
    {
        return "*PEMBAYARAN SPP LUNAS*\n\n" .
               "Yth. Orang Tua/Wali Siswa\n\n" .
               "Kami informasikan bahwa pembayaran SPP telah diterima dan dicatat:\n\n" .
               "📋 *Detail Pembayaran:*\n" .
               "• Nama Siswa: {$pembayaran['siswa']['nama']}\n" .
               "• NIS: {$pembayaran['siswa']['nis']}\n" .
               "• Kelas: {$pembayaran['siswa']['kelas']}\n" .
               "• Bulan: {$pembayaran['bulan']} {$pembayaran['tahun_akademik']['nama']}\n" .
               "• Jumlah: Rp " . number_format($pembayaran['jumlah'], 0, ',', '.') . "\n" .
               "• Status: *LUNAS*\n\n" .
               "✅ Terima kasih atas pembayaran tepat waktu.\n\n" .
               "Pembayaran telah tercatat dalam sistem sekolah.\n\n" .
               "_Pesan ini dikirim otomatis oleh sistem._";
    }
}