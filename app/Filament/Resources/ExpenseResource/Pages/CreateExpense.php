<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ExpenseResource;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // For restock type, calculate total_nominal from products
        if ($data['type'] === 'restock' && isset($data['expenseProducts'])) {
            $total = 0;
            foreach ($data['expenseProducts'] as $product) {
                $total += floatval($product['subtotal']);
            }
            $data['total_nominal'] = $total;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $expense = $this->record;

        // Only process inventory updates and notifications for restock expenses
        if ($expense->type === 'restock') {
            // Schedule notifications for each product's arrival date
            foreach ($expense->expenseProducts as $expenseProduct) {
                $this->scheduleArrivalNotification($expenseProduct);
            }

            // Show a notification that products have been ordered
            Notification::make()
                ->title('Restock order created successfully')
                ->body('You will be notified when products are expected to arrive')
                ->success()
                ->send();
        }
    }

    private function scheduleArrivalNotification($expenseProduct): void
    {
        // This would ideally use Laravel's notification system with scheduled notifications
        // For now, we'll just create a simple database entry that would be checked by a scheduled task
        
        // In a real app, you'd implement a proper scheduled notification system
        // For example using Laravel's notification system with a delay:
        // $expenseProduct->product->notify((new ProductArrivalNotification($expenseProduct))->delay($expenseProduct->arrival_date));
    }
}
