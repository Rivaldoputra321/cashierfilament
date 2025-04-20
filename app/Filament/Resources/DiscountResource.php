<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\product;
use App\Models\category;
use App\Models\discount;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Components\HasManyRepeater;
use App\Filament\Resources\DiscountResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DiscountResource\RelationManagers;
use App\Filament\Resources\DiscountResource\Pages\EditDiscount;
use App\Filament\Resources\DiscountResource\Pages\ListDiscounts;
use App\Filament\Resources\DiscountResource\Pages\CreateDiscount;

class DiscountResource extends Resource
{
    protected static ?string $model = discount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Discount Name')
                    ->required()
                    ->maxLength(255),

                Select::make('type')
                    ->label('Discount Type')
                    ->required()
                    ->options([
                        'product' => 'Product',
                        'category' => 'Category',
                        'supplier' => 'Supplier',
                    ])
                    ->reactive(),

                Select::make('product_id')
                    ->label('Products')
                    ->relationship('products', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->required(fn ($get) => $get('type') === 'product')
                    ->hidden(fn ($get) => $get('type') !== 'product')
                    ->nullable(),

                Select::make('category_id')
                    ->label('Categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->required(fn ($get) => $get('type') === 'category')
                    ->hidden(fn ($get) => $get('type') !== 'category')
                    ->nullable(),


                TextInput::make('discount_percentage')
                    ->label('Discount Percentage (%)')
                    ->numeric()
                    ->required(),

                TextInput::make('min_quantity')
                    ->label('Minimum Quantity')
                    ->numeric()
                    ->nullable(),

                Toggle::make('is_member_only')
                    ->label('For Member Only')
                    ->reactive(), // Ensure the toggle is reactive

                Select::make('member_tiers')
                    ->label('Member Tiers')
                    ->multiple()
                    ->default([])
                    ->reactive()
                    ->options([
                        'gold' => 'Gold',
                        'silver' => 'Silver',
                        'bronze' => 'Bronze',
                    ])
                    ->hidden(fn ($get) => !$get('is_member_only'))
                    ->nullable(),
                

                DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required()
                    ->native(false),

                DatePicker::make('end_date')
                    ->label('End Date')
                    ->required()
                    ->native(false)
                   ,
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kd_discount')
                    ->label('Code')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Discount Type')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('products.name', 'categories.name',)
                    ->label('Discount Target')
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        return match ($record->type) {
                            'product' => $record->products->pluck('name')->join(', '),
                            'category' => $record->categories->pluck('name')->join(', '),
                            default => '-',
                        };
                    }),
                
                TextColumn::make('discount_percentage')
                    ->label('Discount Percentage (%)')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('min_quantity')
                    ->label('Minimum Quantity')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('is_member_only')
                    ->label('For Member Only')
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                    ->color(fn ($state) => $state ? 'green' : 'red')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('member_tiers')
                    ->label('Tier Member')
                    ->formatStateUsing(fn ($state) => 
                        is_array($state)
                            ? implode(', ', $state)
                            : ($state ? implode(', ', explode(',', $state)) : '-')
                    ),
                

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
            'index' => Pages\ListDiscounts::route('/'),
            'create' => Pages\CreateDiscount::route('/create'),
            'edit' => Pages\EditDiscount::route('/{record}/edit'),
        ];
    }
}
