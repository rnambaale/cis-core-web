<?php

namespace App\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Exception $exception
     *
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ConnectException) {
            flash('Error connecting to remote service.')->error();

            return redirect()->back();
        }

        if ($exception instanceof ClientException) {
            $statusCode = $exception->getResponse()->getStatusCode();

            $body = json_decode($exception->getResponse()->getBody(), false);

            flash($body->message)->warning()->important();

            if ($statusCode === 422) {
                return redirect()->back()
                    ->withInput($request->input())
                    ->withErrors($body->errors);
            }

            return redirect()->back();
        }

        if ($exception instanceof RequestException) {
            $body = json_decode($exception->getResponse()->getBody(), false);

            flash($body->message)->error();

            return redirect()->back();
        }

        return parent::render($request, $exception);
    }
}
