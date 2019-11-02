<?php

namespace Tests\Feature\Console;

use Tests\TestCase;
use Illuminate\Filesystem\Filesystem;

/**
 * @see \App\Console\Commands\GenerateJavascriptRoutes
 */
class GenerateJavascriptRoutesTest extends TestCase
{
    public function test_generate_js_routes()
    {
        $path = 'resources/js/test-routes.json';

        $this->artisan('routes:json', [
            '--path' => $path,
        ])
            ->expectsOutput('Generating routes for Javascript.')
            ->expectsOutput("Routes saved to '{$path}'.")
            ->assertExitCode(0);

        $this->assertFileExists($path);

        // Clean up
        (new Filesystem)->delete($path);
    }
}
