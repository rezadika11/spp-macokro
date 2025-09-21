<?php

namespace App\Filament\Resources\Kelas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Components\Component;
use Illuminate\Validation\Rule;

class KelasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Form Kelas')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Kelas')
                            ->placeholder('Masukan nama kelas')
                            ->rules(function (Component $component) {
                                $recordId = $component->getLivewire()->record->id ?? null;
                                return [
                                    'required',
                                    Rule::unique('kelas', 'nama')->ignore($recordId),
                                ];
                            })
                            ->validationMessages([
                                'required' => 'Kolom Nama Kelas wajib diisi.',
                                'unique' => 'Nama kelas ini sudah ada.',
                            ])
                            ->required()
                            ->maxLength(255),
                    ])
            ]);
    }
}
