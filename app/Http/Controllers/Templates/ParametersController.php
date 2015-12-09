<?php namespace Highcore\Http\Controllers\Templates;

use CloudFormer;
use Highcore\Http\Controllers\Controller;
use Persistence;
use Response;

class ParametersController extends Controller {

	/**
	 * Display a listing of the resource.
     * @SWG\Get(
     *     path="/templates/{template_id}/parameters",
     *     summary="All template parameters",
     *     @SWG\Parameter(ref="#/parameters/template_id"),
     *     @SWG\Response(response="default", ref="#/responses/Parameters"),
     * )
     * @param  int  $template_id
	 * @return Response
	 */
	public function index($template_id)
	{
        return CloudFormer::getTemplateParams(
            Persistence::getTemplate($template_id)
        );
	}

    /**
     * Display the specified resource.
     * @SWG\Get(
     *     path="/templates/{template_id}/parameters/{parameter_path}",
     *     summary="Display template parameter",
     *     @SWG\Parameter(ref="#/parameters/template_id"),
     *     @SWG\Parameter(ref="#/parameters/parameter_path"),
     *     @SWG\Response(response="default", ref="#/responses/Parameter"),
     * )
     * @param  int     $template_id
     * @param  string  $parameter_path
     * @return Response
     */
    public function show($template_id, $parameter_path)
	{
        return CloudFormer::getTemplateParams(
            Persistence::getTemplate($template_id), $key_by = 'id'
        )->get($parameter_path);
	}

}
