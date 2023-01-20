<?php

namespace Apility\Plugins\Concerns;

use RuntimeException;
use Apility\Plugins\Contracts\PluginRepository;

trait HasRepository
{
    protected ?PluginRepository $repository = null;

    public function getPluginRepository(): PluginRepository
    {
        if ($this->repository === null) {
            throw new RuntimeException('Plugin repository has not been set');
        }

        return $this->repository;
    }

    public function setPluginRepository(PluginRepository $repository)
    {
        if ($this->repository !== null) {
            throw new RuntimeException('Plugin repository has already been set');
        }

        $this->repository = $repository;
    }
}
