<?php

declare(strict_types=1);

namespace iAmBiB\Hooks\Providers;

use iAmBiB\Hooks\Hooks;
use Illuminate\Support\ServiceProvider;

final class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->singleton('hooks', function ($app)
        {
            return new Hooks();
        });
        $this->_hooks = $this->app->make('hooks');
        $this->_hooks->execute_hook('init_plugin_activation');
        $this->publishes(
            [
                \dirname(__DIR__) . '/publishers/hooks.php' => config_path('hooks.php'),
                \dirname(__DIR__) . '/publishers/plugins' => config('hooks.plugins_folder'),
            ],
            'iambib-hooks'
        );
    }

    public function register(): void
    {
        $configPath = \dirname(__DIR__) . '/publishers/hooks.php';
        $this->mergeConfigFrom($configPath, 'hooks');
        require_once \dirname(\dirname(__FILE__)) . '/Helpers/helpers.php';
    }
}
