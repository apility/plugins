<?php

namespace Apility\Plugins\Contracts;

use Apility\Plugins\Contracts\Plugin;
use Illuminate\Support\HigherOrderCollectionProxy;

interface PluginRepository
{
    public function count(?string $type = null): int;

    /** @param Plugin|string $plugin */
    public function register($plugin);

    public function has(string $type): bool;

    public function first(?string $type = null): ?Plugin;

    /** @return Plugin[] */
    public function all(?string $type = null): array;

    public function each(?string $type = null): HigherOrderCollectionProxy;
}
