<?php namespace Highcore\Http\Controllers\Projects\Environments\Stacks;

use Highcore\Http\Controllers\Controller;
use Highcore\Models\Component;
use Highcore\Models\Parameter;
use Response;
use Input;
use Persistence;
use Exception;

class ComponentsParametersController extends Controller {

    /**
     * Display a listing of the resource.
     * @SWG\Get(
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}/components/{component_id}/parameters",
     *     summary="All component parameters",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Parameters"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @param  int|string  $stack_key
     * @param  string  $component_id
     * @return Response
     */
    public function index($project_key, $environment_key, $stack_key, $component_id)
    {
        $key = $stack_key;
        $stack = Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'));
        if (!$stack->components->has($component_id)) {abort(404);}
        return $stack->get("components.$component_id")->parameters;
    }

    /**
     * Store a newly created resource in storage.
     * @SWG\Post(
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}/components/{component_id}/parameters",
     *     summary="Store component parameter",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Parameter(ref="#/parameters/Parameter"),
     *     @SWG\Response(response="default", ref="#/responses/Parameters"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @param  int|string  $stack_key
     * @param  string  $component_id
     * @return Response
     * @throws Exception
     */
    public function store($project_key, $environment_key, $stack_key, $component_id)
    {
        $key = $stack_key;
        $stack = Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'));
        if (!$stack->components->has($component_id)) {abort(404);}
        $parameter = Parameter::make()->fill(Input::only(['id', 'value', 'sensitive']));
        $stack->get("components.$component_id")->parameters->put($parameter->id, $parameter);
        Persistence::saveStack($stack);
        return $parameter;
    }

    /**
     * Display the specified resource.
     * @SWG\Get(
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}/components/{component_id}/parameters/{parameter_id}",
     *     summary="Display component parameter",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Parameter(ref="#/parameters/parameter_id"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Parameters"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @param  int|string  $stack_key
     * @param  string  $component_id
     * @param  string  $parameter_id
     * @return Response
     */
    public function show($project_key, $environment_key, $stack_key, $component_id, $parameter_id)
    {
        $key = $stack_key;
        $stack = Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'));
        if (!$stack->components->has($component_id)) {abort(404);}
        /** @var Component $component */
        $component = $stack->get("components.$component_id");
        $parameters = $component->parameters;
        if (!$parameters->has($parameter_id)) {abort(404);}
        return $parameters->get($parameter_id);
    }

    /**
     * Update the specified resource in storage.
     * @SWG\Put(
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}/components/{component_id}/parameters/{parameter_id}",
     *     summary="Update component parameter",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Parameter(ref="#/parameters/parameter_id"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Parameter(ref="#/parameters/Parameter"),
     *     @SWG\Response(response="default", ref="#/responses/Parameters"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @param  int|string  $stack_key
     * @param  string  $component_id
     * @param  string  $parameter_id
     * @return Response
     * @throws Exception
     */
    public function update($project_key, $environment_key, $stack_key, $component_id, $parameter_id)
    {
        $key = $stack_key;
        $stack = Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'));
        if (!$stack->components->has($component_id)) {abort(404);}
        /** @var Component $component */
        $component = $stack->get("components.$component_id");
        $parameters = $component->parameters;
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
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}/components/{component_id}/parameters/{parameter_id}",
     *     summary="Delete component parameter",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Parameter(ref="#/parameters/parameter_id"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Bool"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @param  int|string  $stack_key
     * @param  string  $component_id
     * @param  string  $parameter_id
     * @return Response
     * @throws Exception
     */
    public function destroy($project_key, $environment_key, $stack_key, $component_id, $parameter_id)
    {
        $key = $stack_key;
        $stack = Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'));
        if (!$stack->components->has($component_id)) {abort(404);}
        $stack->$parameters->forget($parameter_id);
        Persistence::saveStack($stack);
        return 'true';
    }
}

