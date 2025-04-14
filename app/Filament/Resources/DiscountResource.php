<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\product;
use App\Models\category;
use App\Models\Discount;
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
    protected static ?string $model = Discount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->required()
                ->maxLength(255),
                Select::make('type')
                    ->required()
                    ->options([
                        'product' => 'Product',
                        'category' => 'Category',
                    ])
                    ->reactive(),
                TextInput::make('discount_percentage')
                    ->numeric()
                    ->required(),
                TextInput::make('min_quantity')
                    ->numeric()
                    ->nullable(),
                Toggle::make('is_member_only')
                    ->label('Hanya untuk member?'),
        
                DatePicker::make('start_date')->required(),
                DatePicker::make('end_date')->required(),
               // Produk
            Select::make('target_ids')
                ->label('Target Produk')
                ->multiple()
                ->hidden(fn ($get) => $get('type') !== 'product')
                ->options(product::all()->pluck('name', 'id'))
                ->afterStateHydrated(function ($component, $state, $record) {
                    if ($record && $record->type === 'product') {
                        $component->state(
                            $record->discountTargets->where('targetable_type', product::class)->pluck('targetable_id')->toArray()
                        );
                    }
              })
                ->dehydrated(false),

            // Kategori
            Select::make('target_ids')
                ->label('Target Kategori')
                ->multiple()
                ->hidden(fn ($get) => $get('type') !== 'category')
                ->options(category::all()->pluck('name', 'id'))
                ->afterStateHydrated(function ($component, $state, $record) {
                    if ($record && $record->type === 'category') {
                        $component->state(
                            $record->discountTargets->where('targetable_type', category::class)->pluck('targetable_id')->toArray()
                        );
                    }
                })
                ->dehydrated(false),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kd_discount')
                    ->label('Kode Diskon')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tipe Diskon')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('discount_percentage')
                    ->label('Persentase Diskon')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('min_quantity')
                    ->label('Jumlah Minimal Pembelian')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('end_date')
                    ->label('Tanggal Berakhir')
                    ->date()
                    ->sortable()
                    ->searchable(),
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

    protected function handleRecordCreation(array $data): Discount
    {
        $targets = $data['target_ids'] ?? [];
        unset($data['target_ids']);

        $discount = Discount::create($data);

        foreach ($targets as $targetId) {
            $discount->discountTargets()->create([
                'targetable_id' => $targetId,
                'targetable_type' => $data['type'] === 'product'
                    ? product::class
                    : category::class,
            ]);
        }

        return $discount;
    }

    protected function handleRecordUpdate(Discount $record, array $data): Discount
    {
        $targets = $data['target_ids'] ?? [];
        unset($data['target_ids']);

        $record->update($data);

        $record->discountTargets()->delete();
        foreach ($targets as $targetId) {
            $record->discountTargets()->create([
                'targetable_id' => $targetId,
                'targetable_type' => $data['type'] === 'product'
                    ? product::class
                    : category::class,
            ]);
        }

        return $record;
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
