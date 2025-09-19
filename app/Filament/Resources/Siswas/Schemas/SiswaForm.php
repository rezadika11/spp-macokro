<?php

namespace App\Filament\Resources\Siswas\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Components\Component;
use Illuminate\Validation\Rule;

class SiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        //
                        TextInput::make('nis')
                            ->label('NIS')
                            ->placeholder('Nomor Induk Siswa')
                            ->rules(function (Component $component) {
                                $recordId = $component->getLivewire()->record->id ?? null;
                                return [
                                    'required',
                                    Rule::unique('siswa', 'nis')->ignore($recordId),
                                ];
                            })
                            ->validationMessages([
                                'required' => 'Kolom NIS wajib diisi.',
                                'unique' => 'NIS ini sudah ada.',
                            ])
                            ->required(),
                        TextInput::make('nama')
                            ->label('Nama')
                            ->placeholder('Nama Siswa')
                            ->required(),
                        TextInput::make('kelas')
                            ->label('Kelas')
                            ->placeholder('Kelas Siswa')
                            ->required(),
                        TextInput::make('no_hp')
                            ->label('No WA')
                            ->placeholder('Nomor Whatsapp Siswa')
                            ->type('number')
                            ->required(),
                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->placeholder('Alamat Siswa')
                            ->required(),
                    ]),
            ]);
    }
}
