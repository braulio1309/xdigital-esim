<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel.
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     *
     * Scheduler setup:
     * - Production cron (once): * * * * * cd /ruta/proyecto && php artisan schedule:run >> /dev/null 2>&1
     * - Local verification: php artisan schedule:run
     *   (alternativamente ejecutar directo: php artisan notificar:consumo-esim)
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('notificar:consumo-esim')
            ->everyThirtyMinutes()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
