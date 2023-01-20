<?php

namespace Apility\Plugins\Concerns;

use Apility\Plugins\Contracts\CommandProvider;

trait HasCommands
{
    public static function bootHasCommands(CommandProvider $plugin)
    {
        $plugin->registerCommands();
    }
}
