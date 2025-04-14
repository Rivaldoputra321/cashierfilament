<?php

namespace App\Models;

use App\Models\product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class category extends Model
{
    protected $guarded = [];

    public function products(): BelongsToMany
{
    return $this->belongsToMany(product::class, 'category_products',
      'category_id', 'product_id');
}

}
