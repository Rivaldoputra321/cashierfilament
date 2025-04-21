<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Models\Sale;
use App\Models\Report;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ReportResource;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;
    protected ?string $heading = 'Laporan Keuntungan';

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTableRecordsPerPage(): ?int
    {
        return 30;
    }

    protected function getTableQuery(): Builder
    {
        // Override untuk mencegah query bawaan
        return sale::query()->whereNull('id');
    }

    public function getTableRecordKey(Model $record): string
    {
        // Pastikan menghasilkan kunci unik untuk setiap baris
        return $record->identifier ?? $record->date;
    }

    public function getTableRecords(): EloquentCollection
{
    $fromDate = $this->getTableFiltersForm()->getRawState()['date_range']['from'] ?? null;
    $untilDate = $this->getTableFiltersForm()->getRawState()['date_range']['until'] ?? null;

    // Default ke bulan ini jika tidak ada filter yang dipilih
    if (!$fromDate && !$untilDate) {
        $fromDate = now()->startOfMonth()->format('Y-m-d');
        $untilDate = now()->endOfMonth()->format('Y-m-d');
    }

    // Query untuk grup berdasarkan tanggal
    $salesData = sale::select(
        DB::raw('DATE(created_at) as date'),
        DB::raw('SUM(total_amount - discount_total) as total_sales')
    )
        ->when($fromDate, fn ($q) => $q->whereDate('created_at', '>=', $fromDate))
        ->when($untilDate, fn ($q) => $q->whereDate('created_at', '<=', $untilDate))
        ->groupBy(DB::raw('DATE(created_at)'))
        ->get()
        ->keyBy('date');

    $expensesData = Expense::select(
        DB::raw('DATE(created_at) as date'),
        DB::raw('SUM(total_nominal) as total_expenses')
    )
        ->when($fromDate, fn ($q) => $q->whereDate('created_at', '>=', $fromDate))
        ->when($untilDate, fn ($q) => $q->whereDate('created_at', '<=', $untilDate))
        ->groupBy(DB::raw('DATE(created_at)'))
        ->get()
        ->keyBy('date');

    // Gabungkan data dan hitung profit
    $result = [];
    $allDates = $salesData->keys()->merge($expensesData->keys())->unique();

    foreach ($allDates as $date) {
        $totalSales = $salesData->get($date)->total_sales ?? 0;
        $totalExpenses = $expensesData->get($date)->total_expenses ?? 0;
        $profit = $totalSales - $totalExpenses;

        $reportItem = new Report();
        $reportItem->date = $date;
        $reportItem->total_sales = $totalSales;
        $reportItem->total_expenses = $totalExpenses;
        $reportItem->profit = $profit;
        $reportItem->identifier = $date; // Kunci unik untuk baris ini
        $result[] = $reportItem;
    }

    // Tambahkan total di akhir
    $totalItem = new Report();
    $totalItem->date = null; // Set to null or a non-date value
    $totalItem->total_sales = $salesData->sum('total_sales');
    $totalItem->total_expenses = $expensesData->sum('total_expenses');
    $totalItem->profit = $salesData->sum('total_sales') - $expensesData->sum('total_expenses');
    $totalItem->identifier = 'total_row'; // Kunci unik untuk baris total
    $result[] = $totalItem;

    // Convert to eloquent collection
    return EloquentCollection::make($result);
}

}
