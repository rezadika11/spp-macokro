<?php

namespace App\Filament\Resources\TahunAkademiks;

use App\Filament\Resources\TahunAkademiks\Pages\CreateTahunAkademik;
use App\Filament\Resources\TahunAkademiks\Pages\EditTahunAkademik;
use App\Filament\Resources\TahunAkademiks\Pages\ListTahunAkademiks;
use App\Filament\Resources\TahunAkademiks\Schemas\TahunAkademikForm;
use App\Filament\Resources\TahunAkademiks\Tables\TahunAkademiksTable;
use App\Models\TahunAkademik;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TahunAkademikResource extends Resource
{
    protected static ?string $model = TahunAkademik::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;
    protected static ?string $navigationLabel = 'Tahun Akademik';

    protected static ?string $modelLabel = 'Tahun Akademik';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return TahunAkademikForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TahunAkademiksTable::configure($table);
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
        
        // Kepsek tidak bisa mengakses Tahun Akademik
        if ($user && $user->isKepsek()) {
            return [];
        }
        
        // Keuangan memiliki akses penuh
        return [
            'index' => ListTahunAkademiks::route('/'),
            'create' => CreateTahunAkademik::route('/create'),
            'edit' => EditTahunAkademik::route('/{record}/edit'),
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
