<?php

namespace Apility\Plugins\Contracts;

interface Plugin extends ServiceProvider
{
    public function boot();
    public function getPluginRepository(): PluginRepository;
    public function setPluginRepository(PluginRepository $root);
}
