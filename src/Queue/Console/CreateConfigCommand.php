<?php

namespace Litiano\WindowsServiceForLaravelQueue\Queue\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateConfigCommand extends Command
{
    protected $signature = "windows:service:queue:create
                            {windowsServiceName : Windows Service Name}
                            {--queueConnection=database : The name of the queue connection to work}
                            {--queue= : The names of the queues to work}
                            {--displayName=Laravel Queue Work : Windows Service Display Name}
                            {--description=Laravel Queue Work Service : Windows Service Description}
                            {--startType=Automatic : Windows Service Start Type [Automatic,Manual,Disabled]}
                            {--queueWorkArguments= : Queue Work Arguments}";

    public function handle()
    {
        $windowsServiceName = $this->argument('windowsServiceName');
        $queueConnection = $this->option('queueConnection');
        $queue = $this->option('queue');
        $displayName = $this->option('displayName');
        $description = $this->option('description');
        $startType = $this->option('startType');
        $queueWorkArguments = $this->option('queueWorkArguments');
        $config = [
            '{{service_name}}' => $windowsServiceName,
            '{{queue_connection}}' => $queueConnection,
            '{{service_display_name}}' => "{$displayName} [{$windowsServiceName}]",
            '{{service_description}}' => $description,
            '{{service_start_type}}' => $startType,
            '{{queue_work_arguments}}' => $queueWorkArguments . ($queue ? " --queue={$queue}" : ''),
            '{{php_path}}' => PHP_BINARY,
            '{{laravel_base_path}}' => $this->laravel->basePath(),
        ];

        if (!in_array($startType, ['Automatic', 'Manual', 'Disabled'])) {
            $this->error('Invalid startType');
            return;
        }

        $laravelBasePath = $this->laravel->basePath("bin/windows-queue-service/{$windowsServiceName}");
        if (is_dir($laravelBasePath)) {
            $this->error("Directory {$laravelBasePath} already exists.");
            return;
        }

        $exeBasePath = __DIR__ . '/../../../bin';
        $exeName = 'LaravelQueueService.exe';
        $configFileName = "{$exeName}.config";

        $configFile = file_get_contents("{$exeBasePath}/{$configFileName}");
        $configFile = str_replace(array_keys($config), array_values($config), $configFile);

        mkdir($laravelBasePath, 0777, true);
        copy("$exeBasePath/{$exeName}", "{$laravelBasePath}/{$exeName}");
        file_put_contents("{$laravelBasePath}/{$configFileName}", $configFile);

        $this->warn("Run {$laravelBasePath}/{$exeName} as administrator, check config and click on Install button.");
    }
}
