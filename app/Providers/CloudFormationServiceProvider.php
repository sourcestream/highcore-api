<?php namespace Highcore\Providers;

use Highcore\Services\CloudFormer\CloudFormer;
use Illuminate\Support\ServiceProvider;

class CloudFormationServiceProvider extends ServiceProvider {

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
            'cloud_former',
            'Highcore\Services\CloudFormer\CloudFormer'
        );
        $this->app->bind(
            'template_engine',
            'Highcore\Services\CloudFormer\Sparkle'
        );
	}

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'cloud_former',
            'template_engine',
            'Highcore\Services\CloudFormer\CloudFormer',
            'Highcore\Services\CloudFormer\Sparkle'
        ];
    }

}
