<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Auditoría automática diaria a las 06:00
        $schedule->command('auditoria:automatica --dias=1 --enviar-alertas')
                 ->dailyAt('06:00')
                 ->name('auditoria-diaria')
                 ->description('Auditoría automática diaria del sistema')
                 ->onFailure(function () {
                     Log::error('Falló la auditoría automática diaria');
                 });
        
        // Auditoría semanal completa los domingos a las 02:00
        $schedule->command('auditoria:automatica --dias=7 --enviar-alertas')
                 ->weeklyOn(0, '02:00')
                 ->name('auditoria-semanal')
                 ->description('Auditoría automática semanal completa')
                 ->onFailure(function () {
                     Log::error('Falló la auditoría automática semanal');
                 });
        
        // Verificación rápida de stock crítico cada 4 horas
        $schedule->command('auditoria:automatica --dias=1')
                 ->cron('0 */4 * * *')
                 ->name('verificacion-stock')
                 ->description('Verificación de stock crítico cada 4 horas')
                 ->onFailure(function () {
                     Log::warning('Falló la verificación de stock automática');
                 });
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
