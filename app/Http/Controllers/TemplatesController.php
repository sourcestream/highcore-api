<?php namespace Highcore\Http\Controllers;

use Highcore\Models\Template;
use DB;
use TemplateEngine;
use Persistence;
use Input;
use Response;

/**
 * @SWG\Parameter(
 *     name="template_id",
 *     in="path",
 *     required=true,
 *     type="integer",
 * )
 * @SWG\Parameter(
 *     name="Template",
 *     in="body",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/Template")
 * )
 * @SWG\Response(
 *     response="Templates",
 *     description="Array of Templates",
 *     @SWG\Schema(
 *         type="array",
 *         @SWG\Items(ref="#/definitions/Template")
 *     ),
 * )
 * @SWG\Response(
 *     response="Template",
 *     description="Template",
 *     @SWG\Schema(ref="#/definitions/Template"),
 * )
 */
class TemplatesController extends Controller {

	/**
	 * Display a listing of the resource.
     * @SWG\Get(
     *     path="/templates",
     *     summary="All templates",
     *     @SWG\Response(response="default", ref="#/responses/Templates"),
     * )
	 * @return Response
	 */
	public function index()
	{
        return Persistence::getTemplates();
	}

	/**
	 * Store a newly created resource in storage.
     * @SWG\Post(
     *     path="/templates",
     *     summary="Store template",
     *     @SWG\Parameter(ref="#/parameters/template_id"),
     *     @SWG\Parameter(ref="#/parameters/Template"),
     *     @SWG\Response(response="default", ref="#/responses/Template"),
     * )
	 * @return Response
	 */
	public function store()
	{
		$template = Template::make(Input::all());

        DB::transaction(function()use($template){
            $template = Persistence::saveTemplate($template);
            TemplateEngine::updateTemplate($template);
        });

		return $template;
	}

	/**
	 * Display the specified resource.
     * @SWG\Get(
     *     path="/templates/{template_id}",
     *     summary="Display template",
     *     @SWG\Parameter(ref="#/parameters/template_id"),
     *     @SWG\Response(response="default", ref="#/responses/Template"),
     * )
	 * @param  int  $template_id
	 * @return Response
	 */
	public function show($template_id)
	{
        return Persistence::getTemplate($template_id);
	}

	/**
	 * Update the specified resource in storage.
     * @SWG\Put(
     *     path="/templates/{template_id}",
     *     summary="Update template",
     *     @SWG\Parameter(ref="#/parameters/template_id"),
     *     @SWG\Parameter(ref="#/parameters/Template"),
     *     @SWG\Response(response="default", ref="#/responses/Template"),
     * )
	 * @param  int  $template_id
	 * @return Response
	 */
	public function update($template_id)
	{
		$template = Persistence::getTemplate($template_id)->fill(Input::all());

        DB::transaction(function()use($template){
            $template = Persistence::saveTemplate($template);
            TemplateEngine::updateTemplate($template);
        });

		return $template;
	}

	/**
	 * Remove the specified resource from storage.
     * @SWG\Delete(
     *     path="/environments/{template_id}",
     *     summary="Delete environment",
     *     @SWG\Parameter(ref="#/parameters/template_id"),
     *     @SWG\Response(response="default", ref="#/responses/Bool"),
     * )
	 * @param  int  $template_id
	 * @return Response
	 */
	public function destroy($template_id)
	{
        Persistence::deleteTemplate(
            Persistence::getTemplate($template_id)
        );
        return 'true';
	}

}
