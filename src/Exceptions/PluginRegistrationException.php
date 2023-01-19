<?php

namespace Apility\Plugins\Exceptions;

use Throwable;

class PluginRegistrationException extends PluginException
{
    public function __construct(string $plugin, ?Throwable $previous = null)
    {
        $reason = $previous ? $previous->getMessage() : 'Unknown reason';
        parent::__construct("Unable to register plugin [$plugin]. $reason", $previous ? $previous->getCode() : 0, $previous);
    }
}
