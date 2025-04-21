<?php

namespace App\Filament\Resources\ReportResource\Widgets;

use Carbon\Carbon;
use App\Models\sale;
use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class FinancialSummaryWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null; // Disable auto refresh
    
    protected function getStats(): array
    {
        // Get today's data
        $today = Carbon::today();
        $todayIncome = sale::whereDate('created_at', $today)->sum('total_amount');
        $todayExpense = Expense::whereDate('created_at', $today)->sum('total_nominal');
        $todayProfit = $todayIncome - $todayExpense;
        
        // Get this month's data
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $monthIncome = sale::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');
        $monthExpense = Expense::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_nominal');
        $monthProfit = $monthIncome - $monthExpense;
        
        // Get this year's data 
        $yearStart = Carbon::now()->startOfYear();
        $yearEnd = Carbon::now()->endOfYear();
        $yearIncome = sale::whereBetween('created_at', [$yearStart, $yearEnd])->sum('total_amount');
        $yearExpense = Expense::whereBetween('created_at', [$yearStart, $yearEnd])->sum('total_nominal');
        $yearProfit = $yearIncome - $yearExpense;
        
        $profitColor = function ($value) {
            return $value >= 0 ? 'success' : 'danger';
        };
        
        return [
            Stat::make('Today\'s Income', number_format($todayIncome, 2))
                ->description('Today\'s Profit: ' . number_format($todayProfit, 2))
                ->descriptionIcon($todayProfit >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($profitColor($todayProfit))
                ->chart([
                    $todayIncome / 100,
                    $todayExpense / 100,
                ]),
                
            Stat::make('This Month\'s Income', number_format($monthIncome, 2))
                ->description('Month\'s Profit: ' . number_format($monthProfit, 2))
                ->descriptionIcon($monthProfit >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($profitColor($monthProfit))
                ->chart([
                    $monthIncome / 1000,
                    $monthExpense / 1000,
                ]),
                
            Stat::make('This Year\'s Income', number_format($yearIncome, 2))
                ->description('Year\'s Profit: ' . number_format($yearProfit, 2))
                ->descriptionIcon($yearProfit >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($profitColor($yearProfit))
                ->chart([
                    $yearIncome / 10000,
                    $yearExpense / 10000,
                ]),
        ];
    }
}
