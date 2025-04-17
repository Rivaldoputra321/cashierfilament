<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\member;

class DowngradeInactiveMembers extends Command
{
    protected $signature = 'members:downgrade-inactive';
    protected $description = 'Downgrade tiers of inactive members';

    public function handle()
    {
        $members = member::all();

        foreach ($members as $member) {
            $member->checkInactiveAndDowngrade();
        }

        $this->info('Downgrade process for inactive members completed.');
    }
}