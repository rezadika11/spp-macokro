<?php

namespace App\Filament\Resources\TahunAkademiks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Components\Component;
use Illuminate\Validation\Rule;

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
                            ->rules(function (Component $component) {
                                $recordId = $component->getLivewire()->record->id ?? null;
                                return [
                                    'required',
                                    Rule::unique('tahun_akademik', 'nama')->ignore($recordId),
                                ];
                            })
                            ->validationMessages([
                                'required' => 'Kolom Tahun Akademik wajib diisi.',
                                'unique' => 'Tahun Akademik ini sudah ada.',
                            ]),

                        DatePicker::make('mulai')
                            ->label('Mulai')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),

                        DatePicker::make('selesai')
                            ->label('Selesai')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),

                        Toggle::make('aktif')
                            ->label('Tahun Aktif')
                            ->default(false)
                            ->rule(function ($record) {
                                return function ($attribute, $value, $fail) use ($record) {
                                    if ($value) {
                                        $query = \App\Models\TahunAkademik::where('aktif', true);

                                        if ($record) {
                                            $query->where('id', '!=', $record->id);
                                        }

                                        if ($query->exists()) {
                                            $fail('Hanya boleh ada satu tahun akademik yang aktif.');
                                        }
                                    }
                                };
                            }),

                    ])

            ]);
    }
}
