<?php

namespace Apility\Plugins\Contracts;

use Closure;

interface ServiceProvider
{
    public function register();

    public function booting(Closure $callback);

    public function booted(Closure $callback);

    public function callBootingCallbacks();

    public function callBootedCallbacks();

    public static function publishableProviders();

    public static function publishableGroups();

    public function commands($commands);

    public function provides();

    public function when();

    public function isDeferred();
}
