<?php namespace Highcore\Http\Controllers\Projects\Environments\Stacks;

use Highcore\Http\Controllers\Controller;
use Highcore\Models\Parameter;
use Route;
use Request;
use Response;
use Input;
use Persistence;
use Exception;

class ComponentsController extends Controller {

    /**
     * Display a listing of the resource.
     * @SWG\Get(
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}/components",
     *     summary="All stack components",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Components"),
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
        return Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'))->components;
    }

    /**
     * Store a newly created resource in storage.
     * @SWG\Post(
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}/components",
     *     summary="Store stack component",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Parameter(ref="#/parameters/source"),
     *     @SWG\Parameter(ref="#/parameters/Parameter"),
     *     @SWG\Response(response="default", ref="#/responses/Components"),
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
            $component = Route::dispatch($request)->getOriginalContent();
        } else {
            $component = Parameter::make();
        }
        $component->fill($input);
        $key = $stack_key;
        $stack = Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'));
        $stack->components->put($component->id, $component);
        Persistence::saveStack($stack);
        return $component;
    }

    /**
     * Display the specified resource.
     * @SWG\Get(
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}/components/{component_id}",
     *     summary="Display stack component",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Components"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @param  int|string  $stack_key
     * @param  string  $component_id
     * @return Response
     */
    public function show($project_key, $environment_key, $stack_key, $component_id)
    {
        $key = $stack_key;
        $stack = Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'));
        $components = $stack->components;
        if (!$components->has($component_id)) {abort(404);}
        return $components->get($component_id);
    }

    /**
     * Update the specified resource in storage.
     * @SWG\Put(
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}/components/{component_id}",
     *     summary="Update stack component",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Parameter(ref="#/parameters/Parameter"),
     *     @SWG\Response(response="default", ref="#/responses/Components"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @param  int|string  $stack_key
     * @param  string  $component_id
     * @return Response
     * @throws Exception
     */
    public function update($project_key, $environment_key, $stack_key, $component_id)
    {
        $key = $stack_key;
        $stack = Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'));
        $components = $stack->components;
        if (!$components->has($component_id)) {abort(404);}
        /** @var Component $component */
        $component = $components->get($component_id);
        $component->fill(Input::only('value', 'sensitive'));
        Persistence::saveStack($stack);
        return $component;
    }

    /**
     * Destroy stack component
     * @SWG\Delete(
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}/components/{component_id}",
     *     summary="Delete stack component",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Bool"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @param  int|string  $stack_key
     * @param  string  $component_id
     * @return Response
     * @throws Exception
     */
    public function destroy($project_key, $environment_key, $stack_key, $component_id)
    {
        $key = $stack_key;
        $stack = Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'));
        $stack->components->forget($component_id);
        Persistence::saveStack($stack);
        return 'true';
    }
}
