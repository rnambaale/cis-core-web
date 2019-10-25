<?php

namespace Tests\Unit\Support;

use Tests\TestCase;

class HelpersTest extends TestCase
{
    public function test_can_check_pemissions()
    {
        $permissions = [
            'users' => [
                'create',
            ],
        ];

        app('session')->put('permissions', $permissions);

        $this->assertTrue(can('users'));
        $this->assertFalse(can('roles'));
        $this->assertTrue(can('users', 'create'));
        $this->assertFalse(can('users', 'delete'));
    }
}
