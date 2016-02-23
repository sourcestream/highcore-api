<?php namespace Highcore\Http\Controllers\Environments;

use Highcore\Http\Controllers\Controller;
use Persistence;
use Response;
use Input;

class StacksController extends Controller {

    /**
     * Display a listing of the resource.
     * @SWG\Get(
     *     path="/environments/{environment_id}/stacks",
     *     summary="All stacks of a project",
     *     @SWG\Parameter(ref="#/parameters/environment_id"),
     *     @SWG\Response(response="default", ref="#/responses/Stacks"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int  $environment_id
     * @return Response
     */
    public function index($environment_id)
    {
        $environment_key = $environment_id;
        return Persistence::getStacks(compact('environment_key'));
    }

}
