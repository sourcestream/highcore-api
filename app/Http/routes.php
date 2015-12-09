<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::group(['middleware' => ['auth.basic']], function($router) {
    Route::get('/', 'WelcomeController@index');
    Route::get('home', 'HomeController@index');

    // Environments
    Route::resource('environments', 'EnvironmentsController');
    Route::group(['prefix' => 'environments/{environment_id?}', 'namespace' => 'Environments'], function($router) {
        Route::resource('stacks', 'StacksController', ['only' => ['index']]);
    });
    // Projects
    Route::resource('projects', 'ProjectsController');
    Route::group(['prefix' => 'projects/{project_key?}', 'namespace' => 'Projects'], function($router) {
        Route::resource('parameters', 'ParametersController');
        Route::resource('stacks', 'StacksController', ['only' => ['index']]);
        Route::resource('templates', 'TemplatesController');
        // Environments
        Route::resource('environments', 'EnvironmentsController');
        Route::group(['prefix' => 'environments/{environment_key?}', 'namespace' => 'Environments'], function($router) {
            Route::resource('parameters', 'ParametersController');
            // Stacks
            Route::resource('stacks', 'StacksController');
            Route::group(['prefix' => 'stacks/{stack_id?}', 'namespace' => 'Stacks'], function($router) {
                Route::resource('parameters', 'ParametersController');
                Route::resource('components.parameters', 'ComponentsParametersController');
            });
        });
    });
    // Stacks
    Route::resource('stacks', 'StacksController');
    Route::group(['prefix' => 'stacks/{stack_id?}', 'namespace' => 'Stacks'], function($router) {
        Route::resource('logs', 'LogsController');
        Route::resource('parameters', 'ParametersController');
        Route::resource('templates', 'TemplatesController');
        Route::resource('components.parameters', 'ComponentsParametersController');
    });
    // Templates
    Route::resource('templates', 'TemplatesController');
    Route::group(['prefix' => 'templates/{template_id?}', 'namespace' => 'Templates'], function($router) {
        Route::resource('components', 'ComponentsController', ['only' => ['index', 'show']]);
        Route::resource('parameters', 'ParametersController', ['only' => ['index', 'show']]);
    });
    Route::resource('users', 'UsersController');
});

//funny copypaste from swaggervel, for CORS purposes
Route::any(Config::get('swaggervel.doc-route').'/{page?}', ['middleware' => 'CORS', function($page='api-docs.json') {
    $filePath = Config::get('swaggervel.doc-dir') . "/{$page}";

    if (File::extension($filePath) === "") {
        $filePath .= ".json";
    }
    if (!File::Exists($filePath)) {
        App::abort(404, "Cannot find {$filePath}");
    }

    $content = File::get($filePath);
    return Response::make($content, 200, array(
        'Content-Type' => 'application/json'
    ));
}]);