<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('home')->middleware('verified');

$uuid = '^([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}){1}$';

Route::group(['namespace' => 'Auth', 'prefix' => '', 'as' => ''], function () {
    // Authentication routes...
    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login');
    Route::post('logout', 'LoginController@logout')->name('logout');

    // Password reset routes...
    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');

    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');

    // Email verification routes...
    Route::get('email/verify', 'VerificationController@show')->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', 'VerificationController@verify')->name('verification.verify');
    Route::post('email/resend', 'VerificationController@resend')->name('verification.resend');
});

Route::pattern('facility', $uuid);

Route::group(['prefix' => 'facilities', 'as' => 'facilities.'], function () {
    Route::get('/', 'FacilityController@index')->name('index');
    Route::get('/dt', 'FacilityController@showDatatables')->name('dt.show');
    Route::get('/dt/load', 'FacilityController@datatables')->name('dt');
    Route::post('/', 'FacilityController@store')->name('store');
    Route::get('/create', 'FacilityController@create')->name('create');
    Route::delete('/{facility}', 'FacilityController@destroy')->name('destroy');
    Route::put('/{facility}', 'FacilityController@update')->name('update');
    Route::get('/{facility}', 'FacilityController@show')->name('show');
    Route::get('/{facility}/edit', 'FacilityController@edit')->name('edit');
    Route::put('/{facility}/restore', 'FacilityController@restore')->name('restore');
    Route::put('/{facility}/revoke', 'FacilityController@revoke')->name('revoke');
});

Route::pattern('role', $uuid);

Route::group(['prefix' => 'roles', 'as' => 'roles.'], function () {
    Route::get('/', 'RoleController@index')->name('index');
    Route::get('/dt', 'RoleController@showDatatables')->name('dt.show');
    Route::get('/dt/load', 'RoleController@datatables')->name('dt');
    Route::post('/', 'RoleController@store')->name('store');
    Route::get('/create', 'RoleController@create')->name('create');
    Route::delete('/{role}', 'RoleController@destroy')->name('destroy');
    Route::put('/{role}', 'RoleController@update')->name('update');
    Route::get('/{role}', 'RoleController@show')->name('show');
    Route::get('/{role}/edit', 'RoleController@edit')->name('edit');
    Route::put('/{role}/restore', 'RoleController@restore')->name('restore');
    Route::put('/{role}/revoke', 'RoleController@revoke')->name('revoke');
});

Route::pattern('user', $uuid);

Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
    Route::get('/', 'UserController@index')->name('index');
    Route::get('/dt', 'UserController@showDatatables')->name('dt.show');
    Route::get('/dt/load', 'UserController@datatables')->name('dt');
    Route::post('/', 'UserController@store')->name('store');
    Route::get('/create', 'UserController@create')->name('create');
    Route::delete('/{user}', 'UserController@destroy')->name('destroy');
    Route::put('/{user}', 'UserController@update')->name('update');
    Route::get('/{user}', 'UserController@show')->name('show');
    Route::get('/{user}/edit', 'UserController@edit')->name('edit');
    Route::put('/{user}/restore', 'UserController@restore')->name('restore');
    Route::put('/{user}/revoke', 'UserController@revoke')->name('revoke');
});
