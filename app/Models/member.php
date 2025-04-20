<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class member extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($member) {
            $member->kd_member = (string) Str::uuid();
        });

        static::saving(function ($member) {
            $member->updateTierBasedOnPoints();
        });
    }

    public function updateTierBasedOnPoints()
    {
        $tier = 'bronze';
    
        if ($this->points >= 1000) {
            $tier = 'gold';
        } elseif ($this->points >= 500) {
            $tier = 'silver';
        }
    
        if ($this->tier !== $tier) {
            $this->tier = $tier;
            $this->save();
        }
    }

    public function handleInactivityDowngrade()
    {
        if ($this->last_transaction_date) {
            $monthsInactive = now()->diffInMonths(Carbon::parse($this->last_transaction_date));
    
            if ($monthsInactive >= 3) {
                $this->points = max(0, $this->points - 100);
                $this->updateTierBasedOnPoints();
            }
        }
    }
    
}
