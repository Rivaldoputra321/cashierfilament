<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailSale extends Model
{
    protected $table = 'detail_sales';
    protected $primaryKey = 'id';
    protected $fillable = [
        'sales_id', 'product_id', 'quantity', 'price',
        'discount_id', 'subtotal'
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sales_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }
}
