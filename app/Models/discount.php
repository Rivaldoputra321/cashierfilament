<?php

namespace App\Models;

use App\Models\product;
use App\Models\category;
use App\Models\discount;
use App\Models\Supplier;
use Illuminate\Support\Str;
use App\Models\DiscountTarget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class discount extends Model
{
    protected $guarded = [];

    protected $casts = [
        'member_tiers' => 'array',
    ];

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

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(category::class, 'discount_categories',
            'discount_id', 'category_id');
    }
    
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(product::class, 'discount_products',
            'discount_id', 'product_id');
    }



}
