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

        // Import
        $schedule->command('import:repositories')
            ->daily();
        $schedule->command('module:run Import\\\Vulnerabilities\\\WordPress\\\WPVulnDB')
            ->daily();
        $schedule->command('module:run Import\\\Vulnerabilities\\\Javascript\\\Nodejs\\\SecurityWG')
            ->hourly();
        $schedule->command('module:run Import\\\Vulnerabilities\\\Composer\\\SecurityAdvisories')
            ->hourly();
        $schedule->command('module:run Import\\\Vulnerabilities\\\NIST\\\NVD')
            ->daily();

        // Discovery
        $schedule->command('discovery:organisations single')
            ->weekly()
            ->withoutOverlapping();
        $schedule->command('discovery:domains single')
            ->everyTenMinutes()
            ->withoutOverlapping();
        $schedule->command('discovery:subdomains single')
            ->everyTenMinutes()
            ->withoutOverlapping();
        $schedule->command('discovery:hosts single')
            ->everyTenMinutes()
            ->withoutOverlapping();
        $schedule->command('discovery:ports')
            ->everyTenMinutes()
            ->withoutOverlapping();
        $schedule->command('discovery:certificates')
            ->everyTenMinutes()
            ->withoutOverlapping();
        $schedule->command('discovery:repositories single')
            ->everyTenMinutes()
            ->withoutOverlapping();
        $schedule->command('discovery:websites single')
            ->everyTenMinutes()
            ->withoutOverlapping();

        // Audit
        $schedule->command('audit:domains')
            ->hourly()
            ->withoutOverlapping();
        $schedule->command('audit:subdomains')
            ->hourly()
            ->withoutOverlapping();
        $schedule->command('audit:installations')
            ->hourly()
            ->withoutOverlapping();
        $schedule->command('audit:repositories single')
            ->hourly()
            ->withoutOverlapping();
        $schedule->command('audit:websites single')
            ->hourly()
            ->withoutOverlapping();

        // Clean
        $schedule->command('tools:clean-orphans')
            ->daily()
            ->runInBackground();
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
