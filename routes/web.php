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
$slug = '^[a-z\d]+(?:-[a-z\d]+)*$';
$int = '^\d+$';

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

    // Password confirmation routes...
    Route::get('password/confirm', 'ConfirmPasswordController@showConfirmForm')->name('password.confirm');
    Route::post('password/confirm', 'ConfirmPasswordController@confirm');

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
    Route::get('/{facility}/modules', 'FacilityController@showModules')->name('modules.show');
    Route::put('/{facility}/modules', 'FacilityController@syncModules')->name('modules.update');
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
    Route::get('/{role}/permissions', 'RoleController@showPermissions')->name('permissions.show');
    Route::put('/{role}/permissions', 'RoleController@syncPermissions')->name('permissions.update');
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

Route::pattern('module', $slug);

Route::group(['prefix' => 'modules', 'as' => 'modules.'], function () {
    Route::get('/', 'ModuleController@index')->name('index');
    Route::get('/dt', 'ModuleController@showDatatables')->name('dt.show');
    Route::get('/dt/load', 'ModuleController@datatables')->name('dt');
    Route::post('/', 'ModuleController@store')->name('store');
    Route::get('/create', 'ModuleController@create')->name('create');
    Route::delete('/{module}', 'ModuleController@destroy')->name('destroy');
    Route::put('/{module}', 'ModuleController@update')->name('update');
    Route::get('/{module}', 'ModuleController@show')->name('show');
    Route::get('/{module}/edit', 'ModuleController@edit')->name('edit');
    Route::put('/{module}/restore', 'ModuleController@restore')->name('restore');
    Route::put('/{module}/revoke', 'ModuleController@revoke')->name('revoke');
});

Route::pattern('permission', $int);

Route::group(['prefix' => 'permissions', 'as' => 'permissions.'], function () {
    Route::get('/', 'PermissionController@index')->name('index');
    Route::get('/dt', 'PermissionController@showDatatables')->name('dt.show');
    Route::get('/dt/load', 'PermissionController@datatables')->name('dt');
    Route::post('/', 'PermissionController@store')->name('store');
    Route::get('/create', 'PermissionController@create')->name('create');
    Route::delete('/{permission}', 'PermissionController@destroy')->name('destroy');
    Route::put('/{permission}', 'PermissionController@update')->name('update');
    Route::get('/{permission}', 'PermissionController@show')->name('show');
    Route::get('/{permission}/edit', 'PermissionController@edit')->name('edit');
});

Route::group([
    'prefix' => 'pharmacy',
    'as' => 'pharmacy.',
    'namespace' => '\App\Http\Controllers\Pharmacy',
], function () {
    Route::get('/stores', 'StoreUserController@index')->name('stores.index');

    Route::get('/stores/{store}/inventory', 'InventoryController@index')->name('inventories.index');
    Route::get('/stores/{store}/inventory/suggest', 'InventoryController@suggest')->name('inventories.index_suggest');
    Route::get('/stores/{store}/inventory/dt/load', 'InventoryController@datatables')->name('inventories.index.dt');

    Route::get('/stores/{store}/sales', 'SalesController@index')->name('sales.index');
    Route::get('/stores/{store}/sales/create', 'SalesController@create')->name('sales.create');
    Route::get('/stores/{store}/sales/{sale}', 'SalesController@show')->name('sales.show');
    Route::post('/stores/{store}/sales/checkout', 'SalesController@store')->name('sales.store');

    Route::post('/stores/{store}/sales', 'SalesCartController@store')->name('salesCart.store');
    Route::put('/stores/{store}/sales/{item}', 'SalesCartController@update')->name('salesCart.update');
    Route::delete('/stores/{store}/sales/{item}', 'SalesCartController@destroy')->name('salesCart.destroy');

    Route::get('/stores/{store}/purchases', 'PurchasesController@create')->name('purchases.create');
    Route::post('/stores/{store}/purchase/checkout', 'PurchasesController@store')->name('purchases.store');

    Route::post('/stores/{store}/purchases', 'PurchasesCartController@store')->name('purchasesCart.store');
    Route::put('/stores/{store}/purchases/{item}', 'PurchasesCartController@update')->name('purchasesCart.update');
    Route::delete('/stores/{store}/purchases/{item}', 'PurchasesCartController@destroy')->name('purchasesCart.destroy');
});
