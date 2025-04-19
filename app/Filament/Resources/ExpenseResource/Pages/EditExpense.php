<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ExpenseResource;

class EditExpense extends EditRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // For restock type, recalculate total_nominal from products
        if ($data['type'] === 'restock' && isset($data['expenseProducts'])) {
            $total = 0;
            foreach ($data['expenseProducts'] as $product) {
                $total += floatval($product['subtotal']);
            }
            $data['total_nominal'] = $total;
        }

        return $data;
    }
    
    protected function afterSave(): void
    {
        $expense = $this->record;

        // Only process for restock expenses
        if ($expense->type === 'restock') {
            // Update arrival notifications
            foreach ($expense->expenseProducts as $expenseProduct) {
                // Here you would update any scheduled notifications
            }

            Notification::make()
                ->title('Restock order updated')
                ->body('Product arrival information has been updated')
                ->success()
                ->send();
        }
    }
}
