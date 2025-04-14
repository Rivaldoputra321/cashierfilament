<?php

namespace App\Filament\Resources\DiscountResource\Pages;

use Filament\Actions;
use App\Models\product;
use App\Models\category;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\DiscountResource;

class CreateDiscount extends CreateRecord
{
    protected static string $resource = DiscountResource::class;
    protected array $targetIds = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Simpan target produk/kategori ke properti sementara
        $this->targetIds = $data['type'] === 'product'
            ? $data['target_products'] ?? []
            : $data['target_categories'] ?? [];

        // Hapus dari form data sebelum disimpan ke table discounts
        unset($data['target_products'], $data['target_categories']);

        return $data;
    }

    protected function afterCreate(): void
    {
        foreach ($this->targetIds as $targetId) {
            $this->record->discountTargets()->create([
                'targetable_id' => $targetId,
                'targetable_type' => $this->record->type === 'product'
                    ? product::class
                    : category::class,
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
