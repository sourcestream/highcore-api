<?php namespace Highcore\Providers;

use Highcore\Services\Provisioner\TowerProvisioner;
use Illuminate\Support\ServiceProvider;

class ProvisioningServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'provisioner',
            'Highcore\Services\Provisioning\TowerProvisioner'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['provisioner', 'Highcore\Services\Provisioning\TowerProvisioner'];
    }

}
