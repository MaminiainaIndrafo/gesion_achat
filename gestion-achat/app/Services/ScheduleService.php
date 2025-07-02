<?php

namespace App\Services;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class ScheduleService extends ServiceProvider
{
    public function register(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $this->schedule($schedule);
        });
    }

    protected function schedule(Schedule $schedule): void
    {
        // Synchronisation quotidienne des produits
        $schedule->command('products:sync-suppliers')
            ->dailyAt('03:00')
            ->onOneServer()
            ->appendOutputTo(storage_path('logs/supplier-sync.log'));

        // Nettoyage hebdomadaire des logs
        $schedule->command('log:clear --keep-last')->weekly();
    }
}