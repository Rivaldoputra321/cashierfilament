<?php

use App\Models\member;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $members = member::all();

    foreach ($members as $member) {
        $member->handleInactivityDowngrade(); // method ini kamu buat di model Member
    }
})->daily(); 