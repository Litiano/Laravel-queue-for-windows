<?php

namespace Litiano\LaravelQueueForWindows;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Facade;
use Litiano\LaravelQueueForWindows\Exception\InvalidParamsForWindowsWorker;
use Litiano\LaravelQueueForWindows\Queue\Console\CreateConfigCommand;
use Litiano\LaravelQueueForWindows\Queue\Console\RestartCommand;
use Litiano\LaravelQueueForWindows\Queue\Console\WorkCommand;
use Litiano\LaravelQueueForWindows\Queue\WindowsWorker;

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

    public function register()
    {
        $this->registerWorker();
    }

    /**
     * Register the queue worker.
     *
     * @return void
     */
    protected function registerWorker()
    {
        $this->app->singleton(WindowsWorker::class, function ($app) {
            $isDownForMaintenance = function () {
                return $this->app->isDownForMaintenance();
            };

            $resetScope = function () use ($app) {
                if (method_exists($app['log']->driver(), 'withoutContext')) {
                    $app['log']->withoutContext();
                }

                if (method_exists($app['db'], 'getConnections')) {
                    foreach ($app['db']->getConnections() as $connection) {
                        $connection->resetTotalQueryDuration();
                        $connection->allowQueryDurationHandlersToRunAgain();
                    }
                }

                $app->forgetScopedInstances();

                return Facade::clearResolvedInstances();
            };

            $reflectionClass = new \ReflectionClass(WindowsWorker::class);
            $paramsCount = count($reflectionClass->getConstructor()->getParameters());

            switch ($paramsCount) {
                case 3:
                    return new WindowsWorker(
                        $app['queue'],
                        $app['events'],
                        $app[ExceptionHandler::class]
                    );
                case 4:
                    return new WindowsWorker(
                        $app['queue'],
                        $app['events'],
                        $app[ExceptionHandler::class],
                        $isDownForMaintenance
                    );
                case 5:
                    return new WindowsWorker(
                        $app['queue'],
                        $app['events'],
                        $app[ExceptionHandler::class],
                        $isDownForMaintenance,
                        $resetScope
                    );
                default:
                    throw new InvalidParamsForWindowsWorker("Invalid params count ({$paramsCount}) for WindowsWorker");
            }
        });
    }
}
