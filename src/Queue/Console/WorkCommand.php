<?php

namespace Litiano\LaravelQueueForWindows\Queue\Console;

use Litiano\LaravelQueueForWindows\Exception\EmptyServiceNameException;
use Litiano\LaravelQueueForWindows\Queue\WindowsWorker;

/**
 * @property WindowsWorker $worker
 */
class WorkCommand extends \Illuminate\Queue\Console\WorkCommand
{
    use HasServiceNameOptionTrait;

    public function handle()
    {
        $serviceName = $this->getWindowsServiceNameOptionValue();
        if (empty($serviceName)) {
            throw new EmptyServiceNameException();
        }

        $this->worker = $this->laravel->make(WindowsWorker::class);
        $this->worker->setWindowsServiceName($serviceName);
        $this->setWindowsCtrlEventHandler();

        parent::handle();
    }

    protected function setWindowsCtrlEventHandler(): void
    {
        if (!function_exists('sapi_windows_set_ctrl_handler')) {
            return;
        }

        sapi_windows_set_ctrl_handler(function (int $event) {
            if ($event === PHP_WINDOWS_EVENT_CTRL_C) {
                $this->info('Windows Ctrl+C event handler');
                $this->worker->shouldQuit = true;
            }
        });
    }

    protected function configure()
    {
        parent::configure();
        $this->addWindowsServiceNameOption();
        $this->setName('windows:service:queue:work');
    }
}
