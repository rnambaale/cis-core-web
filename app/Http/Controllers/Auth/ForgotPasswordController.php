<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Validation\ValidationException;
use App\Http\Clients\ClientCredentialsClientInterface;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Client credentials client.
     *
     * @var \App\Http\Clients\ClientCredentialsClientInterface
     */
    protected $machineClient;

    /**
     * Create a new controller instance.
     *
     * @param \App\Http\Clients\ClientCredentialsClientInterface $clientCredentialsClient
     *
     * @return void
     */
    public function __construct(
        ClientCredentialsClientInterface $clientCredentialsClient
    ) {
        $this->machineClient = $clientCredentialsClient;
        $this->middleware('guest');
    }

    /**
     * Validate the email for the given request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        // $request->validate(['email' => 'required|email']);

        $this->remoteEmailValidation($request);
    }

    /**
     * Remote email validation.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return void
     */
    protected function remoteEmailValidation($request)
    {
        try {
            $this->machineClient->post('users/email', [
                'form_params' => $request->input(),
            ]);
        } catch (ConnectException $ex) {
            flash('Error connecting to remote service.')->error()->important();
        } catch (ClientException $ex) {
            $statusCode = $ex->getResponse()->getStatusCode();

            $body = json_decode($ex->getResponse()->getBody(), true);

            flash($body['message'])->warning()->important();

            if ($statusCode === 422) {
                $validator = Validator::make([], []);

                $validator->errors()->merge($body['errors']);

                throw new ValidationException($validator);
            }
        } catch (RequestException $ex) {
            $body = json_decode($ex->getResponse()->getBody(), true);

            flash($body['message'])->warning()->important();
        }

        return redirect()->back();
    }
}
