<?php namespace Highcore\Providers;

use Highcore\Services\CloudFormer\CloudFormer;
use Illuminate\Support\ServiceProvider;

class PersistenceServiceProvider extends ServiceProvider {

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
            'persistence',
            'Highcore\Services\Persistence\Persistence'
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
            'persistence',
            'Highcore\Services\Persistence\Persistence',
        ];
    }

}
