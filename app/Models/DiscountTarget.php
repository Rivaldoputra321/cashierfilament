<?php

namespace App\Models;

use App\Models\product;
use App\Models\category;
use App\Models\discount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountTarget extends Model
{
    
    public function discount()
    {
        return $this->belongsTo(discount::class);
    }

    public function targetable(): MorphTo
    {
        return $this->morphTo();
    }
}
