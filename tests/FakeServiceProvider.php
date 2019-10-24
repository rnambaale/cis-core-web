<?php

namespace Tests;

use App\Http\Clients\PasswordClient;
use App\Providers\AppServiceProvider;
use App\Repositories\SessionTokenRepository;
use App\Http\Clients\ClientCredentialsClient;
use App\Http\Clients\PasswordClientInterface;
use App\Http\Clients\ClientCredentialsClientInterface;
use Bmatovu\OAuthNegotiator\Repositories\TokenRepositoryInterface;

class FakeServiceProvider extends AppServiceProvider
{
    /**
     * Register any application services.
     *
     * Override app binding - necessary to prevent hitting live API endpoints.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(ClientCredentialsClientInterface::class, function () {
            return new ClientCredentialsClient();
        });

        $this->app->bind(PasswordClientInterface::class, function () {
            return new PasswordClient();
        });

        $this->app->bind(TokenRepositoryInterface::class, function () {
            return new SessionTokenRepository();
        });
    }
}
