<?php

use App\Jobs\Appointments\DispatchAppointmentReminderEvents;
use App\Jobs\GenerateTimeReportForDay;
use App\Jobs\Invoices\DispatchInvoiceDueEvents;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(GenerateTimeReportForDay::class)->dailyAt('02:00');

Artisan::command('time-report:generate-day', function () {
    GenerateTimeReportForDay::dispatch();
});

Schedule::job(DispatchInvoiceDueEvents::class)->daily();

Schedule::job(DispatchAppointmentReminderEvents::class)->everyMinute();

Schedule::job(\App\Jobs\AutocheckoutTimeReports::class)->everyFifteenMinutes();
