<?php

use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CheckRiseAlert;
use App\Console\Commands\FetchAndUpdatePrices;

Schedule::command(FetchAndUpdatePrices::class)->everyMinute();

Schedule::command(CheckRiseAlert::class, ['m1', 'm1'])->everyMinute();
Schedule::command(CheckRiseAlert::class, ['m5', 'm5'])->everyFiveMinutes();
Schedule::command(CheckRiseAlert::class, ['m15', 'm15'])->everyFifteenMinutes();
Schedule::command(CheckRiseAlert::class, ['m30', 'm30'])->everyThirtyMinutes();
Schedule::command(CheckRiseAlert::class, ['h1', 'h1'])->hourly();
Schedule::command(CheckRiseAlert::class, ['h4', 'h4'])->everyFourHours();
Schedule::command(CheckRiseAlert::class, ['h12', 'h12'])->twiceDaily();
Schedule::command(CheckRiseAlert::class, ['d1', 'd1'])->daily();
