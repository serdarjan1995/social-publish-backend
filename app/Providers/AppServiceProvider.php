<?php

namespace App\Providers;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(250);
        /*Queue::before(function (JobProcessing $event) {
            Log::info('Event Job before '.$event->connectionName);
            Log::info('Event Job before '.$event->job);
            Log::info('Event Job before '.$event->job->payload());
        });

        Queue::after(function (JobProcessed $event) {
            Log::info('Event Job after '.$event->connectionName);
            Log::info('Event Job after '.$event->job);
            Log::info('Event Job after '.$event->job->payload());
        });

        Queue::failing(function (JobFailed $event) {
            Log::info('Event Job failing '.$event->connectionName);
            Log::info('Event Job failing '.$event->job);
            Log::info('Event Job failing '.$event->job->payload());
        });*/
    }
}
