<?php namespace Highcore\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class CORS {

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);
        $response->headers->add([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin'
        ]);
        return $response;
    }
}