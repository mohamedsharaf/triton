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
        Route::match(["get", "post"], '/home/view_jqgrid', 'HomeController@view_jqgrid');
        Route::post('/home/send_ajax', 'HomeController@send_ajax');
        Route::match(["get", "post"], '/home/reportes', 'HomeController@reportes');
//=== GESTOR DE GRUPOS ===
    Route::get('/grupo', 'Seguridad\GrupoController@index')->name('grupo');
    Route::match(["get", "post"], '/grupo/view_jqgrid', 'Seguridad\GrupoController@view_jqgrid');
    Route::post('/grupo/send_ajax', 'Seguridad\GrupoController@send_ajax');
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
        Route::match(["get", "post"], '/usuario/reportes', 'Seguridad\UsuarioController@reportes');

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
        Route::match(["get", "post"], '/cargo/reportes', 'Institucion\CargoController@reportes');

//=== PERSONA ===
    Route::get('/persona', 'Rrhh\PersonaController@index')->name('persona');
        Route::match(["get", "post"], '/persona/view_jqgrid', 'Rrhh\PersonaController@view_jqgrid');
        Route::post('/persona/send_ajax', 'Rrhh\PersonaController@send_ajax');
        Route::match(["get", "post"], '/persona/reportes', 'Rrhh\PersonaController@reportes');

//=== SOLICITUD DE SALIDA ===
    Route::get('/solicitud_salida', 'Rrhh\SolicitudSalidaController@index')->name('solicitud_salida');
        Route::match(["get", "post"], '/solicitud_salida/view_jqgrid', 'Rrhh\SolicitudSalidaController@view_jqgrid');
        Route::post('/solicitud_salida/send_ajax', 'Rrhh\SolicitudSalidaController@send_ajax');
        Route::match(["get", "post"], '/solicitud_salida/reportes', 'Rrhh\SolicitudSalidaController@reportes');

//=== CONFIRMAR SALIDA ===
    Route::get('/confirmar_salida', 'Rrhh\ConfirmarSalidaController@index')->name('confirmar_salida');
        Route::match(["get", "post"], '/confirmar_salida/view_jqgrid', 'Rrhh\ConfirmarSalidaController@view_jqgrid');
        Route::post('/confirmar_salida/send_ajax', 'Rrhh\ConfirmarSalidaController@send_ajax');
        Route::match(["get", "post"], '/confirmar_salida/reportes', 'Rrhh\ConfirmarSalidaController@reportes');

//=== CONFIRMAR SALIDA RRHH ===
    Route::get('/confirmar_salida_rrhh', 'Rrhh\ConfirmarSalidaRrhhController@index')->name('confirmar_salida_rrhh');
        Route::match(["get", "post"], '/confirmar_salida_rrhh/view_jqgrid', 'Rrhh\ConfirmarSalidaRrhhController@view_jqgrid');
        Route::post('/confirmar_salida_rrhh/send_ajax', 'Rrhh\ConfirmarSalidaRrhhController@send_ajax');
        Route::match(["get", "post"], '/confirmar_salida_rrhh/reportes', 'Rrhh\ConfirmarSalidaRrhhController@reportes');

//=== FUNCIONARIOS ===
    Route::get('/funcionario', 'Rrhh\FuncionarioController@index')->name('funcionario');
        Route::match(["get", "post"], '/funcionario/view_jqgrid', 'Rrhh\FuncionarioController@view_jqgrid');
        Route::post('/funcionario/send_ajax', 'Rrhh\FuncionarioController@send_ajax');
        Route::match(["get", "post"], '/funcionario/reportes', 'Rrhh\FuncionarioController@reportes');

//=== GESTOR DE ASISTENCIAS ===
    Route::get('/asistencia', 'Rrhh\AsistenciaController@index')->name('asistencia');
        Route::match(["get", "post"], '/asistencia/view_jqgrid', 'Rrhh\AsistenciaController@view_jqgrid');
        Route::post('/asistencia/send_ajax', 'Rrhh\AsistenciaController@send_ajax');
        Route::match(["get", "post"], '/asistencia/reportes', 'Rrhh\AsistenciaController@reportes');

//=== CONTROL DE SALIDA PARTICULAR ===
    Route::get('/salida_particular', 'Rrhh\SalidaParticularController@index')->name('salida_particular');
        Route::match(["get", "post"], '/salida_particular/view_jqgrid', 'Rrhh\SalidaParticularController@view_jqgrid');
        Route::post('/salida_particular/send_ajax', 'Rrhh\SalidaParticularController@send_ajax');
        Route::match(["get", "post"], '/salida_particular/reportes', 'Rrhh\SalidaParticularController@reportes');

//=== HORARIOS ===
    Route::get('/horario', 'Rrhh\HorarioController@index')->name('horario');
        Route::match(["get", "post"], '/horario/view_jqgrid', 'Rrhh\HorarioController@view_jqgrid');
        Route::post('/horario/send_ajax', 'Rrhh\HorarioController@send_ajax');
        Route::match(["get", "post"], '/horario/reportes', 'Rrhh\HorarioController@reportes');

//=== FERIADO, TOLERANCIA Y HORARIO CONTINUO ===
    Route::get('/fthc', 'Rrhh\FthcController@index')->name('fthc');
        Route::match(["get", "post"], '/fthc/view_jqgrid', 'Rrhh\FthcController@view_jqgrid');
        Route::post('/fthc/send_ajax', 'Rrhh\FthcController@send_ajax');
        Route::match(["get", "post"], '/fthc/reportes', 'Rrhh\FthcController@reportes');

//=== TIPOS DE SALIDA ===
    Route::get('/tipo_salida', 'Rrhh\TipoSalidaController@index')->name('tipo_salida');
        Route::match(["get", "post"], '/tipo_salida/view_jqgrid', 'Rrhh\TipoSalidaController@view_jqgrid');
        Route::post('/tipo_salida/send_ajax', 'Rrhh\TipoSalidaController@send_ajax');
        Route::match(["get", "post"], '/tipo_salida/reportes', 'Rrhh\TipoSalidaController@reportes');

//=== BIOMETRICOS ===
    Route::get('/biometrico', 'Rrhh\BiometricoController@index')->name('biometrico');
        Route::match(["get", "post"], '/biometrico/view_jqgrid', 'Rrhh\BiometricoController@view_jqgrid');
        Route::post('/biometrico/send_ajax', 'Rrhh\BiometricoController@send_ajax');

//=== PERSONAS - BIOMETRICOS ===
    Route::get('/persona_biometrico', 'Rrhh\PersonaBiometricoController@index')->name('persona_biometrico');
        Route::match(["get", "post"], '/persona_biometrico/view_jqgrid', 'Rrhh\PersonaBiometricoController@view_jqgrid');
        Route::post('/persona_biometrico/send_ajax', 'Rrhh\PersonaBiometricoController@send_ajax');

//=== MARCACION DEL BIOMETRICO ===
    Route::get('/marcacion_biometrico', 'Rrhh\MarcacionBiometricoController@index')->name('marcacion_biometrico');
        Route::match(["get", "post"], '/marcacion_biometrico/view_jqgrid', 'Rrhh\MarcacionBiometricoController@view_jqgrid');
        Route::post('/marcacion_biometrico/send_ajax', 'Rrhh\MarcacionBiometricoController@send_ajax');
        Route::match(["get", "post"], '/marcacion_biometrico/reportes', 'Rrhh\MarcacionBiometricoController@reportes');

//=== MARCACION DEL BIOMETRICO ===
    Route::get('/solicitud_dpvt', 'Dpvt\SolicitudController@index')->name('solicitud_dpvt');
        Route::match(["get", "post"], '/solicitud_dpvt/view_jqgrid', 'Dpvt\SolicitudController@view_jqgrid');
        Route::post('/solicitud_dpvt/send_ajax', 'Dpvt\SolicitudController@send_ajax');
        Route::match(["get", "post"], '/solicitud_dpvt/reportes', 'Dpvt\SolicitudController@reportes');

//=== I4 - DETENCIÓN PREVENTIVA ===
    Route::get('/detencion_preventiva', 'I4\DetencionPreventivaController@index')->name('detencion_preventiva');
    Route::match(["get", "post"], '/detencion_preventiva/view_jqgrid', 'I4\DetencionPreventivaController@view_jqgrid');
    Route::post('/detencion_preventiva/send_ajax', 'I4\DetencionPreventivaController@send_ajax');
    Route::match(["get", "post"], '/detencion_preventiva/reportes', 'I4\DetencionPreventivaController@reportes');

//=== I4 - RECINTO CARCELARIO ===
    Route::get('/recinto_carcelario', 'I4\RecintoCarcelarioController@index')->name('recinto_carcelario');
    Route::match(["get", "post"], '/recinto_carcelario/view_jqgrid', 'I4\RecintoCarcelarioController@view_jqgrid');
    Route::post('/recinto_carcelario/send_ajax', 'I4\RecintoCarcelarioController@send_ajax');
    Route::match(["get", "post"], '/recinto_carcelario/reportes', 'I4\RecintoCarcelarioController@reportes');

//=== I4 - PLATAFORMA ===
    Route::get('/plataforma', 'I4\PlataformaController@index')->name('plataforma');
    Route::match(["get", "post"], '/plataforma/view_jqgrid', 'I4\PlataformaController@view_jqgrid');
    Route::post('/plataforma/send_ajax', 'I4\PlataformaController@send_ajax');
    Route::match(["get", "post"], '/plataforma/reportes', 'I4\PlataformaController@reportes');

//=== I4 - NOTIFICACIONES ===
    Route::get('/notificacion', 'I4\NotificacionController@index')->name('notificacion');
    Route::post('/notificacion/send_ajax', 'I4\NotificacionController@send_ajax');

//=== I4 - CENTRAL DE NOTIFICACIONES ===
    Route::get('/central_notificacion', 'I4\CentralNotificacionController@index')->name('central_notificacion');
    Route::match(["get", "post"], '/central_notificacion/view_jqgrid', 'I4\CentralNotificacionController@view_jqgrid');
    Route::post('/central_notificacion/send_ajax', 'I4\CentralNotificacionController@send_ajax');
    Route::match(["get", "post"], '/central_notificacion/reportes', 'I4\CentralNotificacionController@reportes');

//=== DERIVACIONES - INSTITUCIONES ===
    Route::get('/institucion', 'Institucion\InstitucionController@index')->name('institucion');
    Route::match(["get", "post"], '/institucion/view_jqgrid', 'Institucion\InstitucionController@view_jqgrid');
    Route::post('/institucion/send_ajax', 'Institucion\InstitucionController@send_ajax');

//=== DERIVACIONES ===
    Route::get('/derivacion', 'Dpvt\DerivacionController@index')->name('derivacion');
    Route::match(["get", "post"], '/derivacion/view_jqgrid', 'Dpvt\DerivacionController@view_jqgrid');
    Route::post('/derivacion/send_ajax', 'Dpvt\DerivacionController@send_ajax');

//=== DERIVACIONES - REPORTES ===
    Route::get('/derivacion', 'Dpvt\DerivacionController@index')->name('derivacion');
    Route::match(["get", "post"], '/derivacion/reportes', 'Dpvt\DerivacionController@reportes');

Route::get('/dashboard1', 'Dashboard\Dashboard1Controller@index')->name('home');
