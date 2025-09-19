<?php

namespace App\Filament\Resources\Pembayarans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use App\Services\FontteService;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class PembayaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('siswa.nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tahunAkademik.nama')
                    ->label('Tahun Akademik')
                    ->sortable(),
                TextColumn::make('bulan')
                    ->label('Bulan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'belum_bayar' => 'danger',
                        'lunas' => 'success',
                    })
                    ->formatStateUsing(fn(string $state): string => str_replace('_', ' ', ucwords($state, '_'))),
                TextColumn::make('tanggal_bayar')
                    ->label('Tanggal Bayar')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('nomor_kuitansi')
                    ->label('No. Kuitansi')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'belum_bayar' => 'Belum Bayar',
                        'lunas' => 'Lunas',
                    ]),
                SelectFilter::make('bulan')
                    ->label('Bulan')
                    ->options([
                        'Januari' => 'Januari',
                        'Februari' => 'Februari',
                        'Maret' => 'Maret',
                        'April' => 'April',
                        'Mei' => 'Mei',
                        'Juni' => 'Juni',
                        'Juli' => 'Juli',
                        'Agustus' => 'Agustus',
                        'September' => 'September',
                        'Oktober' => 'Oktober',
                        'November' => 'November',
                        'Desember' => 'Desember',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn($record) => $record->status !== 'lunas'),
                Action::make('kirim_tagihan')
                    ->label('Kirim Tagihan')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->color('warning')
                    ->url(function ($record) {
                        $namaSiswa = $record->siswa->nama;
                        $bulan = $record->bulan;
                        $jumlah = number_format($record->jumlah, 0, ',', '.');
                        $pesan = "Assalamualaikum Wr. Wb.\n\nYth. Bapak/Ibu Wali dari ananda {$namaSiswa}, kami ingin memberitahukan bahwa ada tagihan SPP untuk bulan {$bulan} sebesar Rp {$jumlah} yang perlu dibayarkan.\n\nTerima kasih.\nBendahara MA Cokroaminoto Karangkobar.";
                        return 'https://wa.me/' . $record->siswa->no_hp . '?text=' . urlencode($pesan);
                    })
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->status === 'belum_bayar' && $record->siswa->no_hp),

                Action::make('kirim_notifikasi_lunas')
                    ->label('Kirim Notif Lunas')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('info')
                    ->url(function ($record) {
                        $namaSiswa = $record->siswa->nama;
                        $bulan = $record->bulan;
                        $jumlah = number_format($record->jumlah, 0, ',', '.');
                        $tanggalBayar = $record->tanggal_bayar ? \Carbon\Carbon::parse($record->tanggal_bayar)->format('d-m-Y') : date('d-m-Y');
                        $pesan = "Alhamdulillah.\n\nYth. Bapak/Ibu Wali dari ananda {$namaSiswa}, kami memberitahukan bahwa pembayaran SPP untuk bulan {$bulan} sebesar Rp {$jumlah} telah kami terima pada tanggal {$tanggalBayar}.\n\nTerima kasih atas pembayaran tepat waktu Anda.\nBendahara MA Cokroaminoto Karangkobar.";
                        return 'https://wa.me/' . $record->siswa->no_hp . '?text=' . urlencode($pesan);
                    })
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->status === 'lunas' && $record->siswa->no_hp),

                Action::make('cetak_kuitansi')
                    ->label('Cetak Kuitansi')
                    ->icon(Heroicon::OutlinedPrinter)
                    ->color('success')
                    ->url(fn($record) => route('kuitansi.cetak', $record->id))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->status === 'lunas'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
