<?php namespace Highcore\Http\Controllers\Stacks;

use Highcore\Http\Controllers\Controller;
use Highcore\Http\Requests;
use Highcore\Models\Component;
use Highcore\Models\Parameter;
use Input;
use Response;
use Persistence;
use Exception;

class ComponentsParametersController extends Controller {

    /**
     * Display a listing of the resource.
     * @SWG\Get(
     *     path="/stacks/{stack_id}/components/{component_id}/parameters",
     *     summary="All parameters",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Response(response="default", ref="#/responses/Parameters"),
     * )
     * @param int $stack_id  Stack Id
     * @param  string  $component_id
     * @return Response
     */
    public function index($stack_id, $component_id)
    {
        $stack = Persistence::getStack($stack_id);
        if (!$stack->components->has($component_id)) {abort(404);}
        return $stack->get("components.$component_id")->parameters;
    }

    /**
     * Add a parameter to a stack component
     * @SWG\Post(
     *     path="/stacks/{stack_id}/components/{component_id}/parameters",
     *     summary="Store component parameter",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Parameter(ref="#/parameters/Parameter"),
     *     @SWG\Response(response="default", ref="#/responses/Parameter"),
     * )
     * @param int $stack_id  Stack Id
     * @param  string  $component_id
     * @return Response
     * @throws Exception
     */
    public function store($stack_id, $component_id)
    {
        $stack = Persistence::getStack($stack_id);
        if (!$stack->components->has($component_id)) {abort(404);}
        /** @var Component $component */
        $component = $stack->get("components.$component_id");
        $parameters = $component->parameters;
        /** @var Parameter $parameter */
        $parameter = Parameter::make()->fill(Input::only(['id', 'value', 'sensitive']));
        $parameters->put($parameter->id, $parameter);
        Persistence::saveStack($stack);
        return $parameter;
    }

    /**
     * Display the specified resource.
     * @SWG\Get(
     *     path="/stacks/{stack_id}/components/{component_id}/parameters/{parameter_id}",
     *     summary="Display parameter",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Parameter(ref="#/parameters/parameter_id"),
     *     @SWG\Response(response="default", ref="#/responses/Parameter"),
     * )
     * @param  int  $stack_id
     * @param  string  $component_id
     * @param  string  $parameter_id
     * @return Response
     */
    public function show($stack_id, $component_id, $parameter_id)
    {
        $stack = Persistence::getStack($stack_id);
        if (!$stack->components->has($component_id)) {abort(404);}
        /** @var Component $component */
        $component = $stack->get("components.$component_id");
        $parameters = $component->parameters;
        if (!$parameters->has($parameter_id)) {abort(404);}
        return $parameters->get($parameter_id);
    }

    /**
     * Set stack component parameter value
     * @SWG\Put(
     *     path="/stacks/{stack_id}/components/{component_id}/parameters/{parameter_id}",
     *     summary="Update parameter",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Parameter(ref="#/parameters/parameter_id"),
     *     @SWG\Parameter(ref="#/parameters/Parameter"),
     *     @SWG\Response(response="default", ref="#/responses/Parameter"),
     * )
     * @param int $stack_id  Stack Id
     * @param  string  $component_id
     * @param  string  $parameter_id
     * @return Response
     * @throws Exception
     */
	public function update($stack_id, $component_id, $parameter_id)
	{
        $stack = Persistence::getStack($stack_id);
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
     * Destroy stack component parameter
     * @SWG\Delete(
     *     path="/stacks/{stack_id}/components/{component_id}/parameters/{parameter_id}",
     *     summary="Delete parameter",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Parameter(ref="#/parameters/parameter_id"),
     *     @SWG\Response(response="default", ref="#/responses/Bool"),
     * )
     * @param int $stack_id  Stack Id
     * @param string $component_id
     * @param string $parameter_id
     * @return Response
     * @throws Exception
     */
    public function destroy($stack_id, $component_id, $parameter_id)
    {
        $stack = Persistence::getStack($stack_id);
        if (!$stack->components->has($component_id)) {abort(404);}
        /** @var Component $component */
        $component = $stack->get("components.$component_id");
        $parameters = $component->parameters;
        $parameters->forget($parameter_id);
        Persistence::saveStack($stack);
        return 'true';
    }
}
