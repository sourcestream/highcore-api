<?php namespace Highcore\Http\Controllers;

use Highcore\Models\Environment;
use Route;
use Request;
use Input;
use Response;
use Persistence;

/**
 * @SWG\Parameter(
 *     name="environment_id",
 *     description="Environment Id",
 *     in="path",
 *     required=true,
 *     type="integer",
 * )
 * @SWG\Parameter(
 *     name="environment_key",
 *     description="Environment Key",
 *     in="path",
 *     required=true,
 *     type="string",
 * )
 * @SWG\Parameter(
 *     name="Environment",
 *     in="body",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/Environment")
 * )
 * @SWG\Response(
 *     response="Environments",
 *     description="Array of Environments",
 *     @SWG\Schema(
 *         type="array",
 *         @SWG\Items(ref="#/definitions/Environment")
 *     ),
 * )
 * @SWG\Response(
 *     response="Environment",
 *     description="Environment",
 *     @SWG\Schema(ref="#/definitions/Environment"),
 * )
 */
class EnvironmentsController extends Controller {

	/**
	 * Display a listing of the resource.
	 * @SWG\Get(
     *     path="/environments",
     *     summary="All environments",
     *     @SWG\Response(response="default", ref="#/responses/Environments"),
     *     security={{"highcore_auth":{}}},
     * )
	 * @return Response
	 */
	public function index()
	{
        return Persistence::getEnvironments();
	}

    /**
     * Store a newly created resource in storage.
     * @SWG\Post(
     *     path="/environments",
     *     summary="Store environment",
     *     @SWG\Parameter(ref="#/parameters/source"),
     *     @SWG\Parameter(ref="#/parameters/Environment"),
     *     @SWG\Response(response="default", ref="#/responses/Environment"),
     *     security={{"highcore_auth":{}}},
     * )
	 * @return Response
	 */
	public function store()
	{
        $source = Input::get('source', false);
        if ($source) {
            $request = Request::create($source, 'GET');
            /** @var Environment $environment */
            $environment = Route::dispatch($request)->getOriginalContent()->fill(Input::all());
            $environment->id = null;
        } else {
            $environment = Environment::make(Input::all());
        }
        return Persistence::saveEnvironment($environment);
	}

	/**
	 * Display the specified resource.
     * @SWG\Get(
     *     path="/environments/{environment_id}",
     *     summary="Display environment",
     *     @SWG\Parameter(ref="#/parameters/environment_id"),
     *     @SWG\Response(response="default", ref="#/responses/Environment"),
     *     security={{"highcore_auth":{}}},
     * )
	 * @param  int  $environment_id
	 * @return Response
	 */
	public function show($environment_id)
	{
        return Persistence::getEnvironment($environment_id);
	}

	/**
	 * Update the specified resource in storage.
     * @SWG\Put(
     *     path="/environments/{environment_id}",
     *     summary="Update environment",
     *     @SWG\Parameter(ref="#/parameters/environment_id"),
     *     @SWG\Parameter(ref="#/parameters/Environment"),
     *     @SWG\Response(response="default", ref="#/responses/Environment"),
     *     security={{"highcore_auth":{}}},
     * )
	 * @param  int $environment_id
	 * @return Response
	 */
	public function update($environment_id)
	{
        return Persistence::saveEnvironment(
            Persistence::getEnvironment($environment_id)->fill(Input::all())
        );
	}

	/**
	 * Remove the specified resource from storage.
     * @SWG\Delete(
     *     path="/environments/{environment_id}",
     *     summary="Delete environment",
     *     @SWG\Parameter(ref="#/parameters/environment_id"),
     *     @SWG\Response(response="default", ref="#/responses/Bool"),
     *     security={{"highcore_auth":{}}},
     * )
	 * @param  int  $environment_id
	 * @return Response
	 */
	public function destroy($environment_id)
	{
        Persistence::deleteProject(
            Persistence::getEnvironment($environment_id)
        );
        return 'true';
	}

}
