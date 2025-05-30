<?php

namespace App\Models;

use App\Models\User;
use App\Models\member;
use App\Models\DetailSale;
use Illuminate\Database\Eloquent\Model;

class sale extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($sale) {
            // Update last transaction date for the member
            if ($sale->member_id) {
                $member = $sale->member;
                $member->last_transaction_date = now();
                $member->save();
            }
        });
    }

    public function member()
    {
        return $this->belongsTo(member::class);
    }
    public function User()
    {
        return $this->belongsTo(User::class);
    }
    public function details()
    {
        return $this->hasMany(DetailSale::class);
    }
}
