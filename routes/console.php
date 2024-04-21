<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('telescope:prune')->daily()->at('01:00');
Schedule::command('backup:clean')->weekly()->at('01:15');
Schedule::command('backup:run')->weekly()->at('01:20');
