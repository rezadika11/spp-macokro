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
                            ->disabled()
                            ->preload()
                            ->required(),

                        Select::make('tahun_akademik_id')
                            ->label('Tahun Akademik')
                            ->relationship('tahunAkademik', 'nama')
                            ->searchable()
                            ->disabled()
                            ->preload()
                            ->required(),

                        Select::make('bulan')
                            ->label('Bulan')
                            ->disabled()
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
                            ->disabled()
                            ->prefix('Rp')
                            ->placeholder('100000'),

                        Select::make('status')
                            ->label('Status Pembayaran')
                            ->options([
                                'belum_bayar' => 'Belum Bayar',
                                'lunas' => 'Lunas',
                            ])
                            ->default('belum_bayar')
                            ->required(),

                        DatePicker::make('tanggal_bayar')
                            ->label('Tanggal Bayar')
                            ->placeholder('Pilih Tanggal Bayar')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->columns(2) // form di dalam card dibagi 2 kolom
                    ->columnSpanFull(), // card/section tetap span 12 (full width)
            ]);
    }
}
