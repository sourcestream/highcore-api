<?php namespace Highcore\Http\Controllers\Stacks;

use Highcore\Http\Controllers\Controller;
use Persistence;
use Response;
use CloudFormer;
use Input;
use Exception;

/**
 * @SWG\Parameter(
 *     name="format",
 *     in="path",
 *     required=true,
 *     type="string",
 *     enum={"cloudformation"}
 * )
 * @SWG\Parameter(
 *     name="diff",
 *     in="query",
 *     required=true,
 *     type="boolean",
 * )
 */
class TemplatesController extends Controller {

    /**
     * Show generated stack template
     * @SWG\Get(
     *     path="/stacks/{stack_id}/templates/{format}",
     *     summary="Display generated stack template in required format",
     *     @SWG\Parameter(ref="#/parameters/stack_id"),
     *     @SWG\Parameter(ref="#/parameters/format"),
     *     @SWG\Parameter(ref="#/parameters/diff"),
     *     @SWG\Response(response="default", ref="#/responses/Json"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param int $stack_id  Stack Id
     * @param string $format Template engine to use for template generation
     * @return Response
     * @throws Exception
     */
	public function show($stack_id, $format)
	{
        $diff = Input::get('diff', false);
        $stack = Persistence::getStack($stack_id);

        if ($stack->stacks) {
            $environment_key = $stack->environment->name;
            $project_key = $stack->environment->project->name;
            $key = ['operator' => 'In', 'values' => $stack->stacks->pluck('name')->all()];
            $stack->stacks = Persistence::getStacks(compact('project_key', 'environment_key', 'key'), 'name');
        }

        switch ($format) {
            case 'cloudformation':
                $template = $diff
                    ? CloudFormer::diffTemplate($stack)
                    : CloudFormer::createTemplate($stack);
                break;
            default:
                throw new Exception('Unknown template format');
        }

        return $diff
            ? response($template, 200, ['Content-Type' => 'text'])
            : response()->json(json_decode($template), 200, [], JSON_PRETTY_PRINT);
	}

}
