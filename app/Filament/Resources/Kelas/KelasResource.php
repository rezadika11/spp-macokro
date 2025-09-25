<?php

namespace App\Filament\Resources\Kelas;

use App\Filament\Resources\Kelas\Pages\CreateKelas;
use App\Filament\Resources\Kelas\Pages\EditKelas;
use App\Filament\Resources\Kelas\Pages\ListKelas;
use App\Filament\Resources\Kelas\Schemas\KelasForm;
use App\Filament\Resources\Kelas\Tables\KelasTable;
use App\Models\Kelas;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Kelas';

    protected static ?string $modelLabel = 'Kelas';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return KelasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KelasTable::configure($table);
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
        
        // Kepsek tidak bisa mengakses Kelas
        if ($user && $user->isKepsek()) {
            return [];
        }
        
        // Keuangan memiliki akses penuh
        return [
            'index' => ListKelas::route('/'),
            'create' => CreateKelas::route('/create'),
            'edit' => EditKelas::route('/{record}/edit'),
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
