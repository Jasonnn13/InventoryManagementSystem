<?php

use App\Console\Commands\CheckOverduePayments;
use Illuminate\Console\Scheduling\Schedule;

return function (Schedule $schedule) {
    // Schedule the CheckOverduePayments command to run hourly
    $schedule->command(CheckOverduePayments::class)->hourly();
};
