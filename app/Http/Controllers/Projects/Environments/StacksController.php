<?php namespace Highcore\Http\Controllers\Projects\Environments;

use Highcore\Http\Controllers\Controller;
use Input;
use Persistence;
use Request;
use Response;
use Route;

class StacksController extends Controller {

	/**
	 * Display a listing of the resource.
     * @SWG\Get(
     *     path="/projects/{project_key}/environments/{environment_key}/stacks",
     *     summary="All environment stacks",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Stacks"),
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     *
	 * @return Response
	 */
	public function index($project_key, $environment_key)
	{
        $stacks = Persistence::getStacks(compact('project_key', 'environment_key'), Input::get('key', 'id'));
        return $stacks;
	}

    /**
     * Remove the specified resource from storage.
     * @SWG\Get(
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}",
     *     summary="Display stack",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Stacks"),
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @param  int|string  $stack_key
     *
     * @return Response
     */
    public function show($project_key, $environment_key, $stack_key)
    {
        $key = $stack_key;
        $stack = Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'));
        return $stack;
    }

    /**
     * Remove the specified resource from storage.
     * @SWG\Put(
     *     path="/projects/{project_key}/environments/{environment_key}/stacks/{stack_key}",
     *     summary="Update stack",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/environment_key"),
     *     @SWG\Parameter(ref="#/parameters/stack_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Parameter(ref="#/parameters/Stack"),
     *     @SWG\Response(response="default", ref="#/responses/Stacks"),
     * )
     * @param  int|string  $project_key
     * @param  int|string  $environment_key
     * @param  int|string  $stack_key
     *
     * @return Response
     */
    public function update($project_key, $environment_key, $stack_key)
    {
        $key = $stack_key;
        $stack = Persistence::getStack(compact('project_key', 'environment_key', 'key'), Input::get('key', 'id'));
        $request = Request::create('/stacks/'.$stack->id, Route::getCurrentRequest()->method(), Input::all());
        $result = Route::dispatch($request);
        return $result;
    }
}
