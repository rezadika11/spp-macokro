<?php

namespace App\Filament\Resources\Pembayarans;

use App\Filament\Resources\Pembayarans\Pages\CreatePembayaran;
use App\Filament\Resources\Pembayarans\Pages\EditPembayaran;
use App\Filament\Resources\Pembayarans\Pages\ListPembayarans;
use App\Filament\Resources\Pembayarans\Schemas\PembayaranForm;
use App\Filament\Resources\Pembayarans\Tables\PembayaransTable;
use App\Models\Pembayaran;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = 'Pembayaran SPP';

    protected static ?string $modelLabel = 'Pembayaran SPP';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return PembayaranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PembayaransTable::configure($table);
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
        
        // Kepsek hanya bisa melihat list, tidak bisa create/edit
        if ($user && $user->isKepsek()) {
            return [
                'index' => ListPembayarans::route('/'),
            ];
        }
        
        // Keuangan memiliki akses penuh
        return [
            'index' => ListPembayarans::route('/'),
            'create' => CreatePembayaran::route('/create'),
            'edit' => EditPembayaran::route('/{record}/edit'),
        ];
    }

    /**
     * Check if user can access this resource
     */
    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && ($user->isKeuangan() || $user->isKepsek());
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
