<?php namespace Highcore\Http\Controllers;

use Highcore\User;
use Illuminate\Contracts\Auth\Guard;
use Response;

/**
 * @SWG\Parameter(
 *     name="user_id",
 *     in="path",
 *     required=true,
 *     type="integer",
 * )
 * @SWG\Response(
 *     response="User",
 *     description="User",
 *     @SWG\Schema(ref="#/definitions/User"),
 * )
 */
class UsersController extends Controller {

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Show user data
     * @SWG\Get(
     *     path="/users/{user_id}",
     *     summary="Display user",
     *     @SWG\Parameter(ref="#/parameters/user_id"),
     *     @SWG\Response(response="default", ref="#/responses/User"),
     *     security={{"highcore_auth":{}}},
     * )
     * @param int $user_id  User Id
     * @return Response
     */
	public function show($user_id)
	{
        if ($user_id == 'me') {
            $user = $this->auth->user();
        } else {
            $user = User::find($user_id);
        }
        return $user;
	}

}
