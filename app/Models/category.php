<?php

namespace App\Models;

use App\Models\product;
use App\Models\discount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class category extends Model
{
    protected $guarded = [];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(product::class, 'category_products',
        'category_id', 'product_id');
    }

    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(discount::class, 'discount_categories',
            'discount_id', 'category_id');
    }

}
