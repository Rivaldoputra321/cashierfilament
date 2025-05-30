<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource 
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Products'; 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('kd_product')
                ->required()
                ->label('Code Product')
                ->maxLength(255)
                ->unique(ignoreRecord: true),
                Select::make('category_id')
                ->relationship('categories', 'name')
                ->required()
                ->label('Category')
                ->multiple()
                ->preload()
                ->searchable()
                ->reactive(),
                Select::make('supplier_id')
                ->relationship('suppliers', 'name')
                ->required()
                ->label('Supplier')
                ->preload()
                ->searchable()
                ->reactive(),
                TextInput::make('name')
                ->required()
                ->maxLength(255),
                TextInput::make('stok')
                ->numeric()
                ->minValue(1)
                ->label('Stock')
                ->required()
                ->maxValue(42949672.95),
                TextInput::make('harga')
                ->numeric()
                ->label('Price')
                ->prefix('Rp')
                ->suffix('IDR')
                ->required()
                ->maxValue(42949672.95),
                DatePicker::make('expired_at')
                ->required()
                ->label('Expired Date')
                ->placeholder('yyyy-mm-dd') // Sesuai format MySQL
                ->format('Y-m-d') // Gunakan format MySQL langsung
                ->minDate(now()->addDays(30))
                ->maxDate(now()->endOfYear()),
                FileUpload::make('image')
                    ->required()
                    ->image()
                    ->imageEditor(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kd_product')
                ->label('Code')
                ->searchable(),
                TextColumn::make('name')
                ->label('Name')
                ->sortable()
                ->searchable(),
                TextColumn::make('categories.name')
                ->label('Categories')
                ->listWithLineBreaks() 
                ->searchable(),
                TextColumn::make('suppliers.name')
                ->label('Suppliers')
                ->searchable(),
                TextColumn::make('stok')
                ->label('Stock')
                ->searchable(),
                TextColumn::make('harga')
                ->label('Price')
                ->money('idr')
                ->searchable(),           
                TextColumn::make('expired_at')
                ->label('Expired Date')
                ->date()
                ->searchable(),
                ImageColumn::make('image')
                ->label('Image'),
            ])
            
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
