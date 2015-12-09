<?php namespace Highcore\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory;

class ResponseMacroServiceProvider extends ServiceProvider {

    /**
     * Perform post-registration booting of services.
     *
     * @param  ResponseFactory  $response
     * @return void
     */
    public function boot(ResponseFactory $response)
    {
        $response->macro('crossDomainJson', function($value) use ($response)
        {
            return $response
                ->json($value)
                ->header('Access-Control-Allow-Origin', '*');
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {}

}