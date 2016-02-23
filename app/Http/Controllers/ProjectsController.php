<?php namespace Highcore\Http\Controllers;

use Highcore\Models\Project;
use Route;
use Request;
use Response;
use Input;
use Persistence;

/**
 * @SWG\Parameter(
 *     name="project_id",
 *     description="Project Id",
 *     in="path",
 *     required=true,
 *     type="integer",
 * )
 * @SWG\Parameter(
 *     name="project_key",
 *     description="Project Key",
 *     in="path",
 *     required=true,
 *     type="string",
 * )
 * @SWG\Parameter(
 *     name="Project",
 *     in="body",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/Project")
 * )
 * @SWG\Response(
 *     response="Projects",
 *     description="Array of Projects",
 *     @SWG\Schema(
 *         type="array",
 *         @SWG\Items(ref="#/definitions/Project")
 *     ),
 * )
 * @SWG\Response(
 *     response="Project",
 *     description="Project",
 *     @SWG\Schema(ref="#/definitions/Project"),
 * )
 */
class ProjectsController extends Controller {

	/**
	 * Display a listing of the resource.
     * @SWG\Get(
     *     path="/projects",
     *     summary="All projects",
     *     @SWG\Response(response="default", ref="#/responses/Projects"),
     *     security={{"highcore_auth":{}}},
     * )
	 * @return Response
	 */
	public function index()
	{
        return Persistence::getProjects();
	}

	/**
	 * Store a newly created resource in storage.
     * @SWG\Post(
     *     path="/projects",
     *     summary="Store project",
     *     @SWG\Parameter(ref="#/parameters/source"),
     *     @SWG\Parameter(ref="#/parameters/Project"),
     *     @SWG\Response(response="default", ref="#/responses/Project"),
     *     security={{"highcore_auth":{}}},
     * )
	 * @return Response
	 */
	public function store()
	{
        $source = Input::get('source', false);
        if ($source) {
            $request = Request::create($source, 'GET');
            /** @var Project $project */
            $project = Route::dispatch($request)->getOriginalContent()->fill(Input::all());
            $project->id = null;
        } else {
            $project = Project::make(Input::all());
        }
        return Persistence::saveProject($project);
	}

	/**
	 * Display the specified resource.
     * @SWG\Get(
     *     path="/projects/{project_key}",
     *     summary="Display project",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Project"),
     *     security={{"highcore_auth":{}}},
     * )
	 * @param  int|string $project_key
	 * @return Response
	 */
	public function show($project_key)
	{
        return Persistence::getProject($project_key, Input::get('key', 'id'));
	}

	/**
	 * Update the specified resource in storage.
     * @SWG\Put(
     *     path="/projects/{project_key}",
     *     summary="Update project",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Parameter(ref="#/parameters/Project"),
     *     @SWG\Response(response="default", ref="#/responses/Project"),
     *     security={{"highcore_auth":{}}},
     * )
	 * @param  int|string $project_key
	 * @return Response
	 */
	public function update($project_key)
	{
        return Persistence::saveProject(
            Persistence::getProject($project_key, Input::get('key', 'id'))->fill(Input::all())
        );
	}

	/**
	 * Remove the specified resource from storage.
     * @SWG\Delete(
     *     path="/projects/{project_key}",
     *     summary="Delete project",
     *     @SWG\Parameter(ref="#/parameters/project_key"),
     *     @SWG\Parameter(ref="#/parameters/key"),
     *     @SWG\Response(response="default", ref="#/responses/Bool"),
     *     security={{"highcore_auth":{}}},
     * )
	 * @param  int|string $project_key
	 * @return Response
	 */
	public function destroy($project_key)
	{
        Persistence::deleteProject(
            Persistence::getProject($project_key, Input::get('key', 'id'))
        );
        return 'true';
	}

}
