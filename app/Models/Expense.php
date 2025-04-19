<?php

namespace App\Models;

use App\Models\product;
use App\Models\Supplier;
use App\Models\ExpenseProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Expense extends Model
{
    protected $guarded = [];
    protected $casts = [
        'total_nominal' => 'decimal:2',
    ];



    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
    public function expenseProducts(): HasMany
    {
        return $this->hasMany(ExpenseProduct::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(product::class)
            ->withPivot(['quantity', 'price_per_unit', 'expired_at'])
            ->withTimestamps();
    }
}
