<?php

namespace Tests\Unit\Repositories;

use App\Repositories\SessionTokenRepository;
use Bmatovu\OAuthNegotiator\Exceptions\TokenNotFoundException;
use Bmatovu\OAuthNegotiator\Models\TokenInterface;
use Bmatovu\OAuthNegotiator\Repositories\TokenRepositoryInterface;
use Tests\TestCase;

/**
 * @see \App\Repositories\SessionTokenRepository
 */
class SessionTokenRepositoryTest extends TestCase
{
    public function test_can_create_repo()
    {
        $repo = new SessionTokenRepository();

        $this->assertInstanceOf(TokenRepositoryInterface::class, $repo);
    }

    public function test_can_create_token()
    {
        $token = (new SessionTokenRepository())->create([
            'access_token'  => 'QC9jztmMfeHoRg5zyTiR',
            'refresh_token' => '4IAtuQ1aQZhHeRGlFcX6',
            'token_type'    => 'Bearer',
            'expires_in'    => 3600,
        ]);

        $this->assertNotNull($this->app['session']->get('token'));

        $this->assertInstanceOf(TokenInterface::class, $token);

        $this->assertEquals('QC9jztmMfeHoRg5zyTiR', $token->getAccessToken());
        $this->assertEquals('4IAtuQ1aQZhHeRGlFcX6', $token->getRefreshToken());
        $this->assertEquals('Bearer', $token->getTokenType());
        $this->assertNotNull($token->getExpiresAt());
    }

    public function test_cant_retrieve_missing_token()
    {
        $this->assertNull((new SessionTokenRepository())->retrieve());
    }

    public function test_cant_retrieve_unknown_token()
    {
        $accessToken = 'some_random_access_token';
        // $this->expectException(TokenNotFoundException::class);
        $this->assertNull((new SessionTokenRepository())->retrieve($accessToken));
    }

    public function test_can_retrieve_first_available_token()
    {
        $repo = new SessionTokenRepository();

        $repo->create([
            'access_token'  => 'neGb9VrmDgeHVucZlYvn',
            'refresh_token' => 'tPh9XtPrr7w62lEH1RlK',
            'token_type'    => 'Bearer',
            'expires_in'    => 3600,
        ]);
        $token = $repo->retrieve();
        $this->assertInstanceOf(TokenInterface::class, $token);
        $this->assertEquals('neGb9VrmDgeHVucZlYvn', $token->getAccessToken());
    }

    public function test_can_retrieve_token()
    {
        $repo = new SessionTokenRepository();

        $repo->create([
            'access_token'  => 'QC9jztmMfeHoRg5zyTiR',
            'refresh_token' => '4IAtuQ1aQZhHeRGlFcX6',
            'token_type'    => 'Bearer',
            'expires_in'    => 3600,
        ]);

        $token = $repo->retrieve();
        $this->assertInstanceOf(TokenInterface::class, $token);
        $this->assertEquals('QC9jztmMfeHoRg5zyTiR', $token->getAccessToken());
    }

    public function test_can_update_token()
    {
        $repo = new SessionTokenRepository();

        $tokenData = [
            'access_token'  => 'QC9jztmMfeHoRg5zyTiR',
            'refresh_token' => '4IAtuQ1aQZhHeRGlFcX6',
            'token_type'    => 'Bearer',
            'expires_in'    => 3600,
        ];

        $repo->create($tokenData);

        $newTokenData = [
            'access_token'  => 'ij5AkeuBQs0hx2EDDcCp',
            'refresh_token' => 'HWomm4Z6790xezQi5V6s',
            'token_type'    => 'Basic',
            'expires_in'    => 0,
        ];

        $token = $repo->update($tokenData['access_token'], $newTokenData);

        $this->assertInstanceOf(TokenInterface::class, $token);
        $this->assertEquals($newTokenData['access_token'], $token->getAccessToken());
        $this->assertEquals($newTokenData['refresh_token'], $token->getRefreshToken());
        $this->assertEquals($newTokenData['token_type'], $token->getTokenType());
        $expires_at = (new \DateTime())->add(new \DateInterval("PT{$newTokenData['expires_in']}S"))->format('Y-m-d H:i:s');
        $this->assertEquals($expires_at, $token->getExpiresAt());
    }

    public function test_can_delete_token()
    {
        $repo = new SessionTokenRepository();

        $repo->create([
            'access_token'  => 'QC9jztmMfeHoRg5zyTiR',
            'refresh_token' => '4IAtuQ1aQZhHeRGlFcX6',
            'token_type'    => 'Bearer',
            'expires_in'    => 3600,
        ]);

        $token = $repo->delete('QC9jztmMfeHoRg5zyTiR');

        $this->assertNull($token);
    }
}
