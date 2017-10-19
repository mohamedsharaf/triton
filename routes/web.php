<?php
// dd(env('APP_VERSION'));
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

Route::get('/', function () {
    return view('welcome');
});

// Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

//=== DE CUENTA DE Auth::routes(); ===
  // Authentication Routes...
  Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
  Route::post('login', 'Auth\LoginController@login');
  Route::post('logout', 'Auth\LoginController@logout')->name('logout');

  // Registration Routes...
  // Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
  // Route::post('register', 'Auth\RegisterController@register');

  // Password Reset Routes...
  Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
  Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
  Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
  Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::get('/home', 'HomeController@index')->name('home');

//=== GESTOR DE MODULOS ===
  Route::get('/modulo', 'Seguridad\ModuloController@index')->name('modulo');
    // Route::post('/modulo/view_jqgrid', 'Seguridad\ModuloController@view_jqgrid');
    Route::match(["get", "post"], '/modulo/view_jqgrid', 'Seguridad\ModuloController@view_jqgrid');
    Route::post('/modulo/send_ajax', 'Seguridad\ModuloController@send_ajax');

//=== GESTOR DE PERMISOS ===
  Route::get('/permiso', 'Seguridad\PermisoController@index')->name('modulo');
    Route::match(["get", "post"], '/permiso/view_jqgrid', 'Seguridad\PermisoController@view_jqgrid');
    Route::post('/permiso/send_ajax', 'Seguridad\PermisoController@send_ajax');

Route::get('/dashboard1', 'Dashboard\Dashboard1Controller@index')->name('home');
