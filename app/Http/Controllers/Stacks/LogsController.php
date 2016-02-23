<?php namespace Highcore\Http\Controllers\Stacks;

use Highcore\Http\Controllers\Controller;
use Persistence;
use CloudFormer;
use Input;
use Response;

/**
 * @SWG\Definition(definition="StackLogEntry",
 *   @SWG\Property(property="timestamp", type="integer"),
 *   @SWG\Property(property="event_id", type="string"),
 *   @SWG\Property(property="logical_resource_id", type="string"),
 *   @SWG\Property(property="component_name", type="string"),
 *   @SWG\Property(property="environment_name", type="string"),
 *   @SWG\Property(property="project_name", type="string"),
 *   @SWG\Property(property="status", ref="#/definitions/AwsStatus")
 * )
 * @SWG\Response(
 *     response="StackLogs",
 *     description="Stack Logs",
 *     @SWG\Schema(
 *         type="array",
 *         @SWG\Items(ref="#/definitions/StackLogEntry")
 *     ),
 * )
 */
class LogsController extends Controller {

	/**
	 * Display a listing of the resource.
     *
     * @SWG\Get(
     *     path="/stacks/{stack_id}/logs",
     *     summary="Stack logs",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Response(response="default", ref="#/responses/StackLogs"),
     *     security={{"highcore_auth":{}}},
     * )
	 * @param int $stack_id
	 * @return Response
	 */
	public function index($stack_id)
	{
        $stack = Persistence::getStack($stack_id);
        if (!$stack->provisioned) { abort(404); }
        $events = CloudFormer::getEvents($stack, Input::get('nextToken'));

        return ['logs' => $events->toArray()] + array_merge_recursive($this->hateoasNextPage($events->nextToken), $this->hateoasSelf());
	}

}
