<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran SPP</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.4;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .header h2 {
            margin: 3px 0;
            font-size: 14px;
            font-weight: bold;
        }

        .header p {
            margin: 1px 0;
            font-size: 10px;
        }

        .kuitansi-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            align-items: flex-start;
        }

        .kuitansi-info div {
            width: 48%;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        .detail-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            width: 25%;
        }

        .amount {
            font-size: 12px;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            text-align: center;
            width: 180px;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 50px;
            color: rgba(0, 0, 0, 0.08);
            z-index: -1;
            font-weight: bold;
        }

        .notes {
            margin-top: 20px;
            font-size: 9px;
            color: #666;
        }

        .notes ul {
            margin: 3px 0;
            padding-left: 15px;
        }

        .notes li {
            margin-bottom: 2px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="watermark">MA COKROAMINOTO KARANGKOBAR</div>

        <div class="header">
            <h1>MA COKROAMINOTO KARANGKOBAR</h1>
            <h2>KUITANSI PEMBAYARAN SPP</h2>
            <p>Alamat: Jl. Raya Karangkobar No. 123, Karangkobar, Banjarnegara</p>
            <p>Telp: (0286) 123456 | Email: info@macokroaminoto.sch.id</p>
        </div>

        <div class="kuitansi-info">
            <div>
                <table style="border: none; width: 100%;">
                    <tr style="border: none;">
                        <td style="border: none; padding: 2px 0; width: 80px;">No. Kuitansi</td>
                        <td style="border: none; padding: 2px 0; width: 10px;">:</td>
                        <td style="border: none; padding: 2px 0;"><strong>{{ $pembayaran->nomor_kuitansi }}</strong>
                        </td>

                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 2px 0;">Tanggal</td>
                        <td style="border: none; padding: 2px 0;">:</td>
                        <td style="border: none; padding: 2px 0;">
                            <strong>{{ $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') : date('d/m/Y') }}</strong>
                        </td>
                    </tr>
                </table>
            </div>
            <div>
                <table style="border: none; width: 100%;">
                    <tr style="border: none;">
                        <td style="border: none; padding: 2px 0; width: 80px;">Dicetak</td>
                        <td style="border: none; padding: 2px 0; width: 10px;">:</td>
                        <td style="border: none; padding: 2px 0;"><strong>{{ $tanggal_cetak }}</strong></td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 2px 0; width: 80px;">Status</td>
                        <td style="border: none; padding: 2px 0; width: 10px;">:</td>
                        <td style="border: none; padding: 2px 0;">
                            <strong style="color: {{ $pembayaran->status == 'lunas' ? 'green' : 'red' }};">
                                {{ strtoupper(str_replace('_', ' ', $pembayaran->status)) }}
                            </strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <table class="detail-table">
            <tr>
                <th>Nama Siswa</th>
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
                <td>{{ $pembayaran->bulan }} {{ $pembayaran->tahunAkademik->nama }}</td>
            </tr>
            <tr>
                <th>Jumlah Pembayaran</th>
                <td class="amount">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Terbilang</th>
                <td><em>{{ $terbilang }} Rupiah</em></td>
            </tr>
        </table>

        <div class="footer">
            {{-- <div class="signature">
                <div>Siswa/Wali Murid</div>
                <div class="signature-line">{{ $pembayaran->siswa->nama }}</div>
            </div> --}}
            <div class="signature">
                <div>Petugas Keuangan</div>
                <div class="signature-line">(.............................)</div>
            </div>
        </div>

        <div class="notes">
            <p><strong>Catatan:</strong></p>
            <ul>
                <li>Kuitansi ini adalah bukti sah pembayaran SPP</li>
                <li>Harap simpan kuitansi ini dengan baik</li>
                <li>Untuk informasi lebih lanjut hubungi bagian keuangan sekolah</li>
            </ul>
        </div>
    </div>
</body>

</html>
