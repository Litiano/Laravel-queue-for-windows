<?php

namespace Litiano\LaravelQueueForWindows\Queue\Console;

use Illuminate\Console\Command;
use Litiano\LaravelQueueForWindows\Exception\InvalidOptionException;
use Litiano\LaravelQueueForWindows\Exception\InvalidServiceNameException;

class CreateConfigCommand extends Command
{
    private const BIN_DIR = __DIR__ . '/../../../bin/';
    private const EXE_FILE_NAME = 'LaravelQueueService.exe';
    private const EXE_FILE_PATH = self::BIN_DIR . self::EXE_FILE_NAME;
    private const CONFIG_FILE_NAME = self::BIN_DIR . '.config';
    private const CONFIG_FILE_PATH = self::BIN_DIR . self::CONFIG_FILE_NAME;

    protected $signature = "windows:service:queue:create
                            {windowsServiceName : Windows Service Name}
                            {--queueConnection=database : The name of the queue connection to work}
                            {--queue= : The names of the queues to work}
                            {--displayName=Laravel Queue Work : Windows Service Display Name}
                            {--description=Laravel Queue Work Service : Windows Service Description}
                            {--startType=Automatic : Windows Service Start Type [Automatic,Manual,Disabled]}
                            {--queueWorkArguments= : Others Queue Work Options}";

    protected $description = 'Create new Windows queue service config.';


    public function handle(): void
    {
        $this->validateOptions();

        $windowsServiceName = $this->argument('windowsServiceName');
        $serviceBasePath = $this->laravel->basePath("bin/windows-queue-service/{$windowsServiceName}");
        if (is_dir($serviceBasePath)) {
            throw new InvalidServiceNameException("Directory {$serviceBasePath} already exists.");
        }

        mkdir($serviceBasePath, 0777, true);
        $newExePath = $serviceBasePath . '/' . self::EXE_FILE_NAME;
        $newConfigPath = $serviceBasePath . '/' . self::CONFIG_FILE_NAME;

        copy(self::EXE_FILE_PATH, $newExePath);
        file_put_contents($newConfigPath, $this->getConfigFileContent());

        $this->info("New config for Windows queue service created successfully.");
        $this->warn("Run {$newExePath} as administrator, check config and click on Install button.");
    }

    private function validateOptions(): void
    {
        if (!in_array($this->option('startType'), ['Automatic', 'Manual', 'Disabled'])) {
            throw new InvalidOptionException('Invalid startType');
        }
    }

    private function getConfigFileContent(): string
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
            '{{queue_work_arguments}}' => ($queue ? "--queue={$queue} " : '') . $queueWorkArguments,
            '{{php_path}}' => PHP_BINARY,
            '{{laravel_base_path}}' => $this->laravel->basePath(),
        ];

        $configFile = file_get_contents(self::CONFIG_FILE_PATH);

        return str_replace(array_keys($config), array_values($config), $configFile);
    }
}
