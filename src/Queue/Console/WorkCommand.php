<?php

namespace Litiano\WindowsServiceForLaravelQueue\Queue\Console;

use Litiano\WindowsServiceForLaravelQueue\Exception\EmptyServiceNameException;
use Litiano\WindowsServiceForLaravelQueue\Queue\WindowsWorker;

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

                $this->call(
                    'windows:service:queue:restart',
                    [$this->windowsServiceNameOption => $this->getWindowsServiceNameOptionValue()]
                );
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