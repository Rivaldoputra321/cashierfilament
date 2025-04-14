<?php

namespace App\Models;

use App\Models\product;
use App\Models\category;
use App\Models\discount;
use Illuminate\Support\Str;
use App\Models\DiscountTarget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class discount extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($discount) {
            $discount->kd_discount = (string) Str::uuid();
        });
    }

    public function targets()
    {
        return $this->hasMany(DiscountTarget::class);
    }

    public function products()
    {
        return $this->morphedByMany(product::class, 'targetable', 'discount_targets');
    }

    public function categories()
    {
        return $this->morphedByMany(category::class, 'targetable', 'discount_targets');
    }
    
}
