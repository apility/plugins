<?php

namespace Apility\Plugins\Concerns;

use Apility\Plugins\Contracts\PolicyProvider;
use Illuminate\Support\Facades\Gate;

trait HasPolicies
{
    public static function bootHasPolicy(PolicyProvider $plugin)
    {
        $plugin->registerPolicies();
    }

    public function getPolicies(): array
    {
        return $this->policies ?? [];
    }

    public function registerPolicies()
    {
        foreach ($this->policies ?? [] as $for => $policy) {
            Gate::policy($for, $policy);
        }
    }
}
