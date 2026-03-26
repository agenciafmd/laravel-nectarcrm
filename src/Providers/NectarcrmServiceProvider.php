<?php

declare(strict_types=1);

namespace Agenciafmd\Nectarcrm\Providers;

use Illuminate\Support\ServiceProvider;
use Override;

final class NectarcrmServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->providers();

        $this->publishConfigs();
    }

    #[Override]
    public function register(): void
    {
        $this->loadConfigs();
    }

    public function providers(): void
    {
        $this->app->register(HttpServiceProvider::class);
    }

    protected function loadConfigs(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/laravel-nectarcrm.php', 'laravel-nectarcrm');
    }

    protected function publishConfigs(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/laravel-nectarcrm.php' => config_path('laravel-nectarcrm.php'),
        ], 'laravel-nectarcrm:config');
    }
}
