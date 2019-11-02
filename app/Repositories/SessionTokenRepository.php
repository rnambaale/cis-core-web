<?php

namespace App\Repositories;

use Bmatovu\OAuthNegotiator\Exceptions\TokenNotFoundException;
use Bmatovu\OAuthNegotiator\Models\Token;
use Bmatovu\OAuthNegotiator\Repositories\TokenRepositoryInterface;

/**
 * Session base token persistence.
 */
class SessionTokenRepository implements TokenRepositoryInterface
{
    /**
     * Constructor.
     */
    public function __constructor()
    {
        // Silence is golden.
    }

    /**
     * Create token.
     *
     * @param array $attributes
     *
     * @return \Bmatovu\OAuthNegotiator\Models\TokenInterface Token created.
     */
    public function create(array $attributes)
    {
        $attributes = array_merge([
            'access_token' => null,
            'refresh_token' => null,
            'token_type' => 'Bearer',
            'expires_in' => null,
        ], $attributes);

        $token = new Token(
            $attributes['access_token'],
            $attributes['refresh_token'],
            $attributes['token_type'],
            $attributes['expires_in']
        );

        app('session')->put('token', serialize($token));

        return $token;
    }

    /**
     * Retrieve token.
     *
     * Specified token, or any token available in storage.
     *
     * @param string $access_token
     *
     * @throws \Bmatovu\OAuthNegotiator\Exceptions\TokenNotFoundException
     *
     * @return \Bmatovu\OAuthNegotiator\Models\TokenInterface|null Token, null if non found.
     */
    public function retrieve($access_token = null)
    {
        $sessionToken = app('session')->get('token');

        if (! $sessionToken) {
            return null;
        }

        $token = unserialize($sessionToken);

        if ($access_token && $token->getAccessToken() != $access_token) {
            throw new TokenNotFoundException('Unknown token.');
        }

        return $token;
    }

    /**
     * Updates token.
     *
     * @param mixed $access_token
     * @param array $attributes
     *
     * @throws \Bmatovu\OAuthNegotiator\Exceptions\TokenNotFoundException
     *
     * @return \Bmatovu\OAuthNegotiator\Models\TokenInterface Token
     */
    public function update($access_token, array $attributes)
    {
        return $this->create($attributes);
    }

    /**
     * Destroy token.
     *
     * @param string $access_token
     *
     * @throws \Bmatovu\OAuthNegotiator\Exceptions\TokenNotFoundException
     *
     * @return void
     */
    public function delete($access_token)
    {
        app('session')->put('token', null);
    }
}
