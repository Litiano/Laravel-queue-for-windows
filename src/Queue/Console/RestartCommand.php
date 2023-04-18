<?php

namespace Litiano\LaravelQueueForWindows\Queue\Console;

use Litiano\LaravelQueueForWindows\Exception\EmptyServiceNameException;

class RestartCommand extends \Illuminate\Queue\Console\RestartCommand
{
    use HasServiceNameOptionTrait;

    public function handle()
    {
        $serviceName = $this->getWindowsServiceNameOptionValue();
        if (empty($serviceName)) {
            throw new EmptyServiceNameException();
        }

        $cacheKey = 'windows:service:queue:restart:' . $serviceName;
        $this->_getLaravelCache()->forever($cacheKey, $this->currentTime());
        $this->info('Windows Service Queue restart signal.');
    }

    protected function _getLaravelCache()
    {
        if (!empty($this->cache)) {
            return $this->cache;
        }

        return $this->laravel['cache'];
    }

    protected function configure()
    {
        parent::configure();
        $this->addWindowsServiceNameOption();
        $this->setName('windows:service:queue:restart');
    }
}
