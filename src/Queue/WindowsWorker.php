<?php

namespace Litiano\WindowsServiceForLaravelQueue\Queue;

use Illuminate\Queue\Worker;

class WindowsWorker extends Worker
{
    protected string $windowsServiceName;

    public function setWindowsServiceName(string $windowsServiceName): void
    {
        $this->windowsServiceName = $windowsServiceName;
    }

    protected function getTimestampOfLastQueueRestart()
    {
        if ($this->cache) {
            return $this->cache->get("windows:service:queue:restart:{$this->windowsServiceName}");
        }

        return null;
    }
}
