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
        \App\Console\Commands\autoNerdStat::class,
        \App\Console\Commands\bonAllocation::class,
        \App\Console\Commands\autoSeedbox::class,
        \App\Console\Commands\autoWarning::class,
        \App\Console\Commands\deactivateWarning::class,
        \App\Console\Commands\revokePermissions::class,
        \App\Console\Commands\autoBan::class,
        \App\Console\Commands\FlushPeers::class,
        \App\Console\Commands\autoGroup::class,
        \App\Console\Commands\removeUserFreeleech::class,
        \App\Console\Commands\removeFeaturedTorrent::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('autoNerdStat')->hourly();
        $schedule->command('bonAllocation')->hourly();
        $schedule->command('autoSeedbox')->hourly();
        $schedule->command('removeWarning')->hourly();
        $schedule->command('deactivateWarning')->hourly();
        $schedule->command('revokePermissions')->hourly();
        $schedule->command('autoBan')->hourly();
        $schedule->command('FlushPeers')->hourly();
        $schedule->command('autoGroup')->daily();
        $schedule->command('removeUserFreeleech')->hourly();
        $schedule->command('removeFeaturedTorrent')->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
