<?php

namespace App\Filament\Resources\TahunAkademiks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TahunAkademikForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->components([
                        TextInput::make('nama')
                            ->label('Tahun Akademik')
                            ->placeholder('Contoh: 2025/2026')
                            ->required()
                            ->validationMessages([
                                'required' => 'Kolom Tahun Akademik wajib diisi.',
                            ]),

                        DatePicker::make('mulai')
                            ->label('Tanggal Mulai')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required()
                            ->validationMessages([
                                'required' => 'Tanggal mulai wajib diisi.',
                            ]),

                        DatePicker::make('selesai')
                            ->label('Tanggal Selesai')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required()
                            ->validationMessages([
                                'required' => 'Tanggal selesai wajib diisi.',
                            ]),

                        Toggle::make('aktif')
                            ->label('Tahun Aktif'),
                    ])

            ]);
    }
}
