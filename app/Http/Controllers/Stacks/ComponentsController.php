<?php namespace Highcore\Http\Controllers\Stacks;

use Highcore\Http\Controllers\Controller;
use Highcore\Models\Component;
use Input;
use Route;
use Request;
use Response;
use Persistence;
use Exception;

class ComponentsController extends Controller {

    /**
     * Display a listing of the resource.
     * @SWG\Get(
     *     path="/stacks/{stack_id}/components",
     *     summary="All stack components",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Response(response="default", ref="#/responses/Components"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param int $stack_id  Stack Id
     * @return Response
     */
    public function index($stack_id)
    {
        return Persistence::getStack($stack_id)->components;
    }

    /**
     * Display the specified resource.
     * @SWG\Get(
     *     path="/stacks/{stack_id}/components/{component_id}",
     *     summary="Display stack component",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Response(response="default", ref="#/responses/Component"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int  $stack_id
     * @param  string  $component_id
     * @return Response
     */
    public function show($stack_id, $component_id)
    {
        $stack = Persistence::getStack($stack_id);
        $components = $stack->components;
        if (!$components->has($component_id)) {abort(404);}
        $component = $components->get($component_id);
        return $component;
    }

    /**
     * Add stack component
     * @SWG\Post(
     *     path="/stacks/{stack_id}/components",
     *     summary="Store stack component",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Parameter(ref="#/parameters/source"),
     *     @SWG\Parameter(ref="#/parameters/Component"),
     *     @SWG\Response(response="default", ref="#/responses/Component"),
     *     security={{"highcore_auth":{}}},
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
            /** @var Component $project */
            $component = Route::dispatch($request)->getOriginalContent();
        } else {
            $component = Component::make();
        }
        $component->fill($input);
        $stack = Persistence::getStack($stack_id);
        $components = $stack->components;
        $components->put($component->id, $component);
        Persistence::saveStack($stack);
        return $component;
    }

    /**
     * Set stack component value
     * @SWG\Put(
     *     path="/stacks/{stack_id}/components/{component_id}",
     *     summary="Update stack component",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Parameter(ref="#/parameters/Component"),
     *     @SWG\Response(response="default", ref="#/responses/Component"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param int $id  Stack Id
     * @param string  $component_id
     * @return Response
     * @throws Exception
     */
    public function update($id, $component_id)
    {
        $stack = Persistence::getStack($id);
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
     *     path="/stacks/{stack_id}/components/{component_id}",
     *     summary="Delete stack component",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Response(response="default", ref="#/responses/Bool"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param int $id  Stack Id
     * @param string $component_id
     * @return Response
     * @throws Exception
     */
    public function destroy($id, $component_id)
    {
        $stack = Persistence::getStack($id);
        $components = $stack->components;
        $components->forget($component_id);
        Persistence::saveStack($stack);
        return 'true';
    }
}
