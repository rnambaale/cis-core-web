<?php

if (! function_exists('can')) {
    /**
     * Determine if a user has permission to perform a certain action.
     *
     * Reads permission save in current session.
     *
     * @see \App\Http\Controllers\Auth\LoginController
     *
     * @param string      $module
     * @param null|string $permission
     *
     * @return bool
     */
    function can($module, $permission = null)
    {
        $permissions = app('session')->get('permissions', []);

        if (! isset($permissions[$module])) {
            return false;
        }

        if ($permission && ! in_array($permission, $permissions[$module])) {
            return false;
        }

        return true;
    }
}
