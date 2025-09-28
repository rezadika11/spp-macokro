<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembayaran SPP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 10px 0;
            text-transform: uppercase;
        }

        .header p {
            font-size: 11px;
            margin: 2px 0;
            color: #333;
        }

        .laporan-info {
            margin-bottom: 20px;
        }

        .laporan-info table {
            width: 100%;
            border: none;
        }

        .laporan-info td {
            border: none;
            padding: 3px 0;
            font-size: 12px;
        }

        .laporan-info .label {
            width: 120px;
            font-weight: bold;
        }

        .laporan-info .colon {
            width: 10px;
        }

        .statistik {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border: 1px solid #ddd;
        }

        .statistik h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            text-align: center;
        }

        .statistik-grid {
            display: table;
            width: 100%;
        }

        .statistik-item {
            display: table-cell;
            text-align: center;
            padding: 5px;
            border-right: 1px solid #ddd;
        }

        .statistik-item:last-child {
            border-right: none;
        }

        .statistik-value {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
        }

        .statistik-label {
            font-size: 10px;
            color: #666;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #333;
            padding: 6px 4px;
            text-align: left;
            font-size: 10px;
        }

        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .data-table .text-center {
            text-align: center;
        }

        .data-table .text-right {
            text-align: right;
        }

        .status-lunas {
            color: #16a34a;
            font-weight: bold;
        }

        .status-belum {
            color: #dc2626;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .footer-left {
            width: 60%;
        }

        .footer-right {
            width: 35%;
            text-align: center;
        }

        .signature {
            text-align: center;
            width: 180px;
            margin-left: auto;
            margin-right: auto;
            margin-top: 20px;
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

        .logo-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: -1;
            width: 300px;
            height: auto;
        }

        .page-break {
            page-break-before: always;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Logo Watermark -->
        @if (isset($logoPath) && file_exists($logoPath))
            <img src="data:image/webp;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="Logo"
                class="logo-watermark">
        @else
            <img src="{{ asset('logo.webp') }}" alt="Logo" class="logo-watermark">
        @endif

        <!-- Text Watermark (Optional) -->
        {{-- <div class="watermark">COKROAMINOTO KARANGKOBAR</div> --}}

        <div class="header">
            <h1>MADRASAH ALIYAH COKROAMINOTO KARANGKOBAR</h1>
            <h2>LAPORAN PEMBAYARAN SPP</h2>
            <p>Alamat: Karangkobar Kidul, Karangkobar, Kec. Karangkobar, Kab. Banjarnegara, Jawa Tengah 53453</p>
            <p>Telp: (0286) 5988341</p>
        </div>

        <div class="laporan-info">
            <table>
                <tr>
                    <td class="label">Tahun Akademik</td>
                    <td class="colon">:</td>
                    <td><strong>{{ $tahunAkademik->nama }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Bulan</td>
                    <td class="colon">:</td>
                    <td><strong>{{ $bulan && $bulan !== 'semua' ? ucfirst($bulan) : 'Semua Bulan' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Kelas</td>
                    <td class="colon">:</td>
                    <td><strong>{{ $namaKelas }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Tanggal Cetak</td>
                    <td class="colon">:</td>
                    <td><strong>{{ $tanggalCetak }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="statistik">
            <h3>RINGKASAN STATISTIK</h3>
            <div class="statistik-grid">
                <div class="statistik-item">
                    <div class="statistik-value">{{ number_format($totalPembayaran) }}</div>
                    <div class="statistik-label">Total Pembayaran</div>
                </div>
                <div class="statistik-item">
                    <div class="statistik-value">{{ number_format($totalLunas) }}</div>
                    <div class="statistik-label">Sudah Lunas</div>
                </div>
                <div class="statistik-item">
                    <div class="statistik-value">{{ number_format($totalBelumBayar) }}</div>
                    <div class="statistik-label">Belum Bayar</div>
                </div>
                <div class="statistik-item">
                    <div class="statistik-value">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
                    <div class="statistik-label">Total Nominal</div>
                </div>
            </div>
        </div>

        @if ($pembayarans->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">NIS</th>
                        <th style="width: 25%;">Nama Siswa</th>
                        <th style="width: 10%;">Kelas</th>
                        <th style="width: 10%;">Bulan</th>
                        <th style="width: 15%;">Jumlah</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 10%;">Tgl Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pembayarans as $index => $pembayaran)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $pembayaran->siswa->nis ?? '-' }}</td>
                            <td>{{ $pembayaran->siswa->nama ?? '-' }}</td>
                            <td class="text-center">{{ $pembayaran->siswa->kelas->nama ?? '-' }}</td>
                            <td class="text-center">{{ ucfirst($pembayaran->bulan) }}</td>
                            <td class="text-right">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="{{ $pembayaran->status === 'lunas' ? 'status-lunas' : 'status-belum' }}">
                                    {{ $pembayaran->status === 'lunas' ? 'LUNAS' : 'BELUM BAYAR' }}
                                </span>
                            </td>
                            <td class="text-center">
                                {{ $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <p>Tidak ada data pembayaran untuk periode yang dipilih.</p>
            </div>
        @endif

        <div class="footer">
            <div class="footer-right">
                <p>{{ $lokasi }}, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
                <div class="signature">
                    <p>Petugas Keuangan</p>
                    <div class="signature-line">{{ ucwords($petugas) }}</div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
