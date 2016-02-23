<?php namespace Highcore\Http\Controllers\Templates;

use CloudFormer;
use Persistence;
use Response;
use Highcore\Models\Component;
use Highcore\Http\Controllers\Controller;

class ComponentsController extends Controller {

	/**
	 * Display a listing of the resource.
     * @SWG\Get(
     *     path="/templates/{template_id}/components",
     *     summary="All components",
     *     @SWG\Parameter(ref="#/parameters/template_id"),
     *     @SWG\Response(response="default", ref="#/responses/Components"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int  $template_id
	 * @return Response
	 */
	public function index($template_id)
	{
        return CloudFormer::getTemplateComponents(
            Persistence::getTemplate($template_id)
        );
	}

    /**
     * Display the specified resource.
     * @SWG\Get(
     *     path="/templates/{template_id}/components/{component_id}",
     *     summary="Display component",
     *     @SWG\Parameter(ref="#/parameters/template_id"),
     *     @SWG\Parameter(ref="#/parameters/component_id"),
     *     @SWG\Response(response="default", ref="#/responses/Component"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int     $template_id
     * @param  string  $component_id
     * @return Response
     */
    public function show($template_id, $component_id)
	{
        $components = CloudFormer::getTemplateComponents(
            Persistence::getTemplate($template_id))->keyBy(function(Component $item) {return $item->id;}
        );
        if (!$components->has($component_id)) {abort(404);}
        return $components->get($component_id);
	}

}
