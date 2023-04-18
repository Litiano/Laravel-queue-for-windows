<?php

namespace Litiano\WindowsServiceForLaravelQueue\Queue\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * @see Command
 */
trait HasServiceNameOptionTrait
{
    protected string $windowsServiceNameOption = 'windowsServiceName';

    protected function addWindowsServiceNameOption(): void
    {
        $this->addOption(
            $this->windowsServiceNameOption,
            null,
            InputOption::VALUE_REQUIRED,
            'Windows Service Name'
        );
    }

    protected function getWindowsServiceNameOptionValue()
    {
        return $this->option($this->windowsServiceNameOption);
    }
}
