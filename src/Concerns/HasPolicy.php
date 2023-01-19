<?php

namespace Apility\Plugins\Concerns;

use Apility\Plugins\Contracts\PolicyProvider;
use Illuminate\Support\Facades\Gate;

trait HasPolicy
{
    public static function bootHasPolicy(PolicyProvider $plugin)
    {
        $plugin->registerPolicy();
    }

    public function registerPolicy()
    {
        /** @var PolicyProvider $this */
        Gate::policy(static::class, $this->getPolicy());
    }
}
