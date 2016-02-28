<?php namespace Highcore\Http\Controllers;

use CloudFormer;
use DB;
use Highcore\Models\Stack;
use Persistence;
use Request;
use Route;
use Input;
use Response;

/**
 * @SWG\Parameter(
 *     name="stack_id",
 *     description="Stack Id",
 *     in="path",
 *     required=true,
 *     type="integer",
 * )
 * @SWG\Parameter(
 *     name="stack_key",
 *     description="Stack Key",
 *     in="path",
 *     required=true,
 *     type="string",
 * )
 * @SWG\Parameter(
 *     name="provision",
 *     description="Do provision a stack to the cloud",
 *     in="query",
 *     required=false,
 *     type="boolean",
 * )
 * @SWG\Parameter(
 *     name="deprovision",
 *     description="Do remove a stack from the cloud",
 *     in="query",
 *     required=false,
 *     type="boolean",
 * )
 * @SWG\Parameter(
 *     name="source",
 *     description="Use another data source as a template",
 *     in="query",
 *     required=false,
 *     type="string",
 * )
 * @SWG\Parameter(
 *     name="Stack",
 *     in="body",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/Stack")
 * )
 * @SWG\Response(
 *     response="Stacks",
 *     description="Array of Stacks",
 *     @SWG\Schema(
 *         type="array",
 *         @SWG\Items(ref="#/definitions/Stack")
 *     ),
 * )
 * @SWG\Response(
 *     response="Stack",
 *     description="Stack",
 *     @SWG\Schema(ref="#/definitions/Stack"),
 * )
 */
class StacksController extends Controller {

	/**
	 * Display a listing of the resource.
     * @SWG\Get(
     *     path="/stacks",
     *     summary="All stacks",
     *     @SWG\Response(response="default", ref="#/responses/Stacks"),
     *     security={{"highcore_auth":{}}},
     * )
	 * @return Response
	 */
	public function index()
	{
        return Persistence::getStacks();
	}

	/**
	 * Store a newly created resource in storage.
     * @SWG\Post(
     *     path="/stacks/{stack_id}",
     *     summary="Store stack",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Parameter(ref="#/parameters/source"),
     *     @SWG\Parameter(ref="#/parameters/provision"),
     *     @SWG\Parameter(ref="#/parameters/deprovision"),
     *     @SWG\Parameter(ref="#/parameters/Stack"),
     *     @SWG\Response(response="default", ref="#/responses/Stack"),
     *     security={{"highcore_auth":{}}},
     * )
	 * @return Response
	 */
	public function store()
	{
        $source = Input::get('source', false);
        if ($source) {
            $request = Request::create($source, 'GET');
            /** @var Stack $stack */
            $stack = Route::dispatch($request)->getOriginalContent()->fill(Input::all());
            $stack->id = null;
        } else {
            $stack = Stack::make(Input::all());
        }
        $stack->assign(Persistence::getEnvironment($stack->environment_id))
            ->assign(Persistence::getTemplate($stack->template_id));

        if ($stack->stacks) {
            $environment_key = $stack->environment->name;
            $project_key = $stack->environment->project->name;
            $key = ['operator' => 'In', 'values' => $stack->stacks->pluck('name')->all()];
            $stack->stacks = Persistence::getStacks(compact('project_key', 'environment_key', 'key'), 'name');
        }

        $provision = Input::get('provision', false);
        DB::transaction(function() use($stack, $provision)
        {
            $stack->provisioned = false;
            Persistence::saveStack($stack);
            if($provision) {
                CloudFormer::createStack($stack);
                $stack->provisioned = true;
                Persistence::saveStack($stack);
            }
        });

        return $stack->toArray(false);
	}

	/**
	 * Display the specified resource.
     * @SWG\Get(
     *     path="/stacks/{stack_id}",
     *     summary="Display stack",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Response(response="default", ref="#/responses/Stack"),
     *     security={{"highcore_auth":{}}},
     * )
	 * @param  int  $stack_id
	 * @return Response
	 */
	public function show($stack_id)
	{
        $stack = Persistence::getStack($stack_id);

        $environment_key = $stack->environment->name;
        $project_key = $stack->environment->project->name;
        $key = ['operator' => 'In', 'values' => $stack->stacks->pluck('name')->all()];
        $stack->stacks = Persistence::getStacks(compact('project_key', 'environment_key', 'key'), 'name');

        CloudFormer::describeStack($stack);

        return $stack;
	}

	/**
	 * Update the specified resource in storage.
     * @SWG\Put(
     *     path="/stacks/{stack_id}",
     *     summary="Update stack",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Parameter(ref="#/parameters/provision"),
     *     @SWG\Parameter(ref="#/parameters/deprovision"),
     *     @SWG\Parameter(ref="#/parameters/Stack"),
     *     @SWG\Response(response="default", ref="#/responses/Stack"),
     *     security={{"highcore_auth":{}}},
     * )
	 * @param  int  $stack_id
	 * @return Response
	 */
	public function update($stack_id)
	{
        $stack = Persistence::getStack($stack_id);
        $stack->fill(Input::all());

        if ($stack->stacks) {
            $environment_key = $stack->environment->name;
            $project_key = $stack->environment->project->name;
            $key = ['operator' => 'In', 'values' => $stack->stacks->pluck('name')->all()];
            $stack->stacks = Persistence::getStacks(compact('project_key', 'environment_key', 'key'), 'name');
        }

        $provision = Input::get('provision', false);
        $deprovision = Input::get('deprovision', false);
        DB::transaction(function() use($stack, $provision, $deprovision)
        {
            Persistence::saveStack($stack); //make sure the update will be possible
            if ($provision) {
                if((bool) $stack->provisioned) { //was provisioned before
                    CloudFormer::updateStack($stack);
                } else {
                    CloudFormer::createStack($stack);
                }
                $stack->provisioned = true;
            } else if ($deprovision && (bool) $stack->provisioned) {
                CloudFormer::deleteStack($stack);
                $stack->provisioned = false;
            }
            Persistence::saveStack($stack); //update the `provisioned` flag
        });
        return $stack->toArray(false);
	}

	/**
	 * Remove the specified resource from storage.
     * @SWG\Delete(
     *     path="/stacks/{stack_id}",
     *     summary="Delete stack",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Response(response="default", ref="#/responses/Bool"),
     *     security={{"highcore_auth":{}}},
     * )
	 * @param  int  $stack_id
	 * @return Response
	 */
	public function destroy($stack_id)
	{
        $stack = Persistence::getStack($stack_id);
        DB::transaction(function() use($stack)
        {
            Persistence::deleteStack($stack);
            CloudFormer::deleteStack($stack);
        });
        return 'true';
	}

}
