<?php

namespace Apility\Plugins\Exceptions;

use Apility\Plugins\Contracts\Plugin;

class InvalidPluginException extends PluginException
{
    public function __construct(string $plugin)
    {
        parent::__construct("Unable to register plugin [$plugin]. Plugin doesn't implement " . Plugin::class);
    }
}
