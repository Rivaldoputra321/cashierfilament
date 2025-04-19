<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Expense;
use App\Models\product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Supplier;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ExpenseResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ExpenseResource\RelationManagers;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Expense Details')
                ->schema([
                    Forms\Components\TextInput::make('description')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('type')
                        ->options([
                            'restock' => 'Restock',
                            'operational' => 'Operational',
                            'salaries' => 'Salaries',
                            'other' => 'Other',
                        ])
                        ->required()
                        ->live(),

                    Forms\Components\Select::make('supplier_id')
                        ->label('Supplier')
                        ->options(fn () => Supplier::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->required(fn (Get $get) => $get('type') === 'restock')
                        ->visible(fn (Get $get) => $get('type') === 'restock'),

                    Forms\Components\TextInput::make('total_nominal')
                        ->required()
                        ->numeric()
                        ->prefix('Rp')
                        ->disabled(fn (Get $get) => $get('type') === 'restock')
                        ->dehydrated(),
                ])
                ->columnSpan(['lg' => 1]),

                // Restock products section - Only visible for restock type
                Forms\Components\Section::make('Products')
                    ->schema([
                        Forms\Components\Repeater::make('expenseProducts')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->options(fn () => Product::pluck('name', 'id')->toArray())
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        if ($state) {
                                            $product = Product::find($state);
                                            $set('price_per_unit', $product->harga);
                                        }
                                    }),

                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        $price = floatval($get('price_per_unit'));
                                        $quantity = intval($get('quantity'));
                                        $set('subtotal', $price * $quantity);
                                    }),

                                Forms\Components\TextInput::make('price_per_unit')
                                    ->label('Price Per Unit')
                                    ->prefix('Rp')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        $price = floatval($get('price_per_unit'));
                                        $quantity = intval($get('quantity'));
                                        $set('subtotal', $price * $quantity);
                                    }),

                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated()
                                    ->reactive(),

                                Forms\Components\DatePicker::make('arrival_date')
                                    ->label('Expected Arrival Date')
                                    ->required()
                                    ->minDate(now()),
                            ])
                            ->defaultItems(1)
                            ->reorderable(false)
                            ->columns(2)
                            ->columnSpanFull()
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                // Recalculate total whenever the repeater changes
                                $expenseProducts = $get('expenseProducts');
                                $total = 0;
                                
                                if (is_array($expenseProducts)) {
                                    foreach ($expenseProducts as $product) {
                                        if (isset($product['subtotal'])) {
                                            $total += floatval($product['subtotal']);
                                        }
                                    }
                                }
                                
                                $set('total_nominal', $total);
                            }),
                            
                        // Add a summary component to display the total
                        Forms\Components\Placeholder::make('calculated_total')
                            ->label('Total Amount')
                            ->content(function (Get $get): string {
                                $total = $get('total_nominal') ?: 0;
                                return 'Rp ' . number_format($total, 2);
                            })
                            ->visible(fn (Get $get) => $get('type') === 'restock'),
                    ])
                    ->visible(fn (Get $get) => $get('type') === 'restock')
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                ->searchable(),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'restock',
                        'success' => 'operational',
                        'warning' => 'salaries',
                        'danger' => 'other',
                    ]),
                    
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('total_nominal')
                    ->money('IDR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'restock' => 'Restock',
                        'operational' => 'Operational',
                        'salaries' => 'Salaries',
                        'other' => 'Other',
                    ]),
            ])
                ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
