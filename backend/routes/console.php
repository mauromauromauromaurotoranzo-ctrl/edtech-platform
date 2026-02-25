<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Daily challenge generation at 11 PM
Schedule::command('challenges:generate-next-day')
    ->dailyAt('23:00')
    ->timezone('America/Argentina/Buenos_Aires');

// Send daily challenges at 8 AM
Schedule::command('challenges:send-daily')
    ->dailyAt('08:00')
    ->timezone('America/Argentina/Buenos_Aires');

// Process pending embeddings every hour
Schedule::call(function () {
    app(\App\Application\Services\RAGService::class)->processPendingEmbeddings(100);
})->hourly();

// Smart reminders - check every 15 minutes
Schedule::command('reminders:send')
    ->everyFifteenMinutes()
    ->timezone('America/Argentina/Buenos_Aires');

// Spaced repetition reminders at 9 AM
Schedule::command('sr:send-reminders')
    ->dailyAt('09:00')
    ->timezone('America/Argentina/Buenos_Aires');

// Inactivity detection at 10 AM
Schedule::call(function () {
    app(\App\Application\Services\InactivityDetectorService::class)->detectAndNotify(3);
})->dailyAt('10:00')->timezone('America/Argentina/Buenos_Aires');
