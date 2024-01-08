<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('scrap:prices')->everyTwoHours();
        $schedule->command('scrap:Fuelprices')->everyWeek();
        $schedule->command('scrap:Fuelprices')->monthlyOn(2, '00:00');
        $schedule->command('scrap:Fuelprices')->monthlyOn(16, '00:00');

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
