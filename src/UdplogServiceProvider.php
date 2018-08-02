<?php
namespace Bulaohe\Udplog;

use Illuminate\Support\ServiceProvider;
use Bulaohe\Udplog\UdplogService;

class UdplogServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
        $this->app->singleton('udplog', function ($app) {
            return new UdplogService();
        });
        $this->app->alias('udplog', UdplogService::class);
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/udplog.php' => base_path('config/udplog.php')
        ], 'config');
    }

    /**
     * Merge configurations.
     */
    protected function mergeConfig()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/udplog.php', 'udplog');
    }
}
