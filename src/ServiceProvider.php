<?php

namespace Litiano\LaravelQueueForWindows;

use Litiano\LaravelQueueForWindows\Queue\Console\CreateConfigCommand;
use Litiano\LaravelQueueForWindows\Queue\Console\RestartCommand;
use Litiano\LaravelQueueForWindows\Queue\Console\WorkCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                WorkCommand::class,
                RestartCommand::class,
                CreateConfigCommand::class,
            ]);
        }
    }
}
