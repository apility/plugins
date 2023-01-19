<?php

namespace Apility\Plugins;

use Illuminate\Support\ServiceProvider;

use Apility\Plugins\Concerns\BootableTraits;
use Apility\Plugins\Concerns\HasRepository;
use Apility\Plugins\Contracts\Plugin as PluginContract;

abstract class AbstractPlugin extends ServiceProvider implements PluginContract
{
    use HasRepository;
    use BootableTraits;
}
