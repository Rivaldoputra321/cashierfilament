<?php

namespace App\Models;

use App\Models\Expense;
use App\Models\category;
use App\Models\discount;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class product extends Model
{
    protected $guarded = [];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(category::class, 'category_products',
          'category_id', 'product_id');
    }

    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(discount::class, 'discount_products',
            'discount_id', 'product_id');
    }

    public function suppliers(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function expenseProducts(): HasMany
    {
        return $this->hasMany(ExpenseProduct::class);
    }

}
