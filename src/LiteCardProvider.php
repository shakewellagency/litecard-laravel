<?php

namespace Shakewell\Litecard;

use Illuminate\Support\ServiceProvider;

class LiteCardProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/litecard.php', 'litecard');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([__DIR__.'/../config/litecard.php' => config_path('litecard.php')]);
    }
}
