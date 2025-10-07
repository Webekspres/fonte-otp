<?php

namespace Webekspres\FonnteOtp\Providers;

use Illuminate\Support\ServiceProvider;
use Webekspres\FonnteOtp\Commands\InstallCommand;
use Webekspres\FonnteOtp\Commands\SendOtpCommand;
use Webekspres\FonnteOtp\Commands\VerifyOtpCommand;
use Webekspres\FonnteOtp\Services\FonnteOtpService;
use Webekspres\FonnteOtp\Middleware\OtpRateLimiter;
use Webekspres\FonnteOtp\Middleware\OtpVerificationMiddleware;

class FonnteOtpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../Config/fonnte-otp.php', 'fonnte-otp'
        );

        // Bind service
        $this->app->singleton('fonnte-otp', function ($app) {
            return new FonnteOtpService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Handle Laravel version differences
        $this->handleLaravelVersionDifferences();

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                SendOtpCommand::class,
                VerifyOtpCommand::class,
            ]);
        }

        // Register middleware
        $router = $this->app['router'] ?? null;
        if ($router) {
            $router->aliasMiddleware('otp.rate', OtpRateLimiter::class);
            $router->aliasMiddleware('verify.otp', OtpVerificationMiddleware::class);
        }
    }

    /**
     * Handle Laravel version differences for publishing and migrations.
     *
     * @return void
     */
    protected function handleLaravelVersionDifferences()
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../Config/fonnte-otp.php' => config_path('fonnte-otp.php'),
        ], 'fonnte-otp-config');

        // Publish migration
        $this->publishes([
            __DIR__.'/../Database/migrations/' => database_path('migrations'),
        ], 'fonnte-otp-migrations');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
    }
}