<?php

namespace Tests\Unit\Support;

use Tests\TestCase;

class HelpersTest extends TestCase
{
    public function test_can_check_pemissions()
    {
        $modules = [
            'users' => [
                'create',
            ],
        ];

        app('session')->put('modules', $modules);

        $this->assertTrue(auth_can('users', 'create'));
        $this->assertFalse(auth_can('users', 'delete'));
    }

    public function test_can_check_modules()
    {
        $modules = [
            'users' => [
                'create',
            ],
        ];

        app('session')->put('modules', $modules);

        $this->assertTrue(auth_has('users'));
        $this->assertFalse(auth_has('roles'));
    }

    public function test_can_check_categories()
    {
        $categories = [
            'administration',
        ];

        app('session')->put('categories', $categories);

        $this->assertTrue(auth_any('administration'));
        $this->assertFalse(auth_any('pharmacy'));
    }
}
