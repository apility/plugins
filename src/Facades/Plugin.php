<?php

namespace Apility\Plugins\Facades;

use Illuminate\Support\Facades\Facade;

use Apility\Plugins\Contracts\Plugin as PluginContract;
use Apility\Plugins\Contracts\PluginRepository;

/**
 * @method static int count()
 * @method static void register(PluginContract|string $plugin)
 * @method static bool has(string $type)
 * @method static PluginContract|null first(string|null $type = null)
 * @method static PluginContract[] all(string|null $type = null)
 * @see PluginRepository
 */
class Plugin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PluginRepository::class;
    }
}
