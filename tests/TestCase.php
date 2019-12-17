<?php

namespace Tests;

use App\Http\Clients\ClientCredentialsClient;
use App\Http\Clients\PasswordClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Mock client-credentials client.
     *
     * @param mixed $response
     *
     * @return \App\Http\Clients\ClientCredentialsClient
     */
    protected function mockMachineClient($response)
    {
        if (is_array($response)) {
            $responses = $response;
        } else {
            $responses[] = $response;
        }

        $mockHandler = new MockHandler($responses);

        $handlerStack = HandlerStack::create($mockHandler);

        return new ClientCredentialsClient([
            'base_uri' => 'http://api.example.com/',
            'handler'  => $handlerStack,
            'headers'  => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Mock password client.
     *
     * @param mixed $response
     *
     * @return \App\Http\Clients\PasswordClient
     */
    protected function mockPasswordClient($response)
    {
        if (is_array($response)) {
            $responses = $response;
        } else {
            $responses[] = $response;
        }

        $mockHandler = new MockHandler($responses);

        $handlerStack = HandlerStack::create($mockHandler);

        return new PasswordClient([
            'base_uri' => 'http://api.example.com/',
            'handler'  => $handlerStack,
            'headers'  => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Grant current user fake permissions.
     *
     * @param string $module
     * @param string $permission
     *
     * @return void
     */
    protected function fakeUserPermission($module, $permission = ''): void
    {
        $this->app['session']->put('modules', [
            $module => [
                $permission,
            ],
        ]);
    }
}
