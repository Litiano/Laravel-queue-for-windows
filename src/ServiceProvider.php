<?php

namespace Litiano\WindowsServiceForLaravelQueue;

use Litiano\WindowsServiceForLaravelQueue\Queue\Console\CreateConfigCommand;
use Litiano\WindowsServiceForLaravelQueue\Queue\Console\RestartCommand;
use Litiano\WindowsServiceForLaravelQueue\Queue\Console\WorkCommand;

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
