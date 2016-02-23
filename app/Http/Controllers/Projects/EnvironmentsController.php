<?php namespace Highcore\Http\Controllers\Projects;

use Highcore\Http\Controllers\Controller;
use Highcore\Models\Environment;
use Persistence;
use Response;
use Input;

class EnvironmentsController extends Controller {

	/**
	 * Display a listing of the resource.
     * @SWG\Get(
     *     path="/projects/{project_key}/environments",
     *     summary="All project environments",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Environments"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
	 * @return Response
	 */
	public function index($project_key)
	{
        return Persistence::getEnvironments(compact('project_key'), Input::get('key', 'id'));
	}

    /**
     * Store a newly created resource in storage.
     * @SWG\Post(
     *     path="/projects/{project_key}/environments",
     *     summary="Store project environment",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Parameter(ref="#/parameters/Environment"),
     *     @SWG\Response(response="default", ref="#/responses/Environments"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @return Response
     */
    public function store($project_key)
    {
        return Persistence::saveEnvironment(
            Environment::make(Input::all())
                ->assign(Persistence::getProject(['key' => $project_key]), Input::get('key', 'id'))
        );
    }

    /**
     * Display the specified resource.
     * @SWG\Get(
     *     path="/projects/{project_key}/environments/{environment_key}",
     *     summary="Display project environment",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Environments"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @return Response
     */
    public function show($project_key, $environment_key)
    {
        $key = $environment_key;
        return Persistence::getEnvironment(compact('project_key', 'key'), Input::get('key', 'id'));
    }

    /**
     * Update the specified resource in storage.
     * @SWG\Put(
     *     path="/projects/{project_key}/environments/{environment_key}",
     *     summary="Update project environment",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Parameter(ref="#/parameters/Environment"),
     *     @SWG\Response(response="default", ref="#/responses/Environments"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @return Response
     */
    public function update($project_key, $environment_key)
    {
        $key = $environment_key;
        return Persistence::saveEnvironment(
            Persistence::getEnvironment(compact('project_key', 'key'), Input::get('key', 'id'))->fill(Input::all())
        );
    }

    /**
     * Remove the specified resource from storage.
     * @SWG\Delete(
     *     path="/projects/{project_key}/environments/{environment_key}",
     *     summary="Delete project environment",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Environments"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @return Response
     */
    public function destroy($project_key, $environment_key)
    {
        $key = $environment_key;
        Persistence::deleteEnvironment(
            Persistence::getEnvironment(compact('project_key', 'key'), Input::get('key', 'id'))
        );
        return 'true';
    }
}
