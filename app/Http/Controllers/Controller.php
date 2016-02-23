<?php namespace Highcore\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Input;
use Route;

/**
 * @SWG\Parameter(
 *     name="key",
 *     description="Property name to be used as a key",
 *     in="query",
 *     required=false,
 *     type="string",
 * )
 * @SWG\Parameter(
 *     name="parameter_path",
 *     in="path",
 *     required=true,
 *     type="string",
 * )
 * @SWG\Parameter(
 *     name="parameter_id",
 *     in="path",
 *     required=true,
 *     type="string",
 * )
 * @SWG\Parameter(
 *     name="component_id",
 *     in="path",
 *     required=true,
 *     type="string",
 * )
 * @SWG\Parameter(
 *     name="Parameter",
 *     in="body",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/Parameter")
 * )
 * @SWG\Parameter(
 *     name="Component",
 *     in="body",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/Component")
 * )
 * @SWG\Response(
 *     response="Parameters",
 *     description="Array of Parameters",
 *     @SWG\Schema(
 *         type="array",
 *         @SWG\Items(ref="#/definitions/Parameter")
 *     ),
 * )
 * @SWG\Response(
 *     response="Parameter",
 *     description="Parameter",
 *     @SWG\Schema(ref="#/definitions/Parameter"),
 * )
 * @SWG\Response(
 *     response="Components",
 *     description="Array of Components",
 *     @SWG\Schema(
 *         type="array",
 *         @SWG\Items(ref="#/definitions/Component")
 *     ),
 * )
 * @SWG\Response(
 *     response="Component",
 *     description="Component",
 *     @SWG\Schema(ref="#/definitions/Component"),
 * )
 * @SWG\Response(
 *     response="Bool",
 *     description="True if operation completed successfully",
 * ),
 * @SWG\Response(
 *     response="Json",
 *     description="Json-encoded response",
 * ),
 */
abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;

    /**
     * Generates a link for HATEOAS pagination using nextToken
     * @param string $token
     * @param array $parameters route parameters
     * @param string $routeName route name override
     * @return array
     */
    protected function hateoasNextPage($token, $parameters = [], $routeName = null)
    {
        return $this->hateoasRoute('next', ['nextToken' => $token] + $parameters, $routeName);
    }

    protected function hateoasRoute($linkName, $parameters = [], $routeName = null) {
        $router = $this->getRouter();
        $currentRoute = $routeName === null ?
            $router->current() :
            $router->getRoutes()->getByName($routeName);

        $parameters += $currentRoute->parameters();

        return $this->hateoasLink($linkName, \URL::route($currentRoute->getName(), $parameters));
    }

    protected function hateoasSelf()
    {
        return $this->hateoasLink('self', \URL::full());
    }

    protected function hateoasLink($rel, $href)
    {
        return ['_links' => [$rel => ['rel' => $rel, 'href' => $href]]];
    }
}
