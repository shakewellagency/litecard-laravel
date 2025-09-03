<?php

namespace Shakewell\Litecard;

use Illuminate\Support\ServiceProvider;
use Shakewell\Litecard\Services\TemplateService;

class LiteCardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/litecard.php', 'litecard');

        // Register services
        $this->app->singleton(Card::class);
        $this->app->singleton(Authenticator::class);
        $this->app->singleton(TemplateService::class);

        // Register aliases
        $this->app->alias(Card::class, 'litecard.card');
        $this->app->alias(Authenticator::class, 'litecard.auth');
        $this->app->alias(TemplateService::class, 'litecard.templates');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../config/litecard.php' => config_path('litecard.php'),
        ], 'litecard-config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'litecard-migrations');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load routes if they exist
        if (file_exists(__DIR__ . '/routes.php')) {
            $this->loadRoutesFrom(__DIR__ . '/routes.php');
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            Card::class,
            Authenticator::class,
            TemplateService::class,
            'litecard.card',
            'litecard.auth',
            'litecard.templates',
        ];
    }
}
