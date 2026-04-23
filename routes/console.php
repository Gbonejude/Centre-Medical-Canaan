<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('app:update-course-progress')->everyMinute();
Schedule::command('app:birthdays-check:notification')->dailyAt('02:00');
Schedule::command('app:documents:check-expiry')->dailyAt('08:00');
