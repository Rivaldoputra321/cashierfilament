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
    }

    public function checkInactiveAndDowngrade()
    {
        $inactivePeriod = Carbon::now()->subMonths(3); // 3 bulan tanpa transaksi

        if ($this->last_transaction_date && Carbon::parse($this->last_transaction_date)->lessThan($inactivePeriod)) {
            $this->downgradeTier();
        }
    }

    /**
     * Downgrade the member's tier.
     */
    public function downgradeTier()
    {
        switch ($this->tier) {
            case 'Gold':
                $this->tier = 'Silver';
                break;
            case 'Silver':
                $this->tier = 'Bronze';
                break;
            default:
                $this->tier = 'Bronze';
        }

        $this->save();
    }
}
