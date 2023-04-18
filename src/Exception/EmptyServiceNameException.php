<?php

namespace Litiano\WindowsServiceForLaravelQueue\Exception;

use RuntimeException;
use Throwable;

class EmptyServiceNameException extends RuntimeException
{
    public function __construct($message = 'Specify serviceName option', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
