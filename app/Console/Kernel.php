<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        //Recalcula la caché de "pendiente por producir" (stock - pendiente) usada por calcprecioprodsn (#stockM)
        //en cotización/nota de venta. Requiere un único cron en el hosting que llame "php artisan schedule:run"
        //cada minuto; Laravel decide internamente cuándo corresponde ejecutar este comando.
        $schedule->command('producto:actualizar-cache-pendientexproducir')
                 ->everyFiveMinutes()
                 ->withoutOverlapping();
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
