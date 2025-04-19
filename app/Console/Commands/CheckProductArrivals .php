<?php

namespace App\Console\Commands;

use App\Models\ExpenseProduct;
use App\Models\Product;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckProductArrivals extends Command
{
    protected $signature = 'products:check-arrivals';
    protected $description = 'Check for product arrivals and update inventory';

    public function handle()
    {
        // Get all products scheduled to arrive today
        $arrivingToday = ExpenseProduct::where('arrival_date', now()->toDateString())
            ->where('processed', false) // You'll need to add this column
            ->get();

        foreach ($arrivingToday as $expenseProduct) {
            // Update product stock
            DB::transaction(function () use ($expenseProduct) {
                $product = $expenseProduct->product;
                $product->stok += $expenseProduct->quantity;
                $product->save();

                // Mark as processed
                $expenseProduct->processed = true;
                $expenseProduct->save();
            });

            // Create notification (for Filament admin panel)
            Notification::make()
                ->title('Product Arrived')
                ->body("The restock of {$expenseProduct->quantity} units of {$expenseProduct->product->name} has arrived!")
                ->success()
                ->sendToDatabase(auth()->user());
        }

        $this->info("Processed {$arrivingToday->count()} product arrivals");
    }
}