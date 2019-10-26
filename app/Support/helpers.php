<?php

if (! function_exists('auth_can')) {
    /**
     * Determine if a user has permission to perform a certain action.
     *
     * Reads modules-permissions array saved in current session.
     *
     * @see \App\Http\Controllers\Auth\LoginController
     *
     * @param string $module
     * @param string $permission
     *
     * @return bool
     */
    function auth_can(string $module, string $permission): bool
    {
        $modules = app('session')->get('modules', []);

        if (! isset($modules[$module])) {
            return false;
        }

        return in_array($permission, $modules[$module]);
    }
}

if (! function_exists('auth_has')) {
    /**
     * Determine if a user has any permissions on a module.
     *
     * Reads modules-permissions array saved in current session.
     *
     * @see \App\Http\Controllers\Auth\LoginController
     *
     * @param string $module
     *
     * @return bool
     */
    function auth_has(string $module): bool
    {
        $modules = app('session')->get('modules', []);

        return isset($modules[$module]);
    }
}

if (! function_exists('auth_any')) {
    /**
     * Determine if a user has any permissions on any module in a given category.
     *
     * Reads module-categories array saved in current session.
     *
     * @see \App\Http\Controllers\Auth\LoginController
     *
     * @param string $category
     *
     * @return bool
     */
    function auth_any(string $category): bool
    {
        $categories = app('session')->get('categories', []);

        return in_array($category, $categories);
    }
}
