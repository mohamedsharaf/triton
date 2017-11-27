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

// Route::get('/', function () {
//     return view('welcome');
// });

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

//=== GESTOR DE MODULOS ===
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/home', 'HomeController@index')->name('home');
        Route::post('/home/send_ajax', 'HomeController@send_ajax');

//=== GESTOR DE MODULOS ===
    Route::get('/modulo', 'Seguridad\ModuloController@index')->name('modulo');
        // Route::post('/modulo/view_jqgrid', 'Seguridad\ModuloController@view_jqgrid');
        Route::match(["get", "post"], '/modulo/view_jqgrid', 'Seguridad\ModuloController@view_jqgrid');
        Route::post('/modulo/send_ajax', 'Seguridad\ModuloController@send_ajax');

//=== GESTOR DE PERMISOS ===
    Route::get('/permiso', 'Seguridad\PermisoController@index')->name('permiso');
        Route::match(["get", "post"], '/permiso/view_jqgrid', 'Seguridad\PermisoController@view_jqgrid');
        Route::post('/permiso/send_ajax', 'Seguridad\PermisoController@send_ajax');

//=== GESTOR DE ROLES ===
    Route::get('/rol', 'Seguridad\RolController@index')->name('rol');
        Route::match(["get", "post"], '/rol/view_jqgrid', 'Seguridad\RolController@view_jqgrid');
        Route::post('/rol/send_ajax', 'Seguridad\RolController@send_ajax');

//=== GESTOR DE ROLES ===
    Route::get('/permiso_rol', 'Seguridad\PermisoRolController@index')->name('permiso_rol');
        Route::match(["get", "post"], '/permiso_rol/view_jqgrid', 'Seguridad\PermisoRolController@view_jqgrid');
        Route::post('/permiso_rol/send_ajax', 'Seguridad\PermisoRolController@send_ajax');

//=== GESTOR DE USUARIOS ===
    Route::get('/usuario', 'Seguridad\UsuarioController@index')->name('usuario');
        Route::match(["get", "post"], '/usuario/view_jqgrid', 'Seguridad\UsuarioController@view_jqgrid');
        Route::post('/usuario/send_ajax', 'Seguridad\UsuarioController@send_ajax');

//=== UNIDADES DESCONCENTRADAS ===
    Route::get('/unidad_desconcentrada', 'Institucion\UnidadDesconcentradaController@index')->name('unidad_desconcentrada');
        Route::match(["get", "post"], '/unidad_desconcentrada/view_jqgrid', 'Institucion\UnidadDesconcentradaController@view_jqgrid');
        Route::post('/unidad_desconcentrada/send_ajax', 'Institucion\UnidadDesconcentradaController@send_ajax');

//=== AUO ===
    Route::get('/auo', 'Institucion\AuoController@index')->name('auo');
        Route::match(["get", "post"], '/auo/view_jqgrid', 'Institucion\AuoController@view_jqgrid');
        Route::post('/auo/send_ajax', 'Institucion\AuoController@send_ajax');

//=== CARGOS ===
    Route::get('/cargo', 'Institucion\CargoController@index')->name('cargo');
        Route::match(["get", "post"], '/cargo/view_jqgrid', 'Institucion\CargoController@view_jqgrid');
        Route::post('/cargo/send_ajax', 'Institucion\CargoController@send_ajax');

//=== PERSONA ===
    Route::get('/persona', 'Rrhh\PersonaController@index')->name('persona');
        Route::match(["get", "post"], '/persona/view_jqgrid', 'Rrhh\PersonaController@view_jqgrid');
        Route::post('/persona/send_ajax', 'Rrhh\PersonaController@send_ajax');

//=== BIOMETRICOS ===
    Route::get('/biometrico', 'Rrhh\BiometricoController@index')->name('biometrico');
        Route::match(["get", "post"], '/biometrico/view_jqgrid', 'Rrhh\BiometricoController@view_jqgrid');
        Route::post('/biometrico/send_ajax', 'Rrhh\BiometricoController@send_ajax');

//=== PERSONAS - BIOMETRICOS ===
    Route::get('/persona_biometrico', 'Rrhh\PersonaBiometricoController@index')->name('persona_biometrico');
        Route::match(["get", "post"], '/persona_biometrico/view_jqgrid', 'Rrhh\PersonaBiometricoController@view_jqgrid');
        Route::post('/persona_biometrico/send_ajax', 'Rrhh\PersonaBiometricoController@send_ajax');

Route::get('/dashboard1', 'Dashboard\Dashboard1Controller@index')->name('home');
