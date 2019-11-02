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
    Route::post('/', 'FacilityController@store')->name('store');
    Route::get('/create', 'FacilityController@create')->name('create');
    Route::delete('/{facility}', 'FacilityController@destroy')->name('destroy');
    Route::put('/{facility}', 'FacilityController@update')->name('update');
    Route::get('/{facility}', 'FacilityController@show')->name('show');
    Route::get('/{facility}/edit', 'FacilityController@edit')->name('edit');
    Route::put('/{facility}/restore', 'FacilityController@restore')->name('restore');
    Route::put('/{facility}/revoke', 'FacilityController@revoke')->name('revoke');
});
