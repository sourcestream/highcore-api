<?php namespace Highcore\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException'
	];

	/**
	 * A list of the exception types that should be treated like Http Exceptions.
	 *
	 * @var array
	 */
	protected $convertToHttpException = [
		'Highcore\Services\Persistence\Exceptions\NotFoundException'
	];

    /**
     * Determine if the exception is in the "forward code" list.
     *
     * @param  \Exception  $e
     * @return bool
     */
    protected function shouldConvertToHttpException(Exception $e)
    {
        foreach ($this->convertToHttpException as $type)
        {
            if (is_a($e, $type)) return true;
        }
        return false;
    }

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		return parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{

        // If this exception is listed for code forwarding
        if ($this->shouldConvertToHttpException($e))
        {
            $e = new HttpException($e->getCode(), $e->getMessage(), $e);
        }

        // If the request wants JSON (AJAX doesn't always want JSON)
        if ($request->wantsJson())
        {
            // Define the response
            $response = [
                'errors' => 'Sorry, something went wrong.'
            ];

            // If the app is in debug mode
            if (config('app.debug'))
            {
                // Add the exception class name, message and stack trace to response
                $response['exception'] = get_class($e); // Reflection might be better here
                $response['message'] = $e->getMessage();
                $response['trace'] = $e->getTrace();
            }

            // Default response of 400
            $status = 400;

            // If this exception is an instance of HttpException
            if ($this->isHttpException($e))
            {
                // Grab the HTTP status code from the Exception
                $status = $e->getStatusCode();
            }

            // Return a JSON response with the response array and status code
            unset($response['trace']);
            return response()->json($response, $status);
        }

        // Default to the parent class' implementation of handler
		return parent::render($request, $e);
	}

}
