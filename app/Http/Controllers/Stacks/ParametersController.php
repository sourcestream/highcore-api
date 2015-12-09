<?php namespace Highcore\Http\Controllers\Stacks;

use Highcore\Http\Controllers\Controller;
use Highcore\Models\Parameter;
use Input;
use Route;
use Request;
use Response;
use Persistence;
use Exception;

class ParametersController extends Controller {

    /**
     * Display a listing of the resource.
     * @SWG\Get(
     *     path="/stacks/{stack_id}/parameters",
     *     summary="All stack parameters",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Response(response="default", ref="#/responses/Parameters"),
     * )
     * @param int $stack_id  Stack Id
     * @return Response
     */
    public function index($stack_id)
    {
        return Persistence::getStack($stack_id)->parameters;
    }

    /**
     * Display the specified resource.
     * @SWG\Get(
     *     path="/stacks/{stack_id}/parameters/{parameter_id}",
     *     summary="Display stack parameter",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Parameter(ref="#/parameters/parameter_id"),
     *     @SWG\Response(response="default", ref="#/responses/Parameter"),
     * )
     * @param  int  $stack_id
     * @param  string  $parameter_id
     * @return Response
     */
    public function show($stack_id, $parameter_id)
    {
        $stack = Persistence::getStack($stack_id);
        $parameters = $stack->parameters;
        if (!$parameters->has($parameter_id)) {abort(404);}
        $parameter = $parameters->get($parameter_id);
        return $parameter;
    }

    /**
     * Add stack parameter
     * @SWG\Post(
     *     path="/stacks/{stack_id}/parameters",
     *     summary="Store stack parameter",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Parameter(ref="#/parameters/source"),
     *     @SWG\Parameter(ref="#/parameters/Parameter"),
     *     @SWG\Response(response="default", ref="#/responses/Parameter"),
     * )
     * @param int $stack_id  Stack Id
     * @return Response
     * @throws Exception
     */
    public function store($stack_id)
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
        $stack = Persistence::getStack($stack_id);
        $parameters = $stack->parameters;
        $parameters->put($parameter->id, $parameter);
        Persistence::saveStack($stack);
        return $parameter;
    }

    /**
     * Set stack parameter value
     * @SWG\Put(
     *     path="/stacks/{stack_id}/parameters/{parameter_id}",
     *     summary="Update stack parameter",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Parameter(ref="#/parameters/parameter_id"),
     *     @SWG\Parameter(ref="#/parameters/Parameter"),
     *     @SWG\Response(response="default", ref="#/responses/Parameter"),
     * )
     * @param int $id  Stack Id
     * @param string  $parameter_id
     * @return Response
     * @throws Exception
     */
    public function update($id, $parameter_id)
    {
        $stack = Persistence::getStack($id);
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
     *     path="/stacks/{stack_id}/parameters/{parameter_id}",
     *     summary="Delete stack parameter",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Parameter(ref="#/parameters/parameter_id"),
     *     @SWG\Response(response="default", ref="#/responses/Bool"),
     * )
     * @param int $id  Stack Id
     * @param string $parameter_id
     * @return Response
     * @throws Exception
     */
    public function destroy($id, $parameter_id)
    {
        $stack = Persistence::getStack($id);
        $parameters = $stack->parameters;
        $parameters->forget($parameter_id);
        Persistence::saveStack($stack);
        return 'true';
    }
}
