<?php namespace Highcore\Http\Controllers\Projects;

use Highcore\Http\Controllers\Controller;
use Highcore\Models\Parameter;
use Route;
use Request;
use Response;
use Input;
use Persistence;
use Exception;

class ParametersController extends Controller {

    /**
     * Display a listing of the resource.
     * @SWG\Get(
     *     path="/projects/{project_key}/parameters",
     *     summary="All project parameters",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Parameters"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @return Response
     */
    public function index($project_key)
    {
        return Persistence::getProject($project_key, Input::get('key', 'id'))->parameters;
    }

    /**
     * Store a newly created resource in storage.
     * @SWG\Post(
     *     path="/projects/{project_key}/parameters",
     *     summary="Store project parameter",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Parameter(ref="#/parameters/source"),
     *     @SWG\Parameter(ref="#/parameters/Parameter"),
     *     @SWG\Response(response="default", ref="#/responses/Parameter"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @return Response
     * @throws Exception
     */
    public function store($project_key)
    {
        $source = Input::get('source', false);
        $input = array_filter(Input::only(['id', 'value', 'sensitive']));
        if ($source) {
            $request = Request::create($source, 'GET');
            /** @var Parameter $project */
            $parameter = Route::dispatch($request)->getOriginalContent();
        } else {
            $parameter = Parameter::make();
        }
        $parameter->fill($input);
        $project = Persistence::getProject($project_key, Input::get('key', 'id'));
        $project->parameters->put($parameter->id, $parameter);
        Persistence::saveProject($project);
        return $parameter;
    }

    /**
     * Display the specified resource.
     * @SWG\Get(
     *     path="/projects/{project_key}/parameters/{parameter_id}",
     *     summary="Display project parameter",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/parameter_id"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Parameter"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  string  $parameter_id
     * @return Response
     */
    public function show($project_key, $parameter_id)
    {
        $project = Persistence::getProject($project_key, Input::get('key', 'id'));
        $parameters = $project->parameters;
        if (!$parameters->has($parameter_id)) {abort(404);}
        return $parameters->get($parameter_id);
    }

    /**
     * Update the specified resource in storage.
     * @SWG\Put(
     *     path="/projects/{project_key}/parameters/{parameter_id}",
     *     summary="Update project parameter",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/parameter_id"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Parameter(ref="#/parameters/Parameter"),
     *     @SWG\Response(response="default", ref="#/responses/Parameter"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  string  $parameter_id
     * @return Response
     * @throws Exception
     */
    public function update($project_key, $parameter_id)
    {
        $project = Persistence::getProject($project_key, Input::get('key', 'id'));
        $parameters = $project->parameters;
        if (!$parameters->has($parameter_id)) {abort(404);}
        /** @var Parameter $parameter */
        $parameter = $parameters->get($parameter_id);
        $parameter->fill(Input::only('value', 'sensitive'));
        Persistence::saveProject($project);
        return $parameter;
    }

    /**
     * Destroy stack parameter
     * @SWG\Delete(
     *     path="/projects/{project_key}/parameters/{parameter_id}",
     *     summary="Delete project parameter",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/parameter_id"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Bool"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  string  $parameter_id
     * @return Response
     * @throws Exception
     */
    public function destroy($project_key, $parameter_id)
    {
        $project = Persistence::getProject($project_key, Input::get('key', 'id'));
        $project->parameters->forget($parameter_id);
        Persistence::saveProject($project);
        return 'true';
    }
}
