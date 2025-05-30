<?php

namespace App\Models;

use App\Models\product;
use App\Models\discount;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];

    public function products()
    {
        return $this->hasMany(product::class);
    }

    public function discount()
    {
        return $this->hasMany(discount::class);
    }
    
}
