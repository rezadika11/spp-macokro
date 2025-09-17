<?php

namespace App\Filament\Resources\Pembayarans\Schemas;

use App\Models\Siswa;
use App\Models\TahunAkademik;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PembayaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Form Pembayaran')
                    ->schema([
                        Select::make('siswa_id')
                            ->label('Siswa')
                            ->relationship('siswa', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('tahun_akademik_id')
                            ->label('Tahun Akademik')
                            ->relationship('tahunAkademik', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('bulan')
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
                            ])
                            ->required(),

                        TextInput::make('jumlah')
                            ->label('Jumlah Pembayaran')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('100000'),

                        Select::make('status')
                            ->label('Status Pembayaran')
                            ->options([
                                'belum_bayar' => 'Belum Bayar',
                                'lunas' => 'Lunas',
                                'terlambat' => 'Terlambat'
                            ])
                            ->default('belum_bayar')
                            ->required(),

                        DatePicker::make('tanggal_bayar')
                            ->label('Tanggal Bayar')
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        TextInput::make('nomor_kuitansi')
                            ->label('Nomor Kuitansi')
                            ->placeholder('Otomatis: KWT-202501XXXX')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Nomor kuitansi akan dibuat otomatis saat pembayaran disimpan'),
                    ])
                    ->columns(2) // form di dalam card dibagi 2 kolom
                    ->columnSpanFull(), // card/section tetap span 12 (full width)
            ]);
    }
}
