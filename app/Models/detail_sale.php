<?php

namespace App\Models;

use App\Models\sale;
use App\Models\product;
use App\Models\discount;
use Illuminate\Database\Eloquent\Model;

class detail_sale extends Model
{
    protected $guarded = [];

    public function sale()
    {
        return $this->belongsTo(sale::class);
    }

    public function product()
    {
        return $this->belongsTo(product::class);
    }

    public function discount()
    {
        return $this->belongsTo(discount::class);
    }
}
