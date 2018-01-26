<?php

namespace App\Http\Controllers\Rrhh;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;
use App\Libraries\UtilClass;

use App\Models\Seguridad\SegPermisoRol;
use App\Models\Seguridad\SegLdUser;
use App\Models\Institucion\InstLugarDependencia;
use App\Models\Institucion\InstUnidadDesconcentrada;
use App\Models\Rrhh\RrhhBiometrico;
use App\Models\Rrhh\RrhhLogAlerta;
use App\Models\Rrhh\RrhhLogMarcacion;
use App\Models\Rrhh\RrhhPersonaBiometrico;
use App\User;

use TADPHP\TADFactory;
use TADPHP\TAD;

use Exception;

class BiometricoController extends Controller
{
    private $estado;
    private $e_conexion;
    private $encoding;

    private $rol_id;
    private $permisos;
    private $tipo_emisor;
    private $tipo_alerta;
    private $tipo_marcacion;

    public function __construct()
    {
        $this->middleware('auth');

        $this->estado = [
            '1' => 'HABILITADO CON RED',
            '2' => 'INHABILITADO',
            '3' => 'HABILITADO SIN RED'
        ];

        $this->e_conexion = [
            '1' => 'CON CONEXION',
            '2' => 'SIN CONEXION',
            '3' => 'SIN RED'
        ];

        $this->encoding = [
            '1' => 'utf-8',
            '2' => 'iso8859-1'
        ];

        $this->tipo_emisor = [
            '1' => 'CRON',
            '2' => 'MANUAL',
            '3' => 'EDITAR',
            '4' => 'REGISTRO DE USUARIO'
        ];

        $this->tipo_alerta = [
            '1' => 'ERROR DE CONEXION',
            '2' => 'ARRAY SIN DATOS'
        ];

        $this->tipo_marcacion = [
            '1' => 'POR RED MEDIANTE CRON',
            '2' => 'POR RED PULSANDO BOTON',
            '3' => 'DESDE ARCHIVO SUBIDO',
            '4' => 'POR DIGITAL PERSONA'
        ];
    }

    public function index()
    {
        $this->rol_id   = Auth::user()->rol_id;
        $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                            ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                            ->select("seg_permisos.codigo")
                            ->get()
                            ->toArray();
        if(in_array(['codigo' => '0601'], $this->permisos))
        {
            $user_id = Auth::user()->id;

            $consulta1 = SegLdUser::where("seg_ld_users.user_id", "=", $user_id)
                    ->select('lugar_dependencia_id')
                    ->get()
                    ->toArray();

            $array_where = 'estado=1';
            if(count($consulta1) > 0)
            {
                $c_1_sw        = TRUE;
                $c_2_sw        = TRUE;
                $array_where_1 = '';
                foreach ($consulta1 as $valor)
                {
                    if($valor['lugar_dependencia_id'] == '1')
                    {
                        $c_2_sw = FALSE;
                        break;
                    }

                    if($c_1_sw)
                    {
                        $array_where_1 .= " AND (id=" . $valor['lugar_dependencia_id'];
                        $c_1_sw      = FALSE;
                    }
                    else
                    {
                        $array_where_1 .= " OR id=" . $valor['lugar_dependencia_id'];
                    }
                }
                $array_where_1 .= ")";

                if($c_2_sw)
                {
                    $array_where .= $array_where_1;
                }
            }
            else
            {
                $array_where .= " AND id=0";
            }

            $data = [
                'rol_id'                  => $this->rol_id,
                'permisos'                => $this->permisos,
                'title'                   => 'Gestor de Biometricos',
                'home'                    => 'Inicio',
                'sistema'                 => 'Biometricos',
                'modulo'                  => 'Gestor de Biometricos',
                'title_table'             => 'Biometricos',
                'estado_array'            => $this->estado,
                'e_conexion_array'        => $this->e_conexion,
                'encoding_array'          => $this->encoding,
                'tipo_emisor_array'       => $this->tipo_emisor,
                'tipo_alerta_array'       => $this->tipo_alerta,
                'lugar_dependencia_array' => InstLugarDependencia::whereRaw($array_where)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray()
            ];
            return view('rrhh.biometrico.biometrico')->with($data);
        }
        else
        {
            return back()->withInput();
        }
    }

    public function view_jqgrid(Request $request)
    {
        if( ! $request->ajax())
        {
            $respuesta = [
                'page'    => 0,
                'total'   => 0,
                'records' => 0
            ];
            return json_encode($respuesta);
        }

        $tipo = $request->input('tipo');

        switch($tipo)
        {
            case '1':
                $jqgrid = new JqgridClass($request);

                $tabla1 = "rrhh_biometricos";
                $tabla2 = "inst_unidades_desconcentradas";
                $tabla3 = "inst_lugares_dependencia";

                $select = "
                    $tabla1.id,
                    $tabla1.unidad_desconcentrada_id,
                    $tabla1.estado,
                    $tabla1.e_conexion,
                    $tabla1.fs_conexion,
                    $tabla1.fb_conexion,
                    $tabla1.f_log_asistencia,
                    $tabla1.codigo_af,
                    $tabla1.ip,
                    $tabla1.internal_id,
                    $tabla1.com_key,
                    $tabla1.soap_port,
                    $tabla1.udp_port,
                    $tabla1.encoding,
                    $tabla1.description,

                    a2.lugar_dependencia_id,
                    a2.nombre AS unidad_desconcentrada,

                    a3.nombre AS lugar_dependencia
                ";

                $array_where = 'TRUE';

                $user_id = Auth::user()->id;

                $consulta1 = SegLdUser::where("user_id", "=", $user_id)
                    ->select('lugar_dependencia_id')
                    ->get()
                    ->toArray();
                if(count($consulta1) > 0)
                {
                    $c_1_sw        = TRUE;
                    $c_2_sw        = TRUE;
                    $array_where_1 = '';
                    foreach ($consulta1 as $valor)
                    {
                        if($valor['lugar_dependencia_id'] == '1')
                        {
                            $c_2_sw = FALSE;
                            break;
                        }

                        if($c_1_sw)
                        {
                            $array_where_1 .= " AND (a2.lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
                            $c_1_sw        = FALSE;
                        }
                        else
                        {
                            $array_where_1 .= " OR a2.lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
                        }
                    }
                    $array_where_1 .= ")";

                    if($c_2_sw)
                    {
                        $array_where .= $array_where_1;
                    }
                }
                else
                {
                    $array_where .= " AND a2.lugar_dependencia_id=0 AND ";
                }

                $array_where .= $jqgrid->getWhere();

                $count = RrhhBiometrico::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.unidad_desconcentrada_id")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.lugar_dependencia_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = RrhhBiometrico::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.unidad_desconcentrada_id")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.lugar_dependencia_id")
                    ->whereRaw($array_where)
                    ->select(DB::raw($select))
                    ->orderBy($limit_offset['sidx'], $limit_offset['sord'])
                    ->offset($limit_offset['start'])
                    ->limit($limit_offset['limit'])
                    ->get()
                    ->toArray();

                $respuesta = [
                    'page'    => $limit_offset['page'],
                    'total'   => $limit_offset['total_pages'],
                    'records' => $count
                ];

                $i = 0;
                foreach ($query as $row)
                {
                    $val_array = array(
                        'estado'                   => $row["estado"],
                        'unidad_desconcentrada_id' => $row["unidad_desconcentrada_id"],
                        'lugar_dependencia_id'     => $row["lugar_dependencia_id"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',
                        $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                        $this->utilitarios(array('tipo' => '2', 'e_conexion' => $row["e_conexion"])),
                        $row["fs_conexion"],
                        $row["fb_conexion"],
                        $row["f_log_asistencia"],
                        $row["lugar_dependencia"],
                        $row["unidad_desconcentrada"],
                        "MP-" . $row["codigo_af"],
                        $row["ip"],
                        $row["internal_id"],
                        $row["com_key"],
                        $row["soap_port"],
                        $row["udp_port"],
                        $row["encoding"],
                        $row["description"],
                        //=== VARIABLES OCULTOS ===
                            json_encode($val_array)
                    );
                    $i++;
                }
                return json_encode($respuesta);
                break;
            case '2':
                $jqgrid = new JqgridClass($request);

                $tabla1 = "rrhh_log_alertas";

                $select = "
                    id,
                    biometrico_id,
                    tipo_emisor,
                    tipo_alerta,
                    f_alerta,
                    mensaje
                ";

                $array_where = 'TRUE';

                $array_where .= " AND biometrico_id=" . $request->input('biometrico_id');

                $array_where .= $jqgrid->getWhere();

                $count = RrhhLogAlerta::whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = RrhhLogAlerta::whereRaw($array_where)
                    ->select(DB::raw($select))
                    ->orderBy($limit_offset['sidx'], $limit_offset['sord'])
                    ->offset($limit_offset['start'])
                    ->limit($limit_offset['limit'])
                    ->get()
                    ->toArray();

                $respuesta = [
                    'page'    => $limit_offset['page'],
                    'total'   => $limit_offset['total_pages'],
                    'records' => $count
                ];

                $i = 0;
                foreach ($query as $row)
                {
                    $val_array = array(
                        'biometrico_id' => $row["biometrico_id"],
                        'tipo_emisor'   => $row["tipo_emisor"],
                        'tipo_alerta'   => $row["tipo_alerta"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        $row["f_alerta"],
                        $this->tipo_emisor[$row["tipo_emisor"]],
                        $this->utilitarios(array('tipo' => '3', 'tipo_alerta' => $row["tipo_alerta"])),
                        $row["mensaje"],
                        //=== VARIABLES OCULTOS ===
                            json_encode($val_array)
                    );
                    $i++;
                }
                return json_encode($respuesta);
                break;
            default:
                $respuesta = [
                    'page'    => 0,
                    'total'   => 0,
                    'records' => 0
                ];
                return json_encode($respuesta);
                break;
        }
    }

    public function send_ajax(Request $request)
    {
        if( ! $request->ajax())
        {
            $respuesta = [
                'sw'        => 0,
                'titulo'    => 'GESTOR DE USUARIO',
                'respuesta' => 'No es solicitud AJAX.'
            ];
            return json_encode($respuesta);
        }

        $tipo = $request->input('tipo');

        switch($tipo)
        {
            // === INSERT UPDATE ===
            case '1':
                // === SEGURIDAD ===
                    $this->rol_id   = Auth::user()->rol_id;
                    $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                                        ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                                        ->select("seg_permisos.codigo")
                                        ->get()
                                        ->toArray();

                // === LIBRERIAS ===
                    $util = new UtilClass();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>GESTOR DE BIOMETRICOS</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );
                    $opcion = 'n';

                    $fs_conexion = date("Y-m-d H:i:s");

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if($id != '')
                    {
                        $opcion = 'e';
                        if(!in_array(['codigo' => '0603'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '0602'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                // === VALIDATE ===
                    $estado = trim($request->input('estado'));
                    try
                    {
                        if($estado == '1')
                        {
                            $this->validate($request,[
                                'lugar_dependencia_id'     => 'required',
                                'unidad_desconcentrada_id' => 'required',
                                'codigo_af'                => 'required',
                                'ip'                       => 'required|ipv4',
                                'internal_id'              => 'required|numeric',
                                'com_key'                  => 'required',
                                'soap_port'                => 'required|numeric',
                                'udp_port'                 => 'required|numeric'
                            ],
                            [
                                'lugar_dependencia_id.required'     => 'El campo LUGAR DE DEPENDENCIA es obligatorio.',

                                'unidad_desconcentrada_id.required' => 'El campo UNIDAD DESCONCENTRADA es obligatorio.',

                                'codigo_af.required'                => 'El campo CODIGO ACTIVO FIJO es obligatorio.',

                                'ip.required'                       => 'El campo IP es obligatorio.',
                                'ip.ipv4'                           => 'El campo IP debe ser una dirección IPv4 válida.',

                                'internal_id.required'              => 'El campo ID USUARIO es obligatorio.',
                                'internal_id.numeric'               => 'El campo ID USUARIO debe ser un número.',

                                'com_key.required'                  => 'El campo LLAVE COM es obligatorio.',

                                'soap_port.required'                => 'El campo PUERTO SOAP es obligatorio.',
                                'soap_port.numeric'                 => 'El campo PUERTO SOAP debe ser un número.',

                                'udp_port.required'                 => 'El campo PUERTO UDP es obligatorio.',
                                'udp_port.numeric'                  => 'El campo PUERTO UDP debe ser un número.'
                            ]);
                        }
                        else
                        {
                            $this->validate($request,[
                                'lugar_dependencia_id'     => 'required',
                                'unidad_desconcentrada_id' => 'required',
                                'codigo_af'                => 'required'
                            ],
                            [
                                'lugar_dependencia_id.required'     => 'El campo LUGAR DE DEPENDENCIA es obligatorio.',

                                'unidad_desconcentrada_id.required' => 'El campo UNIDAD DESCONCENTRADA es obligatorio.',

                                'codigo_af.required'                => 'El campo CODIGO ACTIVO FIJO es obligatorio.'
                            ]);
                        }
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                // === VARIABLES ===
                    $lugar_dependencia_id     = trim($request->input('lugar_dependencia_id'));
                    $unidad_desconcentrada_id = trim($request->input('unidad_desconcentrada_id'));
                    $codigo_af_mp             = trim($request->input('codigo_af'));
                    $ip                       = $request->input('ip', null);
                    $internal_id              = $request->input('internal_id', null);
                    $com_key                  = $request->input('com_key', null);
                    $soap_port                = $request->input('soap_port', null);
                    $udp_port                 = $request->input('udp_port', null);
                    $encoding                 = null;
                    $e_conexion               = 3;
                    $fb_conexion              = null;

                    $codigo_af_array = explode("-", $codigo_af_mp);
                    $codigo_af       = $codigo_af_array[1];

                // === VERIFICANDO CONEXION ===
                    if($estado == '1')
                    {
                        $encoding = $this->encoding['1'];
                        $data_conexion = [
                            'ip'            => $ip,                 // '192.168.30.30' '200.107.241.111' by default (totally useless!!!).
                            'internal_id'   => $internal_id,        // 1 by default.
                            'com_key'       => $com_key,            // 0 by default.
                            //'description' => '',                  // 'N/A' by default.
                            'soap_port'     => $soap_port,          // 80 by default,
                            'udp_port'      => $udp_port,           // 4370 by default.
                            'encoding'      => $encoding            // iso8859-1 by default.
                        ];

                        $tad_factory = new TADFactory($data_conexion);
                        $tad         = $tad_factory->get_instance();

                        if($opcion == 'n')
                        {
                            try
                            {
                                $fb_conexion_array = $tad->get_date()->to_array();
                                $fb_conexion       = $fb_conexion_array['Row']['Date'] . ' ' . $fb_conexion_array['Row']['Time'];
                                $e_conexion        = 1;

                                // $f_biometrico           = date("d/m/Y H:i:s", strtotime($fh_biometrico_array['Row']['Date'] . ' ' . $fh_biometrico_array['Row']['Time']));
                            }
                            catch (Exception $e)
                            {
                                // $respuesta['error_sw'] = 3;
                                // $respuesta['error']    = $e . '';
                                $respuesta['respuesta'] .= "No se pudo conectar a " . $ip . "<br>Verifique la IP.";
                                return json_encode($respuesta);
                            }
                        }
                        else
                        {
                            try
                            {
                                $fb_conexion_array = $tad->get_date()->to_array();
                                $fb_conexion       = $fb_conexion_array['Row']['Date'] . ' ' . $fb_conexion_array['Row']['Time'];
                                $e_conexion        = 1;
                            }
                            catch (Exception $e)
                            {
                                $respuesta['respuesta'] .= "No se pudo conectar a " . $ip . "<br>Verifique la conexión.<br>";
                                $e_conexion             = 2;

                                $error = '' . $e;
                                $error_array = explode("Stack trace:", $error);

                                $iu                = new RrhhLogAlerta;
                                $iu->biometrico_id = $id;
                                $iu->tipo_emisor   = 3;
                                $iu->tipo_alerta   = 1;
                                $iu->f_alerta      = $fs_conexion;
                                $iu->mensaje       = $error_array[0];
                                $iu->save();
                            }
                        }
                    }

                //=== OPERACION ===
                    if($opcion == 'n')
                    {
                        $c_codigo_af = RrhhBiometrico::where('codigo_af', '=', $codigo_af)->count();
                        if($c_codigo_af < 1)
                        {
                            $sw_ip = TRUE;
                            if($estado == '1')
                            {
                                $c_ip = RrhhBiometrico::where('ip', '=', $ip)->count();
                                if($c_ip > 0)
                                {
                                    $sw_ip = FALSE;
                                }
                            }

                            if($sw_ip)
                            {
                                $iu                           = new RrhhBiometrico;
                                $iu->unidad_desconcentrada_id = $unidad_desconcentrada_id;
                                $iu->estado                   = $estado;
                                $iu->e_conexion               = $e_conexion;
                                $iu->fs_conexion              = $fs_conexion;
                                $iu->fb_conexion              = $fb_conexion;
                                $iu->codigo_af                = $codigo_af;
                                $iu->ip                       = $ip;
                                $iu->internal_id              = $internal_id;
                                $iu->com_key                  = $com_key;
                                $iu->soap_port                = $soap_port;
                                $iu->udp_port                 = $udp_port;
                                $iu->encoding                 = $encoding;
                                $iu->save();

                                $respuesta['respuesta'] .= "El BIOMETRICO fue registrado con éxito.";
                                $respuesta['sw']         = 1;
                            }
                            else
                            {
                                $respuesta['respuesta'] .= "La IP ya fue registrada.";
                            }
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El CODIGO DEL ACTIVO FIJO ya fue registrado.";
                        }
                    }
                    else
                    {
                        $c_codigo_af = RrhhBiometrico::where('codigo_af', '=', $codigo_af)->where('id', '<>', $id)->count();
                        if($c_codigo_af < 1)
                        {
                            $sw_ip = TRUE;
                            if($estado == '1')
                            {
                                $c_ip = RrhhBiometrico::where('ip', '=', $ip)->where('id', '<>', $id)->count();
                                if($c_ip > 0)
                                {
                                    $sw_ip = FALSE;
                                }
                            }

                            if($sw_ip)
                            {
                                $iu                           = RrhhBiometrico::find($id);
                                $iu->unidad_desconcentrada_id = $unidad_desconcentrada_id;
                                $iu->estado                   = $estado;
                                $iu->e_conexion               = $e_conexion;
                                $iu->fs_conexion              = $fs_conexion;
                                $iu->fb_conexion              = $fb_conexion;
                                $iu->codigo_af                = $codigo_af;
                                $iu->ip                       = $ip;
                                $iu->internal_id              = $internal_id;
                                $iu->com_key                  = $com_key;
                                $iu->soap_port                = $soap_port;
                                $iu->udp_port                 = $udp_port;
                                $iu->encoding                 = $encoding;
                                $iu->save();

                                $respuesta['respuesta'] .= "El BIOMETRICO se edito con éxito.";
                                $respuesta['sw']         = 1;
                                $respuesta['iu']         = 2;
                            }
                            else
                            {
                                $respuesta['respuesta'] .= "La IP ya fue registrada.";
                            }
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El CODIGO DEL ACTIVO FIJO ya fue registrado.";
                        }
                    }

                return json_encode($respuesta);
                break;
            // === REVISAR CONEXION ===
            case '2':
                // === SEGURIDAD ===
                    $this->rol_id   = Auth::user()->rol_id;
                    $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                                        ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                                        ->select("seg_permisos.codigo")
                                        ->get()
                                        ->toArray();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>ALERTA</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo
                    );

                    $fs_conexion = date("Y-m-d H:i:s");

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if(!in_array(['codigo' => '0604'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para REVISAR CONEXION.";
                        return json_encode($respuesta);
                    }

                // === CONSULTA ===
                    $biometrico = RrhhBiometrico::where('id', '=', $id)
                        ->first()
                        ->toArray();

                // === VERIFICANDO CONEXION ===
                    if($biometrico['estado'] == '1')
                    {
                        $encoding = $this->encoding['1'];
                        $data_conexion = [
                            'ip'            => $biometrico['ip'],
                            'internal_id'   => $biometrico['internal_id'],
                            'com_key'       => $biometrico['com_key'],
                            'soap_port'     => $biometrico['soap_port'],
                            'udp_port'      => $biometrico['udp_port'],
                            'encoding'      => $this->encoding['1']
                        ];

                        $tad_factory = new TADFactory($data_conexion);
                        $tad         = $tad_factory->get_instance();

                        try
                        {
                            $fb_conexion_array = $tad->get_date()->to_array();
                            $fb_conexion       = $fb_conexion_array['Row']['Date'] . ' ' . $fb_conexion_array['Row']['Time'];
                            $e_conexion        = 1;

                            $respuesta['respuesta'] .= "Se conecto a " . $biometrico['ip'] . ".";
                            $respuesta['sw'] = 1;
                        }
                        catch (Exception $e)
                        {
                            $respuesta['respuesta'] .= "No se logro conectar a " . $biometrico['ip'] . "<br>Verifique la conexión.<br>";
                            $e_conexion             = 2;
                            $fb_conexion            = null;

                            $error = '' . $e;
                            $error_array = explode("Stack trace:", $error);

                            $iu                = new RrhhLogAlerta;
                            $iu->biometrico_id = $id;
                            $iu->tipo_emisor   = 2;
                            $iu->tipo_alerta   = 1;
                            $iu->f_alerta      = $fs_conexion;
                            $iu->mensaje       = $error_array[0];
                            $iu->save();
                        }
                    }

                //=== OPERACION ===
                    $iu              = RrhhBiometrico::find($id);
                    $iu->e_conexion  = $e_conexion;
                    $iu->fs_conexion = $fs_conexion;
                    $iu->fb_conexion = $fb_conexion;
                    $iu->save();

                return json_encode($respuesta);
                break;
            // === SINCRONIZAR FECHA Y HORA ===
            case '3':
                // === SEGURIDAD ===
                    $this->rol_id   = Auth::user()->rol_id;
                    $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                                        ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                                        ->select("seg_permisos.codigo")
                                        ->get()
                                        ->toArray();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>ALERTA</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo
                    );

                    $fs_conexion = date("Y-m-d H:i:s");

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if(!in_array(['codigo' => '0605'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para SINCRONIZAR FECHA Y HORA.";
                        return json_encode($respuesta);
                    }

                // === CONSULTA ===
                    $biometrico = RrhhBiometrico::where('id', '=', $id)
                        ->first()
                        ->toArray();

                // === VERIFICANDO CONEXION ===
                    if($biometrico['estado'] == '1')
                    {
                        $encoding = $this->encoding['1'];
                        $data_conexion = [
                            'ip'            => $biometrico['ip'],
                            'internal_id'   => $biometrico['internal_id'],
                            'com_key'       => $biometrico['com_key'],
                            'soap_port'     => $biometrico['soap_port'],
                            'udp_port'      => $biometrico['udp_port'],
                            'encoding'      => $biometrico['encoding']
                        ];

                        $tad_factory = new TADFactory($data_conexion);
                        $tad         = $tad_factory->get_instance();

                        try
                        {

                            $tad->set_date(['date' => date("Y-m-d", strtotime($fs_conexion)), 'time' => date("H:i:s", strtotime($fs_conexion))]);

                            $fb_conexion_array = $tad->get_date()->to_array();

                            $fb_conexion       = $fb_conexion_array['Row']['Date'] . ' ' . $fb_conexion_array['Row']['Time'];
                            $e_conexion        = 1;

                            $respuesta['respuesta'] .= "Se sincronizo la fecha y la hora del biometrico en la dirección IP " . $biometrico['ip'] . ".";
                            $respuesta['sw']        = 1;
                        }
                        catch (Exception $e)
                        {
                            $respuesta['respuesta'] .= "No se logro sincronizar la fecha y la hora del biometrico en la dirección IP " . $biometrico['ip'] . "<br>Verifique la conexión.<br>";
                            $e_conexion             = 2;
                            $fb_conexion            = null;

                            $error = '' . $e;
                            $error_array = explode("Stack trace:", $error);

                            $iu                = new RrhhLogAlerta;
                            $iu->biometrico_id = $id;
                            $iu->tipo_emisor   = 2;
                            $iu->tipo_alerta   = 1;
                            $iu->f_alerta      = $fs_conexion;
                            $iu->mensaje       = $error_array[0];
                            $iu->save();
                        }
                    }

                //=== OPERACION ===
                    $iu              = RrhhBiometrico::find($id);
                    $iu->e_conexion  = $e_conexion;
                    $iu->fs_conexion = $fs_conexion;
                    $iu->fb_conexion = $fb_conexion;
                    $iu->save();

                return json_encode($respuesta);
                break;
            // === REINICIAR BIOMETRICO ===
            case '4':
                // === SEGURIDAD ===
                    $this->rol_id   = Auth::user()->rol_id;
                    $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                                        ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                                        ->select("seg_permisos.codigo")
                                        ->get()
                                        ->toArray();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>ALERTA</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo
                    );

                    $fs_conexion = date("Y-m-d H:i:s");

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if(!in_array(['codigo' => '0606'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para REINICIAR BIOMETRICO.";
                        return json_encode($respuesta);
                    }

                // === CONSULTA ===
                    $biometrico = RrhhBiometrico::where('id', '=', $id)
                        ->first()
                        ->toArray();

                // === VERIFICANDO CONEXION ===
                    if($biometrico['estado'] == '1')
                    {
                        $encoding = $this->encoding['1'];
                        $data_conexion = [
                            'ip'            => $biometrico['ip'],
                            'internal_id'   => $biometrico['internal_id'],
                            'com_key'       => $biometrico['com_key'],
                            'soap_port'     => $biometrico['soap_port'],
                            'udp_port'      => $biometrico['udp_port'],
                            'encoding'      => $this->encoding['1']
                        ];

                        $tad_factory = new TADFactory($data_conexion);
                        $tad         = $tad_factory->get_instance();

                        try
                        {
                            $fb_conexion_array = $tad->get_date()->to_array();
                            $fb_conexion       = $fb_conexion_array['Row']['Date'] . ' ' . $fb_conexion_array['Row']['Time'];

                            $tad->restart();

                            $e_conexion = 1;

                            $respuesta['respuesta'] .= "Se reinicio el biométrico en la dirección IP " . $biometrico['ip'] . ".";
                            $respuesta['sw']        = 1;
                        }
                        catch (Exception $e)
                        {
                            $respuesta['respuesta'] .= "No se logro reiniciar el biométrico en la dirección IP " . $biometrico['ip'] . "<br>Verifique la conexión.<br>";
                            $e_conexion             = 2;
                            $fb_conexion            = null;

                            $error = '' . $e;
                            $error_array = explode("Stack trace:", $error);

                            $iu                = new RrhhLogAlerta;
                            $iu->biometrico_id = $id;
                            $iu->tipo_emisor   = 2;
                            $iu->tipo_alerta   = 1;
                            $iu->f_alerta      = $fs_conexion;
                            $iu->mensaje       = $error_array[0];
                            $iu->save();
                        }
                    }

                //=== OPERACION ===
                    $iu              = RrhhBiometrico::find($id);
                    $iu->e_conexion  = $e_conexion;
                    $iu->fs_conexion = $fs_conexion;
                    $iu->fb_conexion = $fb_conexion;
                    $iu->save();

                return json_encode($respuesta);
                break;
            // === APAGAR BIOMETRICO ===
            case '5':
                // === SEGURIDAD ===
                    $this->rol_id   = Auth::user()->rol_id;
                    $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                                        ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                                        ->select("seg_permisos.codigo")
                                        ->get()
                                        ->toArray();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>ALERTA</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo
                    );

                    $fs_conexion = date("Y-m-d H:i:s");

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if(!in_array(['codigo' => '0607'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para APAGAR BIOMETRICO.";
                        return json_encode($respuesta);
                    }

                // === CONSULTA ===
                    $biometrico = RrhhBiometrico::where('id', '=', $id)
                        ->first()
                        ->toArray();

                // === VERIFICANDO CONEXION ===
                    if($biometrico['estado'] == '1')
                    {
                        $encoding = $this->encoding['1'];
                        $data_conexion = [
                            'ip'            => $biometrico['ip'],
                            'internal_id'   => $biometrico['internal_id'],
                            'com_key'       => $biometrico['com_key'],
                            'soap_port'     => $biometrico['soap_port'],
                            'udp_port'      => $biometrico['udp_port'],
                            'encoding'      => $this->encoding['1']
                        ];

                        $tad_factory = new TADFactory($data_conexion);
                        $tad         = $tad_factory->get_instance();

                        try
                        {
                            $fb_conexion_array = $tad->get_date()->to_array();
                            $fb_conexion       = $fb_conexion_array['Row']['Date'] . ' ' . $fb_conexion_array['Row']['Time'];

                            $tad->poweroff();

                            $e_conexion = 1;

                            $respuesta['respuesta'] .= "Se apago el biométrico en la dirección IP " . $biometrico['ip'] . ".";
                            $respuesta['sw']        = 1;
                        }
                        catch (Exception $e)
                        {
                            $respuesta['respuesta'] .= "No se logro apagar el biométrico en la dirección IP " . $biometrico['ip'] . "<br>Verifique la conexión.<br>";
                            $e_conexion             = 2;
                            $fb_conexion            = null;

                            $error = '' . $e;
                            $error_array = explode("Stack trace:", $error);

                            $iu                = new RrhhLogAlerta;
                            $iu->biometrico_id = $id;
                            $iu->tipo_emisor   = 2;
                            $iu->tipo_alerta   = 1;
                            $iu->f_alerta      = $fs_conexion;
                            $iu->mensaje       = $error_array[0];
                            $iu->save();
                        }
                    }

                //=== OPERACION ===
                    $iu              = RrhhBiometrico::find($id);
                    $iu->e_conexion  = $e_conexion;
                    $iu->fs_conexion = $fs_conexion;
                    $iu->fb_conexion = $fb_conexion;
                    $iu->save();

                return json_encode($respuesta);
                break;
            // === OBTENER REGISTRO DE ASISTENCIA ===
            case '6':
                // === SEGURIDAD ===
                    $this->rol_id   = Auth::user()->rol_id;
                    $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                                        ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                                        ->select("seg_permisos.codigo")
                                        ->get()
                                        ->toArray();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>ALERTA</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo
                    );

                    $fs_conexion = date("Y-m-d H:i:s");
                    $f_actual    = date("Y-m-d");

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if(!in_array(['codigo' => '0608'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para OBTENER REGISTRO DE ASISTENCIA.";
                        return json_encode($respuesta);
                    }

                // === CONSULTA ===
                    $biometrico = RrhhBiometrico::where('id', '=', $id)
                        ->first()
                        ->toArray();

                // === VERIFICANDO CONEXION ===
                    if($biometrico['estado'] == '1')
                    {
                        $data_conexion = [
                            'ip'            => $biometrico['ip'],
                            'internal_id'   => $biometrico['internal_id'],
                            'com_key'       => $biometrico['com_key'],
                            'soap_port'     => $biometrico['soap_port'],
                            'udp_port'      => $biometrico['udp_port'],
                            'encoding'      => $biometrico['encoding']
                        ];

                        $tad_factory = new TADFactory($data_conexion);
                        $tad         = $tad_factory->get_instance();

                        $f_log_asistencia_sw = TRUE;
                        try
                        {
                            $fb_conexion_array = $tad->get_date()->to_array();
                            $fb_conexion       = $fb_conexion_array['Row']['Date'] . ' ' . $fb_conexion_array['Row']['Time'];

                            $e_conexion = 1;

                            $att_logs = $tad->get_att_log();

                            if($f_actual <= '2018-01-31')
                            {
                                $log_marcacion = $att_logs->filter_by_date([
                                    'start' => '2018-01-18',
                                    'end'   => $f_actual
                                ])->to_array();
                            }
                            else
                            {
                                $log_marcacion = $att_logs->filter_by_date([
                                    'start' => $f_actual,
                                    'end'   => $f_actual
                                ])->to_array();
                            }

                            if(count($log_marcacion))
                            {
                                $data1     = [];
                                $sw_insert = FALSE;
                                foreach($log_marcacion as $row)
                                {
                                    if(isset($row['PIN']))
                                    {
                                        $consulta1 = RrhhPersonaBiometrico::where("biometrico_id", "=", $id)
                                            ->where("n_documento_biometrico", "=",  $row['PIN'])
                                            ->select('persona_id')
                                            ->first();

                                        if(count($consulta1) > 0)
                                        {
                                            $consulta2 = RrhhLogMarcacion::where("biometrico_id", "=", $id)
                                                ->where("persona_id", "=", $consulta1['persona_id'])
                                                ->where("f_marcacion", "=", $row['DateTime'])
                                                ->select('persona_id')
                                                ->first();

                                            if(count($consulta2) < 1)
                                            {
                                                $data1[] = [
                                                    'biometrico_id'          => $id,
                                                    'persona_id'             => $consulta1['persona_id'],
                                                    'tipo_marcacion'         => 2,
                                                    'n_documento_biometrico' => $row['PIN'],
                                                    'f_marcacion'            => $row['DateTime']
                                                ];

                                                $sw_insert = TRUE;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        foreach($row as $valor1)
                                        {
                                            $consulta1 = RrhhPersonaBiometrico::where("biometrico_id", "=", $id)
                                                ->where("n_documento_biometrico", "=",  $valor1['PIN'])
                                                ->select('persona_id')
                                                ->first();

                                            if(count($consulta1) > 0)
                                            {
                                                $consulta2 = RrhhLogMarcacion::where("biometrico_id", "=", $id)
                                                    ->where("persona_id", "=", $consulta1['persona_id'])
                                                    ->where("f_marcacion", "=", $valor1['DateTime'])
                                                    ->select('persona_id')
                                                    ->first();

                                                if(count($consulta2) < 1)
                                                {
                                                    $data1[] = [
                                                        'biometrico_id'          => $id,
                                                        'persona_id'             => $consulta1['persona_id'],
                                                        'tipo_marcacion'         => 2,
                                                        'n_documento_biometrico' => $valor1['PIN'],
                                                        'f_marcacion'            => $valor1['DateTime']
                                                    ];

                                                    $sw_insert = TRUE;
                                                }
                                            }
                                        }
                                    }
                                }

                                if($sw_insert)
                                {
                                    RrhhLogMarcacion::insert($data1);
                                    $respuesta['respuesta'] .= "Se obtuvo los registros de asistencia de la siguiente dirección " . $biometrico['ip'] . ".";
                                    $respuesta['sw']        = 1;
                                }
                                else
                                {
                                    $respuesta['respuesta'] .= "No existe registros de asistencia en la siguiente dirección " . $biometrico['ip'] . ".";
                                    $f_log_asistencia_sw = FALSE;
                                }
                                // $tad->delete_data(['value' => 3]);
                            }
                            else
                            {
                                $respuesta['respuesta'] .= "No existe registros de asistencia en la siguiente dirección " . $biometrico['ip'] . ".";
                                $f_log_asistencia_sw = FALSE;
                            }

                            // $log_marcacion = $tad->get_att_log()->to_array();

                            // if(count($log_marcacion))
                            // {
                            //     $data1  = [];
                            //     foreach($log_marcacion as $row)
                            //     {
                            //         if(isset($row['PIN']))
                            //         {
                            //             $consulta1 = RrhhPersonaBiometrico::where("biometrico_id", "=", $id)
                            //                 ->where("n_documento_biometrico", "=",  $row['PIN'])
                            //                 ->select('persona_id')
                            //                 ->first();

                            //             if(count($consulta1) > 0)
                            //             {
                            //                 $data1[] = [
                            //                     'biometrico_id'          => $id,
                            //                     'persona_id'             => $consulta1['persona_id'],
                            //                     'tipo_marcacion'         => 2,
                            //                     'n_documento_biometrico' => $row['PIN'],
                            //                     'f_marcacion'            => $row['DateTime']
                            //                 ];
                            //             }
                            //         }
                            //         else
                            //         {
                            //             foreach($row as $valor1)
                            //             {
                            //                 $consulta1 = RrhhPersonaBiometrico::where("biometrico_id", "=", $id)
                            //                     ->where("n_documento_biometrico", "=",  $valor1['PIN'])
                            //                     ->select('persona_id')
                            //                     ->first();

                            //                 if(count($consulta1) > 0)
                            //                 {
                            //                     $data1[] = [
                            //                         'biometrico_id'          => $id,
                            //                         'persona_id'             => $consulta1['persona_id'],
                            //                         'tipo_marcacion'         => 2,
                            //                         'n_documento_biometrico' => $valor1['PIN'],
                            //                         'f_marcacion'            => $valor1['DateTime']
                            //                     ];
                            //                 }
                            //             }
                            //         }
                            //     }

                            //     RrhhLogMarcacion::insert($data1);

                            //     // $tad->delete_data(['value' => 3]);

                            //     $respuesta['respuesta'] .= "Se obtuvo los registros de asistencia de la siguiente dirección " . $biometrico['ip'] . ".";
                            //     $respuesta['sw']        = 1;
                            // }
                            // else
                            // {
                            //     $respuesta['respuesta'] .= "No existe registros de asistencia en la siguiente dirección " . $biometrico['ip'] . ".";
                            //     $f_log_asistencia_sw = FALSE;
                            // }
                        }
                        catch (Exception $e)
                        {
                            $respuesta['respuesta'] .= "No se logró obtener los registros de asistencia de la siguiente dirección " . $biometrico['ip'] . "<br>Verifique la conexión.<br>";
                            $e_conexion             = 2;
                            $fb_conexion            = null;

                            $error       = '' . $e;
                            $error_array = explode("Stack trace:", $error);

                            $iu                = new RrhhLogAlerta;
                            $iu->biometrico_id = $id;
                            $iu->tipo_emisor   = 2;
                            $iu->tipo_alerta   = 1;
                            $iu->f_alerta      = $fs_conexion;
                            $iu->mensaje       = $error_array[0];
                            $iu->save();

                            $f_log_asistencia_sw = FALSE;
                        }

                        if($f_log_asistencia_sw)
                        {
                            $iu                   = RrhhBiometrico::find($id);
                            $iu->e_conexion       = $e_conexion;
                            $iu->fs_conexion      = $fs_conexion;
                            $iu->fb_conexion      = $fb_conexion;
                            $iu->f_log_asistencia = $fs_conexion;
                            $iu->save();
                        }
                        else
                        {
                            $iu              = RrhhBiometrico::find($id);
                            $iu->e_conexion  = $e_conexion;
                            $iu->fs_conexion = $fs_conexion;
                            $iu->fb_conexion = $fb_conexion;
                            $iu->save();
                        }
                    }

                //=== OPERACION ===
                return json_encode($respuesta);
                break;

            // === SELECT2 UNIDAD DESCONCENTRADA ===
            case '103':
                $respuesta = [
                    'tipo' => $tipo,
                    'sw'   => 1
                ];
                if($request->has('lugar_dependencia_id'))
                {
                    $lugar_dependencia_id  = $request->input('lugar_dependencia_id');
                    $query = InstUnidadDesconcentrada::where("lugar_dependencia_id", "=", $lugar_dependencia_id)
                                ->select('id', 'nombre')
                                ->get()
                                ->toArray();
                    if(count($query) > 0)
                    {
                        $respuesta['consulta'] = $query;
                        $respuesta['sw']       = 2;
                    }
                }
                return json_encode($respuesta);
                break;
            default:
                break;
        }
    }

    private function utilitarios($valor)
    {
        switch($valor['tipo'])
        {
            case '1':
                switch($valor['estado'])
                {
                    case '1':
                        $respuesta = '<span class="label label-primary font-sm">' . $this->estado[$valor['estado']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->estado[$valor['estado']] . '</span>';
                        return($respuesta);
                        break;
                    case '3':
                        $respuesta = '<span class="label label-warning font-sm">' . $this->estado[$valor['estado']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
                        return($respuesta);
                        break;
                }
                break;
            case '2':
                switch($valor['e_conexion'])
                {
                    case '1':
                        $respuesta = '<span class="label label-primary font-sm">' . $this->e_conexion[$valor['e_conexion']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->e_conexion[$valor['e_conexion']] . '</span>';
                        return($respuesta);
                        break;
                    case '3':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->e_conexion[$valor['e_conexion']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
                        return($respuesta);
                        break;
                }
                break;
            case '3':
                switch($valor['tipo_alerta'])
                {
                    case '1':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->tipo_alerta[$valor['tipo_alerta']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-warning font-sm">' . $this->tipo_alerta[$valor['tipo_alerta']] . '</span>';
                        return($respuesta);
                        break;
                    case '3':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->tipo_alerta[$valor['tipo_alerta']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
                        return($respuesta);
                        break;
                }
                break;
            default:
                break;
        }
    }
}