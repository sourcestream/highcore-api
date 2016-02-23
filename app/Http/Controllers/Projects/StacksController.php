<?php namespace Highcore\Http\Controllers\Projects;

use Highcore\Http\Controllers\Controller;
use Persistence;
use Response;
use Input;

class StacksController extends Controller {

    /**
     * Display a listing of the resource.
     * @SWG\Get(
     *     path="/projects/{project_key}/stacks",
     *     summary="All stacks of a project",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Stacks"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @return Response
     */
    public function index($project_key)
    {
        return Persistence::getStacks(compact('project_key'), Input::get('key', 'id'));
    }

}
