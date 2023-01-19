<?php

namespace Apility\Plugins\Repositories;

use Closure;
use ReflectionClass;

use Exception;
use ReflectionException;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

use Apility\Plugins\Contracts\Plugin;
use Apility\Plugins\Contracts\PluginRepository as PluginRepositoryContract;

use Apility\Plugins\Exceptions\InvalidPluginException;
use Apility\Plugins\Exceptions\PluginRegistrationException;

class PluginRepository implements PluginRepositoryContract
{
    use Macroable;
    use ForwardsCalls;

    protected Collection $plugins;
    protected Closure $filter;

    protected $isRunningInConsole = null;

    public function __construct()
    {
        $this->plugins = new Collection;
    }

    protected function resolvePlugins(): Collection
    {
        if (App::has(\Illuminate\Contracts\Auth\Access\Gate::class)) {
            return $this->plugins->filter(function (Plugin $plugin) {
                if (!Gate::getPolicyFor(get_class($plugin))) {
                    return $plugin;
                }

                return Gate::allows('view', $plugin);
            });
        }

        return $this->plugins;
    }

    protected function getFilterForType(string $type): Closure
    {
        return fn (Plugin $plugin) => ($plugin instanceof $type) || in_array($type, class_uses($plugin));
    }

    public function count(?string $type = null): int
    {
        if ($type) {
            return $this->countOfType($type);
        }

        return $this->resolvePlugins()
            ->count();
    }

    protected function countOfType(string $type): int
    {
        return $this->resolvePlugins()
            ->filter($this->getFilterForType($type))
            ->count();
    }

    /**
     * @param Plugin|string $plugin
     * @throws InvalidPluginException If the plugin is not a valid plugin
     * @throws PluginRegistrationException If the plugin could not be registered
     */
    public function register($plugin)
    {
        if (!$this->validatePlugin($plugin)) {
            throw new InvalidPluginException($plugin);
        }

        try {
            /** @var Plugin $registered */
            $registered = App::register($plugin);
            $registered->setPluginRepository($this);
            $this->plugins->push($registered);
        } catch (Exception $e) {
            throw new PluginRegistrationException($plugin, $e);
        }
    }

    public function has(string $type): bool
    {
        return $this->first($type) !== null;
    }

    /**
     * @param Plugin|string $plugin
     * @throws ReflectionException
     * @return boolean
     */
    protected function validatePlugin($plugin): bool
    {
        $reflection = new ReflectionClass($plugin);

        if (!$reflection->isSubClassOf(Plugin::class)) {
            return false;
        }

        return true;
    }

    public function first(?string $type = null): ?Plugin
    {
        if ($type) {
            return $this->firstOfType($type);
        }

        return $this->resolvePlugins()
            ->first();
    }

    protected function firstOfType(string $class): ?Plugin
    {
        return $this->resolvePlugins()
            ->first($this->getFilterForType($class));
    }

    /** @return Plugin[] */
    public function all(?string $type = null): array
    {
        if ($type) {
            return $this->allOfType($type);
        }

        return $this->resolvePlugins()
            ->values()
            ->all();
    }

    /** @return Plugin[] */
    protected function allOfType(string $type): array
    {
        return $this->resolvePlugins()
            ->filter($this->getFilterForType($type))
            ->values()
            ->all();
    }

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->resolvePlugins(), $method, $parameters);
    }
}
