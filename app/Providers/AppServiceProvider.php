<?php

namespace App\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use App\Http\Clients\PasswordClient;
use Illuminate\Support\ServiceProvider;
use App\Repositories\SessionTokenRepository;
use App\Http\Clients\ClientCredentialsClient;
use App\Http\Clients\PasswordClientInterface;
use Bmatovu\OAuthNegotiator\OAuth2Middleware;
use Bmatovu\OAuthNegotiator\GrantTypes\Password;
use Bmatovu\OAuthNegotiator\GrantTypes\RefreshToken;
use App\Http\Clients\ClientCredentialsClientInterface;
use Bmatovu\OAuthNegotiator\GrantTypes\ClientCredentials;
use Bmatovu\OAuthNegotiator\Repositories\FileTokenRepository;
use Bmatovu\OAuthNegotiator\Repositories\TokenRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ClientCredentialsClientInterface::class, function () {
            return $this->createClientCredentialsClient();
        });

        $this->app->bind(PasswordClientInterface::class, function () {
            return $this->createPasswordClient();
        });

        $this->app->bind(TokenRepositoryInterface::class, function () {
            return new SessionTokenRepository();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Create client-credentials client.
     *
     * @throws \Exception
     *
     * @return \App\Http\Clients\ClientCredentialsClientInterface
     */
    protected function createClientCredentialsClient()
    {
        // .................................................................

        $handlerStack1 = HandlerStack::create();

        $handlerStack1->push($this->getLogMiddleware());

        // Authorization client - this is used to request OAuth access tokens
        $client = new Client([
            'handler' => $handlerStack1,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $config = [
            'token_uri' => config('oauth.token_uri'),
            'client_id' => config('oauth.client.id'),
            'client_secret' => config('oauth.client.secret'),
            // 'scope' => config('oauth.client.scopes'),
            'scope' => implode(' ', config('oauth.client.scopes')),
        ];

        // This grant type is used to get a new Access Token and,
        // Refresh Token when no valid Access Token or Refresh Token is available
        $clientCredGrant = new ClientCredentials($client, $config);

        $fileTokenRepository = new FileTokenRepository(storage_path('client-token.key'));

        // Tell the middleware to use both the client and refresh token grants
        $oauthMiddleware = new OAuth2Middleware($clientCredGrant, null, $fileTokenRepository);

        // .................................................................

        $handlerStack2 = HandlerStack::create();

        $handlerStack2->push($this->getLogMiddleware());

        $handlerStack2->push($oauthMiddleware);

        return new ClientCredentialsClient([
            'handler' => $handlerStack2,
            'base_uri' => config('oauth.base_uri'),
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Create password client.
     *
     * @throws \Exception
     *
     * @return \App\Http\Clients\PasswordClientInterface
     */
    protected function createPasswordClient()
    {
        // .................................................................

        $handlerStack1 = HandlerStack::create();

        $handlerStack1->push($this->getLogMiddleware());

        // Authorization client - this is used to request OAuth access tokens
        $client = new Client([
            'handler' => $handlerStack1,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $passwordGrant = new Password($client, [
            'token_uri' => config('oauth.token_uri'),
            'client_id' => config('oauth.client.id'),
            'client_secret' => config('oauth.client.secret'),
            'username' => '',
            'password' => '',
            'scope' => implode(' ', config('oauth.client.scopes')),
        ]);

        $refreshTokenGrant = new RefreshToken($client, [
            'token_uri' => config('oauth.token_uri'),
            'client_id' => config('oauth.client.id'),
            'client_secret' => config('oauth.client.secret'),
            // 'refresh_token' => '', // Gotten from tokenRepo at runtime
            'scope' => implode(' ', config('oauth.client.scopes')),
        ]);

        $sessionTokenRepository = new SessionTokenRepository();

        $oauthMiddleware = new OAuth2Middleware($passwordGrant, $refreshTokenGrant, $sessionTokenRepository);

        // .................................................................

        $handlerStack2 = HandlerStack::create();

        $handlerStack2->push($this->getLogMiddleware());

        $handlerStack2->push($oauthMiddleware);

        return new PasswordClient([
            'handler' => $handlerStack2,
            'base_uri' => config('oauth.base_uri'),
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Get log middleware.
     *
     * @throws \Exception
     *
     * @return callable GuzzleHttp Middleware
     */
    protected function getLogMiddleware()
    {
        $logger = $this->app['log']->getLogger();

        $messageFormatter = new MessageFormatter(MessageFormatter::DEBUG);

        return Middleware::log($logger, $messageFormatter);
    }
}
