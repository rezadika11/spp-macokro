<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran SPP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        .kuitansi-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .kuitansi-info div {
            width: 48%;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .detail-table th,
        .detail-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .detail-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .amount {
            font-size: 14px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(0, 0, 0, 0.1);
            z-index: -1;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="watermark">MA COKROAMINOTO KARANGKOBAR</div>
    
    <div class="header">
        <h1>MA COKROAMINOTO KARANGKOBAR</h1>
        <h2>KUITANSI PEMBAYARAN SPP</h2>
        <p>Alamat: Jl. Raya Karangkobar No. 123, Karangkobar, Banjarnegara</p>
        <p>Telp: (0286) 123456 | Email: info@macokroaminoto.sch.id</p>
    </div>

    <div class="kuitansi-info">
        <div>
            <strong>No. Kuitansi:</strong> {{ $pembayaran->nomor_kuitansi }}<br>
            <strong>Tanggal:</strong> {{ $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d/m/Y') : date('d/m/Y') }}<br>
            <strong>Tahun Akademik:</strong> {{ $pembayaran->tahunAkademik->tahun }}
        </div>
        <div style="text-align: right;">
            <strong>Dicetak:</strong> {{ $tanggal_cetak }}<br>
            <strong>Status:</strong> 
            <span style="color: {{ $pembayaran->status == 'lunas' ? 'green' : ($pembayaran->status == 'terlambat' ? 'orange' : 'red') }};">
                {{ strtoupper(str_replace('_', ' ', $pembayaran->status)) }}
            </span>
        </div>
    </div>

    <table class="detail-table">
        <tr>
            <th width="25%">Nama Siswa</th>
            <td>{{ $pembayaran->siswa->nama }}</td>
        </tr>
        <tr>
            <th>NIS</th>
            <td>{{ $pembayaran->siswa->nis }}</td>
        </tr>
        <tr>
            <th>Kelas</th>
            <td>{{ $pembayaran->siswa->kelas }}</td>
        </tr>
        <tr>
            <th>Bulan Pembayaran</th>
            <td>{{ $pembayaran->bulan }} {{ $pembayaran->tahunAkademik->tahun }}</td>
        </tr>
        <tr>
            <th>Jumlah Pembayaran</th>
            <td class="amount">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Terbilang</th>
            <td><em>{{ $this->terbilang($pembayaran->jumlah) }} Rupiah</em></td>
        </tr>
    </table>

    <div style="margin: 20px 0;">
        <strong>Keterangan:</strong><br>
        Pembayaran SPP bulan {{ $pembayaran->bulan }} tahun akademik {{ $pembayaran->tahunAkademik->tahun }}
        @if($pembayaran->status == 'terlambat')
            <br><span style="color: orange;"><strong>* Pembayaran terlambat</strong></span>
        @endif
    </div>

    <div class="footer">
        <div class="signature">
            <div>Siswa/Wali Murid</div>
            <div class="signature-line">{{ $pembayaran->siswa->nama }}</div>
        </div>
        <div class="signature">
            <div>Petugas Keuangan</div>
            <div class="signature-line">(.............................)</div>
        </div>
    </div>

    <div style="margin-top: 30px; font-size: 10px; color: #666;">
        <p><strong>Catatan:</strong></p>
        <ul style="margin: 5px 0; padding-left: 20px;">
            <li>Kuitansi ini adalah bukti sah pembayaran SPP</li>
            <li>Harap simpan kuitansi ini dengan baik</li>
            <li>Untuk informasi lebih lanjut hubungi bagian keuangan sekolah</li>
        </ul>
    </div>
</body>
</html>

@php
function terbilang($angka) {
    $angka = abs($angka);
    $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $terbilang = "";
    
    if ($angka < 12) {
        $terbilang = " " . $baca[$angka];
    } else if ($angka < 20) {
        $terbilang = terbilang($angka - 10) . " belas";
    } else if ($angka < 100) {
        $terbilang = terbilang($angka / 10) . " puluh" . terbilang($angka % 10);
    } else if ($angka < 200) {
        $terbilang = " seratus" . terbilang($angka - 100);
    } else if ($angka < 1000) {
        $terbilang = terbilang($angka / 100) . " ratus" . terbilang($angka % 100);
    } else if ($angka < 2000) {
        $terbilang = " seribu" . terbilang($angka - 1000);
    } else if ($angka < 1000000) {
        $terbilang = terbilang($angka / 1000) . " ribu" . terbilang($angka % 1000);
    } else if ($angka < 1000000000) {
        $terbilang = terbilang($angka / 1000000) . " juta" . terbilang($angka % 1000000);
    }
    
    return ucwords(trim($terbilang));
}
@endphp