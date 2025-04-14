<?php

namespace App\Models;

use App\Models\category;
use App\Models\discount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class product extends Model
{
    protected $guarded = [];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(category::class, 'category_products',
          'category_id', 'product_id');
    }


}
