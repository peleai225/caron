<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Rappels de paiement J-3 (chaque matin à 8h)
Schedule::command('reminders:send --days=3')->dailyAt('08:00');

// Notifications retards (chaque matin à 9h)
Schedule::command('reminders:send --days=0')->dailyAt('09:00');
