<?php namespace Highcore\Http\Controllers\Templates;

use Highcore\Http\Controllers\Controller;
use Persistence;
use Response;

class StacksController extends Controller {

    /**
     * Display a listing of the resource.
     * @SWG\Get(
     *     path="/templates/{template_id}/stacks",
     *     summary="All stacks of a project",
     *     @SWG\Parameter(ref="#/parameters/template_id"),
     *     @SWG\Response(response="default", ref="#/responses/Stacks"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int  $template_id
     * @return Response
     */
    public function index($template_id)
    {
        $template_key = $template_id;
        return Persistence::getStacks(compact('template_key'));
    }

}
