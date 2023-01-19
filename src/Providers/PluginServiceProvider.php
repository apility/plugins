<?php

namespace Apility\Plugins\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

use Apility\Plugins\Console\Commands\MakePlugin;
use Apility\Plugins\Contracts\PluginRepository as PluginRepositoryContract;
use Apility\Plugins\Contracts\Plugin as PluginContract;
use Apility\Plugins\Exceptions\PluginRegistrationException;
use Apility\Plugins\Facades\Plugin;
use Apility\Plugins\Repositories\PluginRepository;

use RecursiveDirectoryIterator;
use ReflectionClass;
use RuntimeException;
use Throwable;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/plugins.php',
            'plugins'
        );

        $this->app->singleton(PluginRepositoryContract::class, PluginRepository::class);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakePlugin::class
            ]);
        }

        $this->booted(function () {
            $this->registerPlugins();
        });
    }

    protected function registerPlugin(string $class)
    {
        try {
            if (!class_exists($class)) {
                throw new RuntimeException("Class [{$class}] does not exist.");
            }

            Plugin::register($class);
        } catch (Throwable $e) {
            throw new PluginRegistrationException($class, $e);
        }
    }

    protected function registerPlugins()
    {
        $plugins = Config::get('plugins.enabled', []);

        if (!is_array($plugins)) {
            $plugins = [$plugins];
        }

        if (Config::get('plugins.auto_discover')) {
            $plugins = array_merge($this->discoverPlugins());
        }

        $plugins = array_unique($plugins);

        foreach ($plugins as $plugin) {
            $this->registerPlugin($plugin);
        }
    }

    protected function discoverPlugins(): array
    {
        $basePath = $this->app->basePath();
        $pluginPath = $this->app->basePath(implode(DIRECTORY_SEPARATOR, ['App', 'Plugins']));

        if (!is_dir(($pluginPath))) {
            return [];
        }

        $iterator = new RecursiveDirectoryIterator(
            $pluginPath,
            RecursiveDirectoryIterator::SKIP_DOTS
        );

        $disabled = Config::get('plugins.disabled', []);

        if (!is_array($disabled)) {
            $disabled = [$disabled];
        }

        if (in_array('*', $disabled)) {
            return [];
        }

        $discovered = [];

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $class = Str::replace(
                '/',
                '\\',
                Str::replaceLast(
                    '.' . $file->getExtension(),
                    '',
                    trim(Str::replace(
                        $basePath,
                        '',
                        $file->getRealPath()
                    ), DIRECTORY_SEPARATOR)
                )
            );

            if (in_array($class, $disabled)) {
                continue;
            }

            if (!(new ReflectionClass($class))->isSubClassOf(PluginContract::class)) {
                continue;
            }

            $discovered[] = $class;
        }

        return $discovered;
    }
}
