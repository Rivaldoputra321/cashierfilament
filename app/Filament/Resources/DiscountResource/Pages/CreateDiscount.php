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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
