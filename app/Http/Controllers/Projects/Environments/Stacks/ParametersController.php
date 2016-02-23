<?php namespace Highcore\Http\Controllers\Projects\Environments\Stacks;

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
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}/parameters",
     *     summary="All stack parameters",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Parameters"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @param  int|string  $stack_key
     * @return Response
     */
    public function index($project_key, $environment_key, $stack_key)
    {
        $key = $stack_key;
        return Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'))->parameters;
    }

    /**
     * Store a newly created resource in storage.
     * @SWG\Post(
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}/parameters",
     *     summary="Store stack parameter",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Parameter(ref="#/parameters/source"),
     *     @SWG\Parameter(ref="#/parameters/Parameter"),
     *     @SWG\Response(response="default", ref="#/responses/Parameters"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @param  int|string  $stack_key
     * @return Response
     * @throws Exception
     */
    public function store($project_key, $environment_key, $stack_key)
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
        $key = $stack_key;
        $stack = Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'));
        $stack->parameters->put($parameter->id, $parameter);
        Persistence::saveStack($stack);
        return $parameter;
    }

    /**
     * Display the specified resource.
     * @SWG\Get(
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}/parameters/{parameter_id}",
     *     summary="Display stack parameter",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/parameter_id"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Parameters"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @param  int|string  $stack_key
     * @param  string  $parameter_id
     * @return Response
     */
    public function show($project_key, $environment_key, $stack_key, $parameter_id)
    {
        $key = $stack_key;
        $stack = Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'));
        $parameters = $stack->parameters;
        if (!$parameters->has($parameter_id)) {abort(404);}
        return $parameters->get($parameter_id);
    }

    /**
     * Update the specified resource in storage.
     * @SWG\Put(
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}/parameters/{parameter_id}",
     *     summary="Update stack parameter",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/parameter_id"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Parameter(ref="#/parameters/Parameter"),
     *     @SWG\Response(response="default", ref="#/responses/Parameters"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @param  int|string  $stack_key
     * @param  string  $parameter_id
     * @return Response
     * @throws Exception
     */
    public function update($project_key, $environment_key, $stack_key, $parameter_id)
    {
        $key = $stack_key;
        $stack = Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'));
        $parameters = $stack->parameters;
        if (!$parameters->has($parameter_id)) {abort(404);}
        /** @var Parameter $parameter */
        $parameter = $parameters->get($parameter_id);
        $parameter->fill(Input::only('value', 'sensitive'));
        Persistence::saveStack($stack);
        return $parameter;
    }

    /**
     * Destroy stack parameter
     * @SWG\Delete(
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}/parameters/{parameter_id}",
     *     summary="Delete stack parameter",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/parameter_id"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Bool"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @param  int|string  $stack_key
     * @param  string  $parameter_id
     * @return Response
     * @throws Exception
     */
    public function destroy($project_key, $environment_key, $stack_key, $parameter_id)
    {
        $key = $stack_key;
        $stack = Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'));
        $stack->parameters->forget($parameter_id);
        Persistence::saveStack($stack);
        return 'true';
    }
}
