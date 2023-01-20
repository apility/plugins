<?php

namespace Apility\Plugins\Concerns;

use Apility\Plugins\Contracts\Plugin;
use Apility\Plugins\Contracts\PluginRepository;
use Apility\Plugins\Contracts\RouteProvider;

use Illuminate\Support\Traits\Macroable;

trait HasRoutes
{
    public static function bootHasRoutes(RouteProvider $plugin)
    {
        /** @var Plugin&RouteProvider $plugin */
        if ($repository = $plugin->getPluginRepository()) {
            static::registerRouteMacro($repository);
        }
    }

    protected static function registerRouteMacro(PluginRepository $repository)
    {
        if (class_uses($repository, Macroable::class)) {
            /** @var PluginRepository&Macroable $repository */
            if (!$repository->hasMacro('routes')) {
                $repository->macro('routes', function () {
                    foreach ($this->all(RouteProvider::class) as $plugin) {
                        /** @var RouteProvider $plugin */
                        $plugin->routes();
                    }
                });
            }
        }
    }
}
