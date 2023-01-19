# Laravel plugin architecture

This package provides a simple way of making your Laravel application modular.
It implments a simple plugin architecture that allows you to create plugins and register them in a shared repository.

You can then use feature detection through interfaces or traits to registered plugins.

The plugins are an abstraction around Laravel's service providers, so you can use all the features of service providers in your plugins.

## Installation

You can install the package via composer:

```bash
composer require apility/plugins
```

## Usage

### Creating a plugin

To create a plugin, you need to create a class that implements the `Apility\Plugins\Contracts\Plugin` interface.

The simplest way to do this is to extend from the abstract `Apility\Plugins\Plugin` class.

```php

use Apility\Plugins\Plugin;

class MyPlugin extends Plugin
{
    public function register()
    {
        // Here you can perform any registration that
        // you would normally do in a service provider.
    }
}
```

### Registering a plugin

To register a plugin, you need to register them in the plugin repository.

The plugin repository is a singleton that implements the `Apility\Plugins\Contracts\PluginRepository` interface.

The recommended way to interface with the repository is through the `Apility\Plugins\Facades\Plugin` facade.

```php
use Apility\Plugins\Facades\Plugin;

Plugin::register(MyPlugin::class);
```

### Using a plugin

The main purpose of this package is to allow you to use plugins in your application through feature detection.

You can use the `Apility\Plugins\Facades\Plugin` facade to resolve plugins based on features (interfaces or traits).

```php
use Apility\Plugins\Facades\Plugin;
use Apility\Plugins\Plugin as BasePlugin;

interface MyFeature
{
    public function doSomething();
}

class MyPlugin extends BasePlugin implements MyFeature
{
    public function doSomething()
    {
        return 'Hello world!';
    }
}

$plugins = Plugin::all(MyFeature::class);

foreach ($plugins as $plugin) {
    /** @var MyFeature $plugin */
    echo $plugin->doSomething();
}
```

#### Other ways of using plugins

```php
use Apility\Plugins\Facades\Plugin;

// Get the number of registered plugins
$pluginCount = Plugin::count();

// Get the number of registered plugins by type
$pluginCount = Plugin::count(MyPlugin::class);

// Check if a plugin is registered
$pluginRegistered = Plugin::has(MyPlugin::class);

// Get all plugins
$plugins = Plugin::all();

// Get all plugins by type
$plugins = Plugin::all(MyPlugin::class);

// Get the first registered plugin
$plugin = Plugin::first();

// Get a plugin by type
$plugin = Plugin::first(MyPlugin::class);
```

```php
use Apility\Plugins\Facades\Plugin;

$plugins = Plugin::filter(function ($plugin) {
    return $plugin instanceof MyPlugin;
});
```

## Generating a plugin

You can use the `make:plugin` command to generate a plugin.

```bash
php artisan make:plugin MyPlugin
```

### Generating a plugin with Policy

You can use the `--policy` option to generate a plugin with a corresponsing policy.

```bash
php artisan make:plugin MyPlugin --policy
```