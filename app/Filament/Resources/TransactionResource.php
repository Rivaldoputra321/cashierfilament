<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\sale;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Filament\Resources\TransactionResource\Pages\TransactionPage;

class TransactionResource extends Resource
{
    protected static ?string $model = sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => TransactionPage::route('/transaction-page'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
            'transaction' => TransactionPage::route('/transaction'),
        ];
    }
}
