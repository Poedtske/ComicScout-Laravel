<?php

namespace App\Console;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('db:database-seeder')->once()->when(function () {
        //     // Condition to check if the seeder should be executed
        //     // You can check if the database is empty or any other condition
        //     return DB::table('scanlator_table')->exists(); // Example condition
        // });
        $schedule->command('app:update-serie-list')->weeklyOn(1, '8:00');;

    // Run the updater every 6 hours
    $schedule->command('app:update-series')->everySixHours($minutes = 0);;
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
