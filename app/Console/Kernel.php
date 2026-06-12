<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Cek stok kritis setiap hari jam 07:00
        $schedule->command('simor:check-stock')->dailyAt('07:00');

        // Cek kadaluarsa setiap hari jam 06:00
        $schedule->command('simor:check-expiry')->dailyAt('06:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
