<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'owner' => \App\Http\Middleware\EnsureUserIsOwner::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Update status penetasan setiap hari pada jam 00:01
        $schedule->command('penetasan:update-status')->dailyAt('00:01');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
