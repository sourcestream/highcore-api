<?php namespace Highcore\Http\Controllers\Projects;

use Highcore\Http\Controllers\Controller;
use Highcore\Models\Template;
use DB;
use Persistence;
use Response;
use Input;
use TemplateEngine;

class TemplatesController extends Controller {

    /**
     * Display a listing of the resource.
     * @SWG\Get(
     *     path="/projects/{project_key}/templates",
     *     summary="All templates of a project",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Templates"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @return Response
     */
    public function index($project_key)
    {
        return Persistence::getTemplates(compact('project_key'), Input::get('key', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     * @SWG\Post(
     *     path="/projects/{project_key}/templates",
     *     summary="Store project template",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Parameter(ref="#/parameters/Template"),
     *     @SWG\Response(response="default", ref="#/responses/Templates"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int|string  $project_key
     * @return Response
     */
    public function store($project_key)
    {
        $template = Template::make(Input::all())
            ->assign(Persistence::getProject(['key' => $project_key]), Input::get('key', 'id'));

        DB::transaction(function()use($template){
            $template = Persistence::saveTemplate($template);
            TemplateEngine::updateTemplate($template);
        });

        return $template;
    }

    /**
     * Display the specified resource.
     * @SWG\Get(
     *     path="/projects/{project_id}/templates/{template_id}",
     *     summary="Display project template",
     *     @SWG\Parameter(ref="#/parameters/project_id"),
     *     @SWG\Parameter(ref="#/parameters/template_id"),
     *     @SWG\Response(response="default", ref="#/responses/Templates"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int  $project_id
     * @param  int  $template_id
     * @return Response
     */
    public function show($project_id, $template_id)
    {
        $key = $template_id;
        $project_key = $project_id;
        return Persistence::getTemplate(compact('project_key', 'key'));
    }

    /**
     * Update the specified resource in storage.
     * @SWG\Put(
     *     path="/projects/{project_id}/templates/{template_id}",
     *     summary="Update project template",
     *     @SWG\Parameter(ref="#/parameters/project_id"),
     *     @SWG\Parameter(ref="#/parameters/template_id"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Parameter(ref="#/parameters/Template"),
     *     @SWG\Response(response="default", ref="#/responses/Templates"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int  $project_id
     * @param  int  $template_id
     * @return Response
     */
    public function update($project_id, $template_id)
    {
        $key = $template_id;
        $project_key = $project_id;
        $template = Persistence::getTemplate(compact('project_key', 'key'))->fill(Input::all());

        DB::transaction(function()use($template){
            $template = Persistence::saveTemplate($template);
            TemplateEngine::updateTemplate($template);
        });

        return $template;
    }

    /**
     * Remove the specified resource from storage.
     * @SWG\Delete(
     *     path="/projects/{project_id}/templates/{template_id}",
     *     summary="Delete project template",
     *     @SWG\Parameter(ref="#/parameters/project_id"),
     *     @SWG\Parameter(ref="#/parameters/template_id"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Templates"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param  int  $project_id
     * @param  int  $template_id
     * @return Response
     */
    public function destroy($project_id, $template_id)
    {
        $key = $template_id;
        $project_key = $project_id;
        Persistence::deleteTemplate(
            Persistence::getTemplate(compact('project_key', 'key'))
        );
        return 'true';
    }
}
