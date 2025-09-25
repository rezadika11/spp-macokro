<?php

namespace App\Filament\Resources\Siswas;

use App\Filament\Resources\Siswas\Pages\CreateSiswa;
use App\Filament\Resources\Siswas\Pages\EditSiswa;
use App\Filament\Resources\Siswas\Pages\ListSiswas;
use App\Filament\Resources\Siswas\Schemas\SiswaForm;
use App\Filament\Resources\Siswas\Tables\SiswasTable;
use App\Models\Siswa;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Siswa';

    protected static ?string $modelLabel = 'Siswa';

    protected static ?int $navigationSort = 3;


    public static function form(Schema $schema): Schema
    {
        return SiswaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiswasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        $user = Auth::user();
        
        // Kepsek tidak bisa mengakses Siswa
        if ($user && $user->isKepsek()) {
            return [];
        }
        
        // Keuangan memiliki akses penuh
        return [
            'index' => ListSiswas::route('/'),
            'create' => CreateSiswa::route('/create'),
            'edit' => EditSiswa::route('/{record}/edit'),
        ];
    }

    /**
     * Check if user can access this resource
     */
    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->isKeuangan();
    }

    /**
     * Check if user can create records
     */
    public static function canCreate(): bool
    {
        $user = Auth::user();
        return $user && $user->isKeuangan();
    }

    /**
     * Check if user can edit records
     */
    public static function canEdit($record): bool
    {
        $user = Auth::user();
        return $user && $user->isKeuangan();
    }

    /**
     * Check if user can delete records
     */
    public static function canDelete($record): bool
    {
        $user = Auth::user();
        return $user && $user->isKeuangan();
    }
}
