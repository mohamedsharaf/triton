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
use App\Models\Institucion\InstAuo;
use App\Models\Institucion\InstTipoCargo;
use App\Models\Institucion\InstCargo;

use App\Models\Rrhh\RrhhPersona;
use App\Models\Rrhh\RrhhFuncionario;
use App\Models\Rrhh\RrhhFthc;
use App\Models\Rrhh\RrhhHorario;
use App\Models\Rrhh\RrhhTipoSalida;
use App\Models\Rrhh\RrhhSalida;
use App\Models\Rrhh\RrhhAsistencia;
use App\Models\Rrhh\RrhhLogMarcacion;

use Maatwebsite\Excel\Facades\Excel;
use PDF;

use Exception;

class AsistenciaController extends Controller
{
    private $estado;
    private $omision;
    private $falta;
    private $fthc;
    private $f_corte;

    private $rol_id;
    private $permisos;

    private $reporte_1;
    private $reporte_data_1;

    public function __construct()
    {
        $this->middleware('auth');

        $this->estado = [
            '1' => 'HABILITADA',
            '2' => 'ANULADA',
            '3' => 'CERRADA'
        ];

        $this->omision = [
            '1' => 'NO MARCADO',
            '2' => 'MARCADO',
            '3' => 'REGULARIZADA'
        ];

        $this->falta = [
            '1' => 'FALTA',
            '2' => 'ASISTIO',
            '3' => 'REGULARIZADA',
            '4' => 'SIN HORARIO'
        ];

        $this->fthc = [
            '1' => 'FERIADO',
            '2' => 'TOLERANCIA',
            '3' => 'HORARIO CONTINUO'
        ];

        $this->omitir = [
            '1' => 'INCLUIR',
            '2' => 'VACACIONES',
            '3' => 'MIGRACION'
        ];

        $this->tipo_salida = [
            '1' => 'LICENCIA OFICIAL',
            '2' => 'LICENCIA PARTICULAR',
            '3' => 'VACACIONES',
            '4' => 'CUMPLEAÑOS',
            '5' => 'LICENCIA SIN GOCE DE HABER'
        ];

        $this->con_sin_retorno = [
            '1' => 'CON RETORNO',
            '2' => 'SIN RETORNO'
        ];

        $this->periodo = [
            '1' => 'MAÑANA',
            '2' => 'TARDE'
        ];

        $this->f_corte = '2018-01-15';

        $this->public_dir = '/image/logo';
        $this->public_url = 'storage/rrhh/salidas/solicitud_salida/';
    }

    public function index()
    {
        $this->rol_id   = Auth::user()->rol_id;
        $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
            ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
            ->select("seg_permisos.codigo")
            ->get()
            ->toArray();

        if(in_array(['codigo' => '1301'], $this->permisos))
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
                        $c_1_sw        = FALSE;
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
                'title'                   => 'Gestor de asistencia',
                'home'                    => 'Inicio',
                'sistema'                 => 'Recursos humanos',
                'modulo'                  => 'Asistencias',
                'title_table'             => 'Asistencias',
                'public_url'              => $this->public_url,
                'estado_array'            => $this->estado,
                'omision_array'           => $this->omision,
                'falta_array'             => $this->falta,
                'f_corte'                 => $this->f_corte,
                'lugar_dependencia_array' => InstLugarDependencia::whereRaw($array_where)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray()
            ];
            return view('rrhh.asistencia.asistencia')->with($data);
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
                $where_concatenar = "";
                if($request->has('fecha'))
                {
                    $where_concatenar = " AND fecha='" . $request->input('fecha') . "'";
                }

                $jqgrid = new JqgridClass($request);

                $tabla1  = "rrhh_asistencias";
                $tabla2  = "rrhh_personas";
                $tabla3  = "inst_cargos";
                $tabla4  = "inst_auos";
                $tabla5  = "inst_unidades_desconcentradas";
                $tabla6  = "inst_lugares_dependencia";
                $tabla7  = "rrhh_log_marcaciones";
                $tabla8  = "rrhh_horarios";
                $tabla9  = "rrhh_salidas";
                $tabla10 = "rrhh_tipos_salida";

                $select = "
                    $tabla1.id,
                    $tabla1.persona_id,

                    $tabla1.persona_id_rrhh_h1_i,
                    $tabla1.persona_id_rrhh_h1_s,
                    $tabla1.persona_id_rrhh_h2_i,
                    $tabla1.persona_id_rrhh_h2_s,

                    $tabla1.cargo_id,
                    $tabla1.unidad_desconcentrada_id,

                    $tabla1.log_marcaciones_id_i1,
                    $tabla1.log_marcaciones_id_s1,
                    $tabla1.log_marcaciones_id_i2,
                    $tabla1.log_marcaciones_id_s2,

                    $tabla1.horario_id_1,
                    $tabla1.horario_id_2,

                    $tabla1.salida_id_i1,
                    $tabla1.salida_id_s1,
                    $tabla1.salida_id_i2,
                    $tabla1.salida_id_s2,

                    $tabla1.fthc_id_h1,
                    $tabla1.fthc_id_h2,

                    $tabla1.estado,
                    $tabla1.fecha,

                    $tabla1.h1_i_omitir,
                    $tabla1.h1_s_omitir,
                    $tabla1.h2_i_omitir,
                    $tabla1.h2_s_omitir,

                    $tabla1.h1_min_retrasos,
                    $tabla1.h2_min_retrasos,

                    $tabla1.h1_descuento,
                    $tabla1.h2_descuento,

                    $tabla1.h1_i_omision_registro,
                    $tabla1.h1_s_omision_registro,
                    $tabla1.h2_i_omision_registro,
                    $tabla1.h2_s_omision_registro,

                    $tabla1.f_omision_registro,
                    $tabla1.e_omision_registro,

                    $tabla1.h1_falta,
                    $tabla1.h2_falta,

                    $tabla1.observaciones,
                    $tabla1.justificacion,

                    $tabla1.horario_1_i,
                    $tabla1.horario_1_s,

                    $tabla1.horario_2_i,
                    $tabla1.horario_2_s,

                    a2.n_documento,
                    a2.nombre AS nombre_persona,
                    a2.ap_paterno,
                    a2.ap_materno,

                    a3.lugar_dependencia_id AS lugar_dependencia_id_funcionario,
                    a3.nombre AS ud_funcionario,

                    a4.nombre AS lugar_dependencia_funcionario
                ";

                $array_where = "TRUE" . $where_concatenar;

                $user_id = Auth::user()->id;
                $rol_id  = Auth::user()->rol_id;

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
                        if(($valor['lugar_dependencia_id'] == '1') && ($rol_id == '1' || $rol_id == '5'))
                        {
                            $c_2_sw = FALSE;
                            break;
                        }

                        if($c_1_sw)
                        {
                            $array_where_1 .= " AND (a3.lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
                            $c_1_sw        = FALSE;
                        }
                        else
                        {
                            $array_where_1 .= " OR a3.lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
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
                    $array_where .= " AND a3.lugar_dependencia_id=0 AND ";
                }

                $array_where .= $jqgrid->getWhere();

                $count = RrhhAsistencia::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.persona_id")
                    ->leftJoin("$tabla5 AS a3", "a3.id", "=", "$tabla1.unidad_desconcentrada_id")
                    ->leftJoin("$tabla6 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = RrhhAsistencia::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.persona_id")
                    ->leftJoin("$tabla5 AS a3", "a3.id", "=", "$tabla1.unidad_desconcentrada_id")
                    ->leftJoin("$tabla6 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
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
                        'persona_id'      => $row["persona_id"],

                        'persona_id_rrhh_h1_i' => $row["persona_id_rrhh_h1_i"],
                        'persona_id_rrhh_h1_s' => $row["persona_id_rrhh_h1_s"],
                        'persona_id_rrhh_h2_i' => $row["persona_id_rrhh_h2_i"],
                        'persona_id_rrhh_h2_s' => $row["persona_id_rrhh_h2_s"],

                        'cargo_id'                 => $row["cargo_id"],
                        'unidad_desconcentrada_id' => $row["unidad_desconcentrada_id"],

                        'log_marcaciones_id_i1' => $row["log_marcaciones_id_i1"],
                        'log_marcaciones_id_s1' => $row["log_marcaciones_id_s1"],
                        'log_marcaciones_id_i2' => $row["log_marcaciones_id_i2"],
                        'log_marcaciones_id_s2' => $row["log_marcaciones_id_s2"],

                        'horario_id_1' => $row["horario_id_1"],
                        'horario_id_2' => $row["horario_id_2"],

                        'salida_id_i1' => $row["salida_id_i1"],
                        'salida_id_s1' => $row["salida_id_s1"],
                        'salida_id_i2' => $row["salida_id_i2"],
                        'salida_id_s2' => $row["salida_id_s2"],

                        'fthc_id_h1' => $row["fthc_id_h1"],
                        'fthc_id_h2' => $row["fthc_id_h2"],

                        'estado' => $row["estado"],

                        'h1_min_retrasos' => $row["h1_min_retrasos"],
                        'h2_min_retrasos' => $row["h2_min_retrasos"],

                        'h1_i_omitir' => $row["h1_i_omitir"],
                        'h1_s_omitir' => $row["h1_s_omitir"],
                        'h2_i_omitir' => $row["h2_i_omitir"],
                        'h2_s_omitir' => $row["h2_s_omitir"],

                        'h1_i_omision_registro' => $row["h1_i_omision_registro"],
                        'h1_s_omision_registro' => $row["h1_s_omision_registro"],
                        'h2_i_omision_registro' => $row["h2_i_omision_registro"],
                        'h2_s_omision_registro' => $row["h2_s_omision_registro"],

                        'f_omision_registro' => $row["f_omision_registro"],
                        'e_omision_registro' => $row["e_omision_registro"],

                        'h1_falta' => $row["h1_falta"],
                        'h2_falta' => $row["h2_falta"],

                        'horario_1_i' => $row["horario_1_i"],
                        'horario_1_s' => $row["horario_1_s"],

                        'horario_2_i' => $row["horario_2_i"],
                        'horario_2_s' => $row["horario_2_s"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',

                        $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),

                        $row["fecha"],

                        $row["n_documento"],
                        $row["nombre_persona"],
                        $row["ap_paterno"],
                        $row["ap_materno"],

                        $this->utilitarios(array('tipo' => '3', 'horario' => $row["horario_1_i"], 'log_marcaciones_id' => $row["log_marcaciones_id_i1"], 'salida_id' => $row["salida_id_i1"], 'fthc_id' => $row["fthc_id_h1"], 'id' => $row["id"], 'fecha' => $row["fecha"], 'persona_id' => $row["persona_id"], 'persona_id_rrhh' => $row["persona_id_rrhh_h1_i"])),
                        '',
                        $this->utilitarios(array('tipo' => '3', 'horario' => $row["horario_1_s"], 'log_marcaciones_id' => $row["log_marcaciones_id_s1"], 'salida_id' => $row["salida_id_s1"], 'fthc_id' => $row["fthc_id_h1"], 'id' => $row["id"], 'fecha' => $row["fecha"], 'persona_id' => $row["persona_id"], 'persona_id_rrhh' => $row["persona_id_rrhh_h1_s"])),
                        '',
                        $this->utilitarios(array('tipo' => '2', 'min_retrasos' => $row["h1_min_retrasos"])),

                        $this->utilitarios(array('tipo' => '3', 'horario' => $row["horario_2_i"], 'log_marcaciones_id' => $row["log_marcaciones_id_i2"], 'salida_id' => $row["salida_id_i2"], 'fthc_id' => $row["fthc_id_h2"], 'id' => $row["id"], 'fecha' => $row["fecha"], 'persona_id' => $row["persona_id"], 'persona_id_rrhh' => $row["persona_id_rrhh_h2_i"])),
                        '',
                        $this->utilitarios(array('tipo' => '3', 'horario' => $row["horario_2_s"], 'log_marcaciones_id' => $row["log_marcaciones_id_s2"], 'salida_id' => $row["salida_id_s2"], 'fthc_id' => $row["fthc_id_h2"], 'id' => $row["id"], 'fecha' => $row["fecha"], 'persona_id' => $row["persona_id"], 'persona_id_rrhh' => $row["persona_id_rrhh_h2_s"])),
                        '',
                        $this->utilitarios(array('tipo' => '2', 'min_retrasos' => $row["h2_min_retrasos"], 'fecha' => $row["fecha"], 'persona_id' => $row["persona_id"])),

                        $row["ud_funcionario"],
                        $row["lugar_dependencia_funcionario"],

                        //=== VARIABLES OCULTOS ===
                            json_encode($val_array)
                    );
                    $i++;
                }
                return json_encode($respuesta);
                break;
            case '2':
                if($request->has('persona_id'))
                {
                    $persona_id = $request->input('persona_id');
                }
                else
                {
                    $respuesta = [
                        'page'    => 0,
                        'total'   => 0,
                        'records' => 0
                    ];
                    return json_encode($respuesta);
                }

                if($request->has('f_marcacion'))
                {
                    $f_marcacion = $request->input('f_marcacion');
                }
                else
                {
                    $respuesta = [
                        'page'    => 0,
                        'total'   => 0,
                        'records' => 0
                    ];
                    return json_encode($respuesta);
                }

                $jqgrid = new JqgridClass($request);

                $tabla1 = "rrhh_log_marcaciones";
                $tabla2 = "rrhh_biometricos";
                $tabla3 = "inst_unidades_desconcentradas";
                $tabla4 = "inst_lugares_dependencia";

                $select = "
                    $tabla1.id,
                    $tabla1.biometrico_id,
                    $tabla1.persona_id,
                    $tabla1.f_marcacion,

                    a2.unidad_desconcentrada_id,
                    a2.codigo_af,
                    a2.ip,

                    a3.lugar_dependencia_id,
                    a3.nombre AS unidad_desconcentrada,

                    a4.nombre AS lugar_dependencia
                ";

                $array_where = "$tabla1.persona_id=" . $persona_id . " AND $tabla1.f_marcacion::text LIKE '%" . $f_marcacion . "%' ";

                $array_where .= $jqgrid->getWhere();

                $count = RrhhLogMarcacion::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.biometrico_id")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.unidad_desconcentrada_id")
                    ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = RrhhLogMarcacion::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.biometrico_id")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.unidad_desconcentrada_id")
                    ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
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
                        'biometrico_id'            => $row["biometrico_id"],
                        'unidad_desconcentrada_id' => $row["unidad_desconcentrada_id"],
                        'lugar_dependencia_id'     => $row["lugar_dependencia_id"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        $row["f_marcacion"],
                        "MP-" . $row["codigo_af"],
                        $row["unidad_desconcentrada"],
                        $row["lugar_dependencia"],

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
            // === INSERT FECHAS PARA LAS ASISTENCIAS ===
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
                        'titulo'     => '<div class="text-center"><strong>Asistencia</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );
                    $opcion = 'n';

                // === PERMISOS ===
                    if(!in_array(['codigo' => '1302'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para CREAR FECHAS PARA LAS ASISTENCIAS.";
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['fecha_del']                        = trim($request->input('fecha_del'));
                    $data1['fecha_al']                         = trim($request->input('fecha_al'));
                    $data1['lugar_dependencia_id_funcionario'] = trim($request->input('lugar_dependencia_id_funcionario'));

                    $data1['persona_id']                 = trim($request->input('persona_id'));
                    $data1['unidad_desconcentrada_id']   = trim($request->input('unidad_desconcentrada_id'));

                    $data1['horario_id_1'] = trim($request->input('horario_id_1'));
                    $data1['horario_id_2'] = trim($request->input('horario_id_2'));

                    $data1['lugar_dependencia_id_cargo'] = trim($request->input('lugar_dependencia_id_cargo'));
                    $data1['auo_id']                     = trim($request->input('auo_id'));
                    $data1['cargo_id']                   = trim($request->input('cargo_id'));

                // === ANALISIS ===
                    if( ! ($data1['fecha_del'] <= $data1['fecha_al']))
                    {
                        $respuesta['respuesta'] .= "La FECHA DEL es mayor que la FECHA AL.";
                        return json_encode($respuesta);
                    }

                    if($request->has('persona_id'))
                    {
                        $numero_dias = (strtotime($data1['fecha_al']) - strtotime($data1['fecha_del']))/86400 +1;

                        $cantidad_registros = 0;

                        $fecha_acu = $data1['fecha_del'];

                        set_time_limit(3600);
                        ini_set('memory_limit','-1');

                        for($i=0; $i < $numero_dias; $i++)
                        {
                            $array_where = "fecha='" . $fecha_acu . "' AND persona_id=" . $data1['persona_id'];

                            $consulta2 = RrhhAsistencia::whereRaw($array_where)
                                ->count();

                            if($consulta2 < 1)
                            {
                                $consulta3 = RrhhHorario::where("id", "=", $data1['horario_id_1'])
                                    ->first();

                                $sw_asistencia_registra = FALSE;
                                switch(date('w', strtotime($fecha_acu)))
                                {
                                    // === DOMINGO ===
                                    case '0':
                                        if($consulta3['domingo'] == '2')
                                        {
                                            $sw_asistencia_registra = TRUE;
                                        }
                                        break;
                                    // === LUNES ===
                                    case '1':
                                        if($consulta3['lunes'] == '2')
                                        {
                                            $sw_asistencia_registra = TRUE;
                                        }
                                        break;
                                    // === MARTES ===
                                    case '2':
                                        if($consulta3['martes'] == '2')
                                        {
                                            $sw_asistencia_registra = TRUE;
                                        }
                                        break;
                                    // === MIERCOLES ===
                                    case '3':
                                        if($consulta3['miercoles'] == '2')
                                        {
                                            $sw_asistencia_registra = TRUE;
                                        }
                                        break;
                                    // === JUEVES ===
                                    case '4':
                                        if($consulta3['jueves'] == '2')
                                        {
                                            $sw_asistencia_registra = TRUE;
                                        }
                                        break;
                                    // === VIERNES ===
                                    case '5':
                                        if($consulta3['viernes'] == '2')
                                        {
                                            $sw_asistencia_registra = TRUE;
                                        }
                                        break;
                                    // === SABADO ===
                                    case '6':
                                        if($consulta3['sabado'] == '2')
                                        {
                                            $sw_asistencia_registra = TRUE;
                                        }
                                        break;
                                    default:
                                        break;
                                }

                                if($sw_asistencia_registra)
                                {
                                    $iu             = new RrhhAsistencia;
                                    $iu->persona_id = $data1['persona_id'];

                                    $iu->cargo_id                 = $data1['cargo_id'];
                                    $iu->unidad_desconcentrada_id = $data1['unidad_desconcentrada_id'];

                                    $iu->horario_id_1 = $data1['horario_id_1'];

                                    $iu->fecha = $fecha_acu;

                                    $iu->horario_1_i = $this->falta['1'];
                                    $iu->horario_1_s = $this->falta['1'];

                                    if($data1['horario_id_2'] != '')
                                    {
                                        $iu->horario_id_2 = $data1['horario_id_2'];

                                        $iu->horario_2_i = $this->falta['1'];
                                        $iu->horario_2_s = $this->falta['1'];
                                    }
                                    else
                                    {
                                        $iu->horario_id_2 = NULL;

                                        $iu->horario_2_i = $this->falta['4'];
                                        $iu->horario_2_s = $this->falta['4'];
                                    }

                                    $iu->save();

                                    $id = $iu->id;

                                    $cantidad_registros++;

                                    $array_where = "estado='1' AND fecha='" . $fecha_acu . "' AND lugar_dependencia_id=" . $data1['lugar_dependencia_id_funcionario'];

                                    $consulta4 = RrhhFthc::whereRaw($array_where)
                                        ->get()
                                        ->toArray();
                                    if(count($consulta4) > 0)
                                    {
                                        foreach($consulta4 as $row4)
                                        {
                                            switch($row4['tipo_fthc'])
                                            {
                                                // === FERIADO ===
                                                case '1':
                                                    $sw_1 = TRUE;
                                                    if($row4['unidad_desconcentrada_id'] != '')
                                                    {
                                                        if($data1['unidad_desconcentrada_id'] != $row4['unidad_desconcentrada_id'])
                                                        {
                                                            $sw_1 = FALSE;
                                                        }
                                                    }

                                                    if($sw_1)
                                                    {
                                                        $iu = RrhhAsistencia::find($id);

                                                        $iu->fthc_id_h1 = $row4['id'];

                                                        $iu->horario_1_i = $this->fthc['1'];
                                                        $iu->horario_1_s = $this->fthc['1'];

                                                        if($data1['horario_id_2'] != '')
                                                        {
                                                            $iu->fthc_id_h2 = $row4['id'];

                                                            $iu->horario_2_i = $this->fthc['1'];
                                                            $iu->horario_2_s = $this->fthc['1'];
                                                        }

                                                        $iu->save();
                                                    }
                                                    break;
                                                // === TOLERANCIA ===
                                                case '2':
                                                    if($data1['horario_id_2'] != '')
                                                    {
                                                        $sw_1 = TRUE;
                                                        if($row4['unidad_desconcentrada_id'] != '')
                                                        {
                                                            if($data1['unidad_desconcentrada_id'] != $row4['unidad_desconcentrada_id'])
                                                            {
                                                                $sw_1 = FALSE;
                                                            }
                                                        }

                                                        if($sw_1)
                                                        {
                                                            $sw_2 = TRUE;
                                                            if($row4['sexo'] != '')
                                                            {
                                                                $consulta5 = RrhhPersona::where('id', '=', $data1['persona_id'])->first();
                                                                if($consulta5['sexo'] != $row4['sexo'])
                                                                {
                                                                    $sw_2 = FALSE;
                                                                }
                                                            }
                                                            if($sw_2)
                                                            {
                                                                switch($row4['tipo_horario']) {
                                                                    case '1':
                                                                        $iu = RrhhAsistencia::find($id);

                                                                        $iu->fthc_id_h1 = $row4['id'];

                                                                        $iu->horario_1_i = $this->fthc['2'];
                                                                        $iu->horario_1_s = $this->fthc['2'];

                                                                        $iu->save();
                                                                        break;
                                                                    case '2':
                                                                        $iu = RrhhAsistencia::find($id);

                                                                        $iu->fthc_id_h2 = $row4['id'];

                                                                        $iu->horario_2_i = $this->fthc['2'];
                                                                        $iu->horario_2_s = $this->fthc['2'];

                                                                        $iu->save();
                                                                        break;
                                                                    default:
                                                                        break;
                                                                }
                                                            }
                                                        }
                                                    }
                                                    break;
                                                // === HORARIO CONTINUO ===
                                                case '3':
                                                    if($data1['horario_id_2'] != '')
                                                    {
                                                        $sw_1 = TRUE;
                                                        if($row4['unidad_desconcentrada_id'] != '')
                                                        {
                                                            if($data1['unidad_desconcentrada_id'] != $row4['unidad_desconcentrada_id'])
                                                            {
                                                                $sw_1 = FALSE;
                                                            }
                                                        }

                                                        if($sw_1)
                                                        {
                                                            $iu = RrhhAsistencia::find($id);

                                                            $iu->horario_id_1 = $row4['horario_id'];

                                                            $iu->fthc_id_h2 = $row4['id'];

                                                            $iu->horario_2_i = $this->fthc['3'];
                                                            $iu->horario_2_s = $this->fthc['3'];

                                                            $iu->save();
                                                        }
                                                    }
                                                    break;
                                                default:
                                                    break;
                                            }
                                        }
                                    }
                                }
                            }

                            $fecha_acu = date("Y-m-d", strtotime($fecha_acu . "+ 1 days"));
                        }

                        $respuesta['respuesta'] .= "Se registraron " . $cantidad_registros . " asistencias.";
                        $respuesta['sw']         = 1;
                    }
                    else
                    {
                        $tabla1 = "rrhh_funcionarios";
                        $tabla2 = "inst_unidades_desconcentradas";
                        $tabla3 = "rrhh_horarios";
                        $tabla4 = "rrhh_personas";

                        $select = "
                            $tabla1.id,
                            $tabla1.persona_id,
                            $tabla1.cargo_id,
                            $tabla1.unidad_desconcentrada_id,
                            $tabla1.horario_id_1,
                            $tabla1.horario_id_2,

                            a2.lugar_dependencia_id AS lugar_dependencia_id_funcionario,
                            a2.nombre AS ud_funcionario,

                            a3.estado AS estado_h1,
                            a3.tipo_horario AS tipo_horario_h1,
                            a3.h_ingreso AS h_ingreso_h1,
                            a3.h_salida AS h_salida_h1,
                            a3.tolerancia AS tolerancia_h1,
                            a3.marcacion_ingreso_del AS marcacion_ingreso_del_h1,
                            a3.marcacion_ingreso_al AS marcacion_ingreso_al_h1,
                            a3.marcacion_salida_del AS marcacion_salida_del_h1,
                            a3.marcacion_salida_al AS marcacion_salida_al_h1,
                            a3.lunes AS lunes_h1,
                            a3.martes AS martes_h1,
                            a3.miercoles AS miercoles_h1,
                            a3.jueves AS jueves_h1,
                            a3.viernes AS viernes_h1,
                            a3.sabado AS sabado_h1,
                            a3.domingo AS domingo_h1,

                            a4.estado AS estado_h2,
                            a4.tipo_horario AS tipo_horario_h2,
                            a4.h_ingreso AS h_ingreso_h2,
                            a4.h_salida AS h_salida_h2,
                            a4.tolerancia AS tolerancia_h2,
                            a4.marcacion_ingreso_del AS marcacion_ingreso_del_h2,
                            a4.marcacion_ingreso_al AS marcacion_ingreso_al_h2,
                            a4.marcacion_salida_del AS marcacion_salida_del_h2,
                            a4.marcacion_salida_al AS marcacion_salida_al_h2,
                            a4.lunes AS lunes_h2,
                            a4.martes AS martes_h2,
                            a4.miercoles AS miercoles_h2,
                            a4.jueves AS jueves_h2,
                            a4.viernes AS viernes_h2,
                            a4.sabado AS sabado_h2,
                            a4.domingo AS domingo_h2,

                            a5.sexo
                        ";

                        $array_where = "a2.lugar_dependencia_id=" . $data1['lugar_dependencia_id_funcionario'];

                        $consulta1 = RrhhFuncionario::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.unidad_desconcentrada_id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.horario_id_1")
                            ->leftJoin("$tabla3 AS a4", "a4.id", "=", "$tabla1.horario_id_2")
                            ->leftJoin("$tabla4 AS a5", "a5.id", "=", "$tabla1.persona_id")
                            ->whereRaw($array_where)
                            ->select(DB::raw($select))
                            ->get()
                            ->toArray();

                        if(count($consulta1) > 0)
                        {
                            $numero_dias = (strtotime($data1['fecha_al']) - strtotime($data1['fecha_del']))/86400 +1;

                            $cantidad_registros = 0;

                            foreach($consulta1 as $row1)
                            {
                                $fecha_acu = $data1['fecha_del'];
                                for($i=0; $i < $numero_dias; $i++)
                                {
                                    $array_where = "fecha='" . $fecha_acu . "' AND persona_id=" . $row1['persona_id'];

                                    $consulta2 = RrhhAsistencia::whereRaw($array_where)
                                        ->count();

                                    if($consulta2 < 1)
                                    {
                                        $sw_asistencia_registra = FALSE;
                                        switch(date('w', strtotime($fecha_acu)))
                                        {
                                            // === DOMINGO ===
                                            case '0':
                                                if($row1['domingo_h1'] == '2')
                                                {
                                                    $sw_asistencia_registra = TRUE;
                                                }
                                                break;
                                            // === LUNES ===
                                            case '1':
                                                if($row1['lunes_h1'] == '2')
                                                {
                                                    $sw_asistencia_registra = TRUE;
                                                }
                                                break;
                                            // === MARTES ===
                                            case '2':
                                                if($row1['martes_h1'] == '2')
                                                {
                                                    $sw_asistencia_registra = TRUE;
                                                }
                                                break;
                                            // === MIERCOLES ===
                                            case '3':
                                                if($row1['miercoles_h1'] == '2')
                                                {
                                                    $sw_asistencia_registra = TRUE;
                                                }
                                                break;
                                            // === JUEVES ===
                                            case '4':
                                                if($row1['jueves_h1'] == '2')
                                                {
                                                    $sw_asistencia_registra = TRUE;
                                                }
                                                break;
                                            // === VIERNES ===
                                            case '5':
                                                if($row1['viernes_h1'] == '2')
                                                {
                                                    $sw_asistencia_registra = TRUE;
                                                }
                                                break;
                                            // === SABADO ===
                                            case '6':
                                                if($row1['sabado_h1'] == '2')
                                                {
                                                    $sw_asistencia_registra = TRUE;
                                                }
                                                break;
                                            default:
                                                break;
                                        }

                                        if($sw_asistencia_registra)
                                        {
                                            $iu             = new RrhhAsistencia;
                                            $iu->persona_id = $row1['persona_id'];

                                            $iu->cargo_id                 = $row1['cargo_id'];
                                            $iu->unidad_desconcentrada_id = $row1['unidad_desconcentrada_id'];

                                            $iu->horario_id_1 = $row1['horario_id_1'];

                                            $iu->fecha = $fecha_acu;

                                            $iu->horario_1_i = $this->falta['1'];
                                            $iu->horario_1_s = $this->falta['1'];

                                            if($row1['horario_id_2'] != '')
                                            {
                                                $iu->horario_id_2 = $row1['horario_id_2'];

                                                $iu->horario_2_i = $this->falta['1'];
                                                $iu->horario_2_s = $this->falta['1'];
                                            }
                                            else
                                            {
                                                $iu->horario_id_2 = NULL;

                                                $iu->horario_2_i = $this->falta['4'];
                                                $iu->horario_2_s = $this->falta['4'];
                                            }

                                            $iu->save();

                                            $id = $iu->id;

                                            $cantidad_registros++;

                                            $array_where = "estado='1' AND fecha='" . $fecha_acu . "' AND lugar_dependencia_id=" . $row1['lugar_dependencia_id_funcionario'];

                                            $consulta3 = RrhhFthc::whereRaw($array_where)
                                                ->get()
                                                ->toArray();
                                            if(count($consulta3) > 0)
                                            {
                                                foreach($consulta3 as $row3)
                                                {
                                                    switch($row3['tipo_fthc'])
                                                    {
                                                        // === FERIADO ===
                                                        case '1':
                                                            $sw_1 = TRUE;
                                                            if($row3['unidad_desconcentrada_id'] != '')
                                                            {
                                                                if($row1['unidad_desconcentrada_id'] != $row3['unidad_desconcentrada_id'])
                                                                {
                                                                    $sw_1 = FALSE;
                                                                }
                                                            }

                                                            if($sw_1)
                                                            {
                                                                $iu = RrhhAsistencia::find($id);

                                                                $iu->fthc_id_h1 = $row3['id'];

                                                                $iu->horario_1_i = $this->fthc['1'];
                                                                $iu->horario_1_s = $this->fthc['1'];

                                                                if($row1['horario_id_2'] != '')
                                                                {
                                                                    $iu->fthc_id_h2 = $row3['id'];

                                                                    $iu->horario_2_i = $this->fthc['1'];
                                                                    $iu->horario_2_s = $this->fthc['1'];
                                                                }

                                                                $iu->save();
                                                            }
                                                            break;
                                                        // === TOLERANCIA ===
                                                        case '2':
                                                            if($row1['horario_id_2'] != '')
                                                            {
                                                                $sw_1 = TRUE;
                                                                if($row3['unidad_desconcentrada_id'] != '')
                                                                {
                                                                    if($row1['unidad_desconcentrada_id'] != $row3['unidad_desconcentrada_id'])
                                                                    {
                                                                        $sw_1 = FALSE;
                                                                    }
                                                                }

                                                                if($sw_1)
                                                                {
                                                                    $sw_2 = TRUE;
                                                                    if($row3['sexo'] != '')
                                                                    {
                                                                        if($row1['sexo'] != $row3['sexo'])
                                                                        {
                                                                            $sw_2 = FALSE;
                                                                        }
                                                                    }
                                                                    if($sw_2)
                                                                    {
                                                                        switch($row3['tipo_horario']) {
                                                                            case '1':
                                                                                $iu = RrhhAsistencia::find($id);

                                                                                $iu->fthc_id_h1 = $row3['id'];

                                                                                $iu->horario_1_i = $this->fthc['2'];
                                                                                $iu->horario_1_s = $this->fthc['2'];

                                                                                $iu->save();
                                                                                break;
                                                                            case '2':
                                                                                $iu = RrhhAsistencia::find($id);

                                                                                $iu->fthc_id_h2 = $row3['id'];

                                                                                $iu->horario_2_i = $this->fthc['2'];
                                                                                $iu->horario_2_s = $this->fthc['2'];

                                                                                $iu->save();
                                                                                break;
                                                                            default:
                                                                                break;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            break;
                                                        // === HORARIO CONTINUO ===
                                                        case '3':
                                                            if($row1['horario_id_2'] != '')
                                                            {
                                                                $sw_1 = TRUE;
                                                                if($row3['unidad_desconcentrada_id'] != '')
                                                                {
                                                                    if($row1['unidad_desconcentrada_id'] != $row3['unidad_desconcentrada_id'])
                                                                    {
                                                                        $sw_1 = FALSE;
                                                                    }
                                                                }

                                                                if($sw_1)
                                                                {
                                                                    $iu = RrhhAsistencia::find($id);

                                                                    $iu->horario_id_1 = $row3['horario_id'];

                                                                    $iu->fthc_id_h2 = $row3['id'];

                                                                    $iu->horario_2_i = $this->fthc['3'];
                                                                    $iu->horario_2_s = $this->fthc['3'];

                                                                    $iu->save();
                                                                }
                                                            }
                                                            break;
                                                        default:
                                                            break;
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    $fecha_acu = date("Y-m-d", strtotime($fecha_acu . "+ 1 days"));
                                }
                            }

                            $respuesta['respuesta'] .= "Se registraron " . $cantidad_registros . " asistencias.";
                            $respuesta['sw']         = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "No existen FUNCIONARIOS.";
                        }
                    }

                return json_encode($respuesta);
                break;

            // === SINCRONIZAR ASISTENCIAS ===
            case '2':
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
                        'titulo'     => '<div class="text-center"><strong>Asistencia</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );
                    $opcion = 'n';

                // === PERMISOS ===
                    if(!in_array(['codigo' => '1303'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para SINCRONIZAR ASISTENCIAS.";
                        return json_encode($respuesta);
                    }

                // === OPERACION ===
                    $data1['fecha_del'] = trim($request->input('fecha_del'));
                    $data1['fecha_al']  = trim($request->input('fecha_al'));

                    $data1['lugar_dependencia_id_funcionario'] = trim($request->input('lugar_dependencia_id_funcionario'));

                    $data1['persona_id'] = trim($request->input('persona_id'));

                // === ANALISIS ===
                    if( ! ($data1['fecha_del'] <= $data1['fecha_al']))
                    {
                        $respuesta['respuesta'] .= "La FECHA DEL es mayor que la FECHA AL.";
                        return json_encode($respuesta);
                    }

                    $tabla1  = "rrhh_asistencias";
                    $tabla2  = "inst_unidades_desconcentradas";

                    $select = "
                        $tabla1.id,
                        $tabla1.persona_id,

                        $tabla1.persona_id_rrhh_h1_i,
                        $tabla1.persona_id_rrhh_h1_s,
                        $tabla1.persona_id_rrhh_h2_i,
                        $tabla1.persona_id_rrhh_h2_s,

                        $tabla1.cargo_id,
                        $tabla1.unidad_desconcentrada_id,

                        $tabla1.log_marcaciones_id_i1,
                        $tabla1.log_marcaciones_id_s1,
                        $tabla1.log_marcaciones_id_i2,
                        $tabla1.log_marcaciones_id_s2,

                        $tabla1.horario_id_1,
                        $tabla1.horario_id_2,

                        $tabla1.salida_id_i1,
                        $tabla1.salida_id_s1,
                        $tabla1.salida_id_i2,
                        $tabla1.salida_id_s2,

                        $tabla1.fthc_id_h1,
                        $tabla1.fthc_id_h2,

                        $tabla1.estado,
                        $tabla1.fecha,

                        $tabla1.h1_i_omitir,
                        $tabla1.h1_s_omitir,
                        $tabla1.h2_i_omitir,
                        $tabla1.h2_s_omitir,

                        $tabla1.h1_min_retrasos,
                        $tabla1.h2_min_retrasos,

                        $tabla1.h1_descuento,
                        $tabla1.h2_descuento,

                        $tabla1.h1_i_omision_registro,
                        $tabla1.h1_s_omision_registro,
                        $tabla1.h2_i_omision_registro,
                        $tabla1.h2_s_omision_registro,

                        $tabla1.f_omision_registro,
                        $tabla1.e_omision_registro,

                        $tabla1.h1_falta,
                        $tabla1.h2_falta,

                        $tabla1.observaciones,
                        $tabla1.justificacion,

                        $tabla1.horario_1_i,
                        $tabla1.horario_1_s,

                        $tabla1.horario_2_i,
                        $tabla1.horario_2_s,

                        a2.lugar_dependencia_id AS lugar_dependencia_id_funcionario,
                        a2.nombre AS ud_funcionario
                    ";

                    $array_where = "$tabla1.estado = '1' AND $tabla1.fecha <= '" . $data1['fecha_al'] . "' AND $tabla1.fecha >= '"  . $data1['fecha_del'] . "'";

                    if($request->has('lugar_dependencia_id_funcionario'))
                    {
                        $array_where .= " AND a2.lugar_dependencia_id=" . $data1['lugar_dependencia_id_funcionario'];
                    }

                    if($request->has('persona_id'))
                    {
                        $array_where .= " AND $tabla1.persona_id=" . $data1['persona_id'];
                    }

                    $consulta1 = RrhhAsistencia::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.unidad_desconcentrada_id")
                        ->whereRaw($array_where)
                        ->select(DB::raw($select))
                        ->get()
                        ->toArray();

                    if(count($consulta1) > 0)
                    {
                        $sw_cerrado = TRUE;
                        set_time_limit(3600);
                        ini_set('memory_limit','-1');

                        foreach ($consulta1 as $row1)
                        {
                            if($row1['estado'] != '3')
                            {
                                $horario_1_sw = FALSE;
                                if($row1['fthc_id_h1'] == '')
                                {
                                    $horario_1_sw = TRUE;
                                }

                                if($horario_1_sw)
                                {
                                    $consulta2 = RrhhHorario::where("id", "=", $row1['horario_id_1'])
                                        ->first();

                                    // === HORARIO 1 ENTRADA ===
                                        if($row1['h1_i_omitir'] == '1')
                                        {
                                            $fh_ingreso = $row1['fecha'] . " " . $consulta2['h_ingreso'];

                                            $fh_ingreso_tolerancia = date("Y-m-d H:i:s", strtotime('+' . ($consulta2['tolerancia'] + 1) . ' minute', strtotime($fh_ingreso)));

                                            $ingreso_del = $row1['fecha'] . " " . $consulta2['marcacion_ingreso_del'];

                                            $ingreso_al = $row1['fecha'] . " " . $consulta2['marcacion_ingreso_al'];

                                            $marcacion_e_o_sw = TRUE;

                                            // $marcacion_h1_e = RrhhLogMarcacion::where("persona_id", "=", $row1['persona_id'])
                                            //     ->whereBetween('f_marcacion', [$ingreso_del, $ingreso_al])
                                            //     ->min('f_marcacion');

                                            $consulta3 = RrhhLogMarcacion::where("persona_id", "=", $row1['persona_id'])
                                                ->whereBetween('f_marcacion', [$ingreso_del, $ingreso_al])
                                                ->select('id', 'f_marcacion')
                                                ->orderBy('f_marcacion', 'asc')
                                                ->first();

                                            if(count($consulta3) > 0)
                                            {
                                                if($consulta3['f_marcacion'] < $fh_ingreso_tolerancia)
                                                {
                                                    // === SALIDAS POR HORAS ===
                                                        $salida_sw_1 = TRUE;
                                                        $salida_sw_3 = TRUE;

                                                        $tabla1 = "rrhh_salidas";
                                                        $tabla2 = "rrhh_tipos_salida";

                                                        $consulta4 = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                                                            ->where("$tabla1.persona_id", "=", $row1['persona_id'])
                                                            ->where("$tabla1.f_salida", "=", $row1['fecha'])
                                                            ->where("$tabla1.estado", "<>", '2')
                                                            ->where("$tabla1.validar_superior", "=", '2')
                                                            ->where("$tabla1.validar_rrhh", "=", '2')
                                                            ->where("a2.tipo_cronograma", "=", '1')
                                                            ->select("$tabla1.id", "$tabla1.h_salida", "$tabla1.h_retorno",  "$tabla1.con_sin_retorno", "a2.tipo_salida")
                                                            ->get()
                                                            ->toArray();

                                                        if(count($consulta4) > 0)
                                                        {
                                                            foreach ($consulta4 as $row4)
                                                            {
                                                                $fh_s = $row1['fecha'] . " " . $row4['h_salida'];

                                                                if($fh_s <= $fh_ingreso)
                                                                {
                                                                    // === MODIFICACION A ASISTENCIA ===
                                                                        $iu = RrhhAsistencia::find($row1['id']);

                                                                        $iu->salida_id_i1          = $row4['id'];
                                                                        $iu->log_marcaciones_id_i1 = NULL;
                                                                        $iu->h1_min_retrasos       = '0';
                                                                        $iu->h1_descuento          = '0';
                                                                        $iu->h1_i_omision_registro = '2';
                                                                        $iu->h1_falta              = '2';

                                                                        $iu->horario_1_i = $this->tipo_salida[$row4['tipo_salida']];
                                                                        $iu->horario_1_s = $this->omision['1'];

                                                                        $iu->save();

                                                                    // === MODIFICACION A LOG DE MARCACIONES ===
                                                                        $iu = RrhhSalida::find($row4['id']);

                                                                        $iu->estado = '3';

                                                                        $iu->save();

                                                                    $salida_sw_1 = FALSE;
                                                                    $salida_sw_3 = FALSE;

                                                                    $marcacion_e_o_sw = FALSE;
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                    // === SALIDAS POR DIAS ===
                                                        if($salida_sw_1)
                                                        {
                                                            $tabla1 = "rrhh_salidas";
                                                            $tabla2 = "rrhh_tipos_salida";

                                                            $consulta5 = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                                                                ->where("$tabla1.persona_id", "=", $row1['persona_id'])
                                                                ->where("$tabla1.f_salida", "<=", $row1['fecha'])
                                                                ->where("$tabla1.f_retorno", ">=", $row1['fecha'])
                                                                ->where("$tabla1.estado", "<>", '2')
                                                                ->where("$tabla1.validar_superior", "=", '2')
                                                                ->where("$tabla1.validar_rrhh", "=", '2')
                                                                ->where("a2.tipo_cronograma", "=", '2')
                                                                ->select("$tabla1.id", "$tabla1.f_salida", "$tabla1.f_retorno", "$tabla1.periodo_salida", "$tabla1.periodo_retorno", "a2.tipo_salida")
                                                                ->orderBy("$tabla1.f_salida", 'asc')
                                                                ->get()
                                                                ->toArray();

                                                            if(count($consulta5) > 0)
                                                            {
                                                                $salida_sw_2 = FALSE;
                                                                foreach($consulta5 as $row5)
                                                                {
                                                                    if(($row5['f_salida'] == $row1['fecha']) && ($row5['f_retorno'] == $row1['fecha']))
                                                                    {
                                                                        if($row5['periodo_retorno'] == '1')
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                        elseif(($row5['periodo_salida'] == '2') && ($row5['periodo_retorno'] == '1'))
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                        elseif(($row5['periodo_salida'] == '') && ($row5['periodo_retorno'] == ''))
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                    }
                                                                    elseif(($row5['f_retorno'] == $row1['fecha']))
                                                                    {
                                                                        if($row5['periodo_retorno'] == '1')
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                        elseif(($row5['periodo_salida'] == '') && ($row5['periodo_retorno'] == ''))
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                    }
                                                                    elseif(($row5['f_salida'] == $row1['fecha']))
                                                                    {
                                                                        if( ! ($row5['periodo_salida'] == '2'))
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        $salida_sw_2 = TRUE;
                                                                    }

                                                                    if($salida_sw_2)
                                                                    {
                                                                        // === MODIFICACION A ASISTENCIA ===
                                                                            $iu = RrhhAsistencia::find($row1['id']);

                                                                            $iu->salida_id_i1          = $row5['id'];
                                                                            $iu->salida_id_s1          = $row5['id'];
                                                                            $iu->log_marcaciones_id_i1 = NULL;
                                                                            $iu->log_marcaciones_id_s1 = NULL;
                                                                            $iu->h1_min_retrasos       = '0';

                                                                            $descuento = 0;
                                                                            if($row5['tipo_salida'] == '5')
                                                                            {
                                                                                $descuento = 0.5;
                                                                            }

                                                                            $iu->h1_descuento          = $descuento;

                                                                            $iu->h1_i_omision_registro = '2';
                                                                            $iu->h1_s_omision_registro = '2';
                                                                            $iu->h1_falta              = '2';

                                                                            $iu->horario_1_i = $this->tipo_salida[$row5['tipo_salida']];
                                                                            $iu->horario_1_s = $this->tipo_salida[$row5['tipo_salida']];

                                                                            $iu->save();

                                                                        // === MODIFICACION A LOG DE MARCACIONES ===
                                                                            $iu = RrhhSalida::find($row5['id']);

                                                                            $iu->estado = '3';

                                                                            $iu->save();

                                                                        $salida_sw_3 = FALSE;

                                                                        $marcacion_e_o_sw = FALSE;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        }

                                                    // === MODIFICACION A ASISTENCIA ===
                                                        if($salida_sw_3)
                                                        {
                                                            $iu = RrhhAsistencia::find($row1['id']);

                                                            $iu->log_marcaciones_id_i1 = $consulta3['id'];
                                                            $iu->salida_id_i1          = NULL;
                                                            $iu->h1_min_retrasos       = '0';
                                                            $iu->h1_descuento          = '0';
                                                            $iu->h1_i_omision_registro = '2';
                                                            $iu->h1_falta              = '2';

                                                            $iu->horario_1_i = date("H:i:s", strtotime($consulta3['f_marcacion']));
                                                            $iu->horario_1_s = $this->omision['1'];

                                                            $iu->save();

                                                            // === MODIFICACION A LOG DE MARCACIONES ===
                                                                $iu = RrhhLogMarcacion::find($consulta3['id']);

                                                                $iu->estado = '2';

                                                                $iu->save();

                                                            $marcacion_e_o_sw = FALSE;
                                                        }
                                                }
                                                else
                                                {
                                                    // === SALIDAS POR HORAS ===
                                                        $salida_sw_1 = TRUE;
                                                        $salida_sw_3 = TRUE;

                                                        $tabla1 = "rrhh_salidas";
                                                        $tabla2 = "rrhh_tipos_salida";

                                                        $consulta4 = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                                                            ->where("$tabla1.persona_id", "=", $row1['persona_id'])
                                                            ->where("$tabla1.f_salida", "=", $row1['fecha'])
                                                            ->where("$tabla1.estado", "<>", '2')
                                                            ->where("$tabla1.validar_superior", "=", '2')
                                                            ->where("$tabla1.validar_rrhh", "=", '2')
                                                            ->where("a2.tipo_cronograma", "=", '1')
                                                            ->select("$tabla1.id", "$tabla1.h_salida", "$tabla1.h_retorno",  "$tabla1.con_sin_retorno", "a2.tipo_salida")
                                                            ->get()
                                                            ->toArray();

                                                        if(count($consulta4) > 0)
                                                        {
                                                            foreach ($consulta4 as $row4)
                                                            {
                                                                $fh_s = $row1['fecha'] . " " . $row4['h_salida'];

                                                                if($fh_s <= $fh_ingreso)
                                                                {
                                                                    // === MODIFICACION A ASISTENCIA ===
                                                                        $iu = RrhhAsistencia::find($row1['id']);

                                                                        $iu->salida_id_i1          = $row4['id'];
                                                                        $iu->log_marcaciones_id_i1 = NULL;
                                                                        $iu->h1_min_retrasos       = '0';
                                                                        $iu->h1_descuento          = '0';
                                                                        $iu->h1_i_omision_registro = '2';
                                                                        $iu->h1_falta              = '2';

                                                                        $iu->horario_1_i = $this->tipo_salida[$row4['tipo_salida']];
                                                                        $iu->horario_1_s = $this->omision['1'];

                                                                        $iu->save();

                                                                    // === MODIFICACION A LOG DE MARCACIONES ===
                                                                        $iu = RrhhSalida::find($row4['id']);

                                                                        $iu->estado = '3';

                                                                        $iu->save();

                                                                    $salida_sw_1 = FALSE;
                                                                    $salida_sw_3 = FALSE;

                                                                    $marcacion_e_o_sw = FALSE;
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                    // === SALIDAS POR DIAS ===
                                                        if($salida_sw_1)
                                                        {
                                                            $tabla1 = "rrhh_salidas";
                                                            $tabla2 = "rrhh_tipos_salida";

                                                            $consulta5 = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                                                                ->where("$tabla1.persona_id", "=", $row1['persona_id'])
                                                                ->where("$tabla1.f_salida", "<=", $row1['fecha'])
                                                                ->where("$tabla1.f_retorno", ">=", $row1['fecha'])
                                                                ->where("$tabla1.estado", "<>", '2')
                                                                ->where("$tabla1.validar_superior", "=", '2')
                                                                ->where("$tabla1.validar_rrhh", "=", '2')
                                                                ->where("a2.tipo_cronograma", "=", '2')
                                                                ->select("$tabla1.id", "$tabla1.f_salida", "$tabla1.f_retorno", "$tabla1.periodo_salida", "$tabla1.periodo_retorno", "a2.tipo_salida")
                                                                ->orderBy("$tabla1.f_salida", 'asc')
                                                                ->get()
                                                                ->toArray();

                                                            if(count($consulta5) > 0)
                                                            {
                                                                $salida_sw_2 = FALSE;
                                                                foreach($consulta5 as $row5)
                                                                {
                                                                    if(($row5['f_salida'] == $row1['fecha']) && ($row5['f_retorno'] == $row1['fecha']))
                                                                    {
                                                                        if($row5['periodo_retorno'] == '1')
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                        elseif(($row5['periodo_salida'] == '2') && ($row5['periodo_retorno'] == '1'))
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                        elseif(($row5['periodo_salida'] == '') && ($row5['periodo_retorno'] == ''))
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                    }
                                                                    elseif(($row5['f_retorno'] == $row1['fecha']))
                                                                    {
                                                                        if($row5['periodo_retorno'] == '1')
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                        elseif(($row5['periodo_salida'] == '') && ($row5['periodo_retorno'] == ''))
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                    }
                                                                    elseif(($row5['f_salida'] == $row1['fecha']))
                                                                    {
                                                                        if( ! ($row5['periodo_salida'] == '2'))
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        $salida_sw_2 = TRUE;
                                                                    }

                                                                    if($salida_sw_2)
                                                                    {
                                                                        // === MODIFICACION A ASISTENCIA ===
                                                                            $iu = RrhhAsistencia::find($row1['id']);

                                                                            $iu->salida_id_i1          = $row5['id'];
                                                                            $iu->salida_id_s1          = $row5['id'];
                                                                            $iu->log_marcaciones_id_i1 = NULL;
                                                                            $iu->log_marcaciones_id_s1 = NULL;
                                                                            $iu->h1_min_retrasos       = '0';

                                                                            $descuento = 0;
                                                                            if($row5['tipo_salida'] == '5')
                                                                            {
                                                                                $descuento = 0.5;
                                                                            }

                                                                            $iu->h1_descuento          = $descuento;

                                                                            $iu->h1_i_omision_registro = '2';
                                                                            $iu->h1_s_omision_registro = '2';
                                                                            $iu->h1_falta              = '2';

                                                                            $iu->horario_1_i = $this->tipo_salida[$row5['tipo_salida']];
                                                                            $iu->horario_1_s = $this->tipo_salida[$row5['tipo_salida']];

                                                                            $iu->save();

                                                                        // === MODIFICACION A LOG DE MARCACIONES ===
                                                                            $iu = RrhhSalida::find($row5['id']);

                                                                            $iu->estado = '3';

                                                                            $iu->save();

                                                                        $salida_sw_3 = FALSE;

                                                                        $marcacion_e_o_sw = FALSE;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        }

                                                    // === RETRASO MINUTOS ===
                                                        if($salida_sw_3)
                                                        {
                                                            // === OJO REDONDEO HACIA ABAJO O USAR ESTE OTRO A CONSULTA CEIL ===
                                                            $min_retrasos = floor((strtotime($consulta3['f_marcacion']) - strtotime($fh_ingreso)) / 60);

                                                            // === MODIFICACION A ASISTENCIA ===
                                                                $iu = RrhhAsistencia::find($row1['id']);

                                                                $iu->log_marcaciones_id_i1 = $consulta3['id'];
                                                                $iu->salida_id_i1          = NULL;
                                                                $iu->h1_min_retrasos       = $min_retrasos;
                                                                $iu->h1_descuento          = '0';
                                                                $iu->h1_i_omision_registro = '2';
                                                                $iu->h1_falta              = '2';

                                                                $iu->horario_1_i = date("H:i:s", strtotime($consulta3['f_marcacion']));
                                                                $iu->horario_1_s = $this->omision['1'];

                                                                $iu->save();

                                                            // === MODIFICACION A LOG DE MARCACIONES ===
                                                                $iu = RrhhLogMarcacion::find($consulta3['id']);

                                                                $iu->estado = '2';

                                                                $iu->save();

                                                            $marcacion_e_o_sw = FALSE;
                                                        }
                                                }
                                            }
                                            else
                                            {
                                                // === SALIDAS POR HORAS ===
                                                    $salida_sw_1 = TRUE;

                                                    $tabla1 = "rrhh_salidas";
                                                    $tabla2 = "rrhh_tipos_salida";

                                                    $consulta4 = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                                                        ->where("$tabla1.persona_id", "=", $row1['persona_id'])
                                                        ->where("$tabla1.f_salida", "=", $row1['fecha'])
                                                        ->where("$tabla1.estado", "<>", '2')
                                                        ->where("$tabla1.validar_superior", "=", '2')
                                                        ->where("$tabla1.validar_rrhh", "=", '2')
                                                        ->where("a2.tipo_cronograma", "=", '1')
                                                        ->select("$tabla1.id", "$tabla1.h_salida", "$tabla1.h_retorno", "$tabla1.con_sin_retorno", "a2.tipo_salida")
                                                        ->get()
                                                        ->toArray();

                                                    if(count($consulta4) > 0)
                                                    {
                                                        foreach ($consulta4 as $row4)
                                                        {
                                                            $fh_s = $row1['fecha'] . " " . $row4['h_salida'];

                                                            if($fh_s <= $fh_ingreso)
                                                            {
                                                                // === MODIFICACION A ASISTENCIA ===
                                                                    $iu = RrhhAsistencia::find($row1['id']);

                                                                    $iu->salida_id_i1          = $row4['id'];
                                                                    $iu->log_marcaciones_id_i1 = NULL;
                                                                    $iu->h1_min_retrasos       = '0';
                                                                    $iu->h1_descuento          = '0';
                                                                    $iu->h1_i_omision_registro = '2';
                                                                    $iu->h1_falta              = '2';

                                                                    $iu->horario_1_i = $this->tipo_salida[$row4['tipo_salida']];
                                                                    $iu->horario_1_s = $this->omision['1'];

                                                                    $iu->save();

                                                                // === MODIFICACION A LOG DE MARCACIONES ===
                                                                    $iu = RrhhSalida::find($row4['id']);

                                                                    $iu->estado = '3';

                                                                    $iu->save();

                                                                $salida_sw_1 = FALSE;

                                                                $marcacion_e_o_sw = FALSE;
                                                                break;
                                                            }
                                                        }
                                                    }

                                                // === SALIDAS POR DIAS ===
                                                    if($salida_sw_1)
                                                    {
                                                        $tabla1 = "rrhh_salidas";
                                                        $tabla2 = "rrhh_tipos_salida";

                                                        $consulta5 = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                                                            ->where("$tabla1.persona_id", "=", $row1['persona_id'])
                                                            ->where("$tabla1.f_salida", "<=", $row1['fecha'])
                                                            ->where("$tabla1.f_retorno", ">=", $row1['fecha'])
                                                            ->where("$tabla1.estado", "<>", '2')
                                                            ->where("$tabla1.validar_superior", "=", '2')
                                                            ->where("$tabla1.validar_rrhh", "=", '2')
                                                            ->where("a2.tipo_cronograma", "=", '2')
                                                            ->select("$tabla1.id", "$tabla1.f_salida", "$tabla1.f_retorno", "$tabla1.periodo_salida", "$tabla1.periodo_retorno", "a2.tipo_salida")
                                                            ->orderBy("$tabla1.f_salida", 'asc')
                                                            ->get()
                                                            ->toArray();

                                                        if(count($consulta5) > 0)
                                                        {
                                                            $salida_sw_2 = FALSE;
                                                            foreach($consulta5 as $row5)
                                                            {
                                                                if(($row5['f_salida'] == $row1['fecha']) && ($row5['f_retorno'] == $row1['fecha']))
                                                                {
                                                                    if($row5['periodo_retorno'] == '1')
                                                                    {
                                                                        $salida_sw_2 = TRUE;
                                                                    }
                                                                    elseif(($row5['periodo_salida'] != '2') && ($row5['periodo_retorno'] != '1'))
                                                                    {
                                                                        $salida_sw_2 = TRUE;
                                                                    }
                                                                }
                                                                elseif(($row5['f_retorno'] == $row1['fecha']))
                                                                {
                                                                    if($row5['periodo_retorno'] == '1')
                                                                    {
                                                                        $salida_sw_2 = TRUE;
                                                                    }
                                                                    elseif(($row5['periodo_salida'] != '2') && ($row5['periodo_retorno'] != '1'))
                                                                    {
                                                                        $salida_sw_2 = TRUE;
                                                                    }
                                                                }
                                                                elseif(($row5['f_salida'] == $row1['fecha']))
                                                                {
                                                                    if( ! ($row5['periodo_salida'] == '2'))
                                                                    {
                                                                        $salida_sw_2 = TRUE;
                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    $salida_sw_2 = TRUE;
                                                                }

                                                                if($salida_sw_2)
                                                                {
                                                                    // === MODIFICACION A ASISTENCIA ===
                                                                        $iu = RrhhAsistencia::find($row1['id']);

                                                                        $iu->salida_id_i1          = $row5['id'];
                                                                        $iu->salida_id_s1          = $row5['id'];
                                                                        $iu->log_marcaciones_id_i1 = NULL;
                                                                        $iu->log_marcaciones_id_s1 = NULL;
                                                                        $iu->h1_min_retrasos       = '0';

                                                                        $descuento = 0;
                                                                        if($row5['tipo_salida'] == '5')
                                                                        {
                                                                            $descuento = 0.5;
                                                                        }

                                                                        $iu->h1_descuento          = $descuento;

                                                                        $iu->h1_i_omision_registro = '2';
                                                                        $iu->h1_s_omision_registro = '2';
                                                                        $iu->h1_falta              = '2';

                                                                        $iu->horario_1_i = $this->tipo_salida[$row5['tipo_salida']];
                                                                        $iu->horario_1_s = $this->tipo_salida[$row5['tipo_salida']];

                                                                        $iu->save();

                                                                    // === MODIFICACION A LOG DE MARCACIONES ===
                                                                        $iu = RrhhSalida::find($row5['id']);

                                                                        $iu->estado = '3';

                                                                        $iu->save();

                                                                    $marcacion_e_o_sw = FALSE;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    }
                                            }
                                        }

                                    // === HORARIO 1 SALIDA ===
                                        if($row1['h1_s_omitir'] == '1')
                                        {
                                            $fh_salida = $row1['fecha'] . " " . $consulta2['h_salida'];

                                            $salida_del = $row1['fecha'] . " " . $consulta2['marcacion_salida_del'];

                                            $salida_al = $row1['fecha'] . " " . $consulta2['marcacion_salida_al'];
                                            $salida_al = date("Y-m-d H:i:s", strtotime('+59 second', strtotime($salida_al)));

                                            $consulta3 = RrhhLogMarcacion::where("persona_id", "=", $row1['persona_id'])
                                                ->whereBetween('f_marcacion', [$salida_del, $salida_al])
                                                ->select('id', 'f_marcacion')
                                                ->orderBy('f_marcacion', 'asc')
                                                ->first();

                                            if(count($consulta3) > 0)
                                            {
                                                // === MODIFICACION A ASISTENCIA ===
                                                    $iu = RrhhAsistencia::find($row1['id']);

                                                    $iu->log_marcaciones_id_s1 = $consulta3['id'];
                                                    $iu->salida_id_s1          = NULL;
                                                    $iu->h1_descuento          = '0';
                                                    $iu->h1_s_omision_registro = '2';

                                                    if($marcacion_e_o_sw)
                                                    {
                                                        $iu->horario_1_i = $this->omision['1'];
                                                        $iu->h1_falta    = '2';
                                                    }

                                                    $iu->horario_1_s = date("H:i:s", strtotime($consulta3['f_marcacion']));

                                                    $iu->save();

                                                // === MODIFICACION A LOG DE MARCACIONES ===
                                                    $iu = RrhhLogMarcacion::find($consulta3['id']);

                                                    $iu->estado = '2';

                                                    $iu->save();
                                            }
                                            else
                                            {
                                                // === SALIDAS POR HORAS ===
                                                    $tabla1 = "rrhh_salidas";
                                                    $tabla2 = "rrhh_tipos_salida";

                                                    $consulta4 = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                                                        ->where("$tabla1.persona_id", "=", $row1['persona_id'])
                                                        ->where("$tabla1.f_salida", "=", $row1['fecha'])
                                                        ->where("$tabla1.estado", "<>", '2')
                                                        ->where("$tabla1.validar_superior", "=", '2')
                                                        ->where("$tabla1.validar_rrhh", "=", '2')
                                                        ->where("a2.tipo_cronograma", "=", '1')
                                                        ->select("$tabla1.id", "$tabla1.h_salida", "$tabla1.h_retorno", "$tabla1.con_sin_retorno", "a2.tipo_salida")
                                                        ->get()
                                                        ->toArray();

                                                    if(count($consulta4) > 0)
                                                    {
                                                        foreach ($consulta4 as $row4)
                                                        {
                                                            if($row4['h_retorno'] != '')
                                                            {
                                                                $fh_r = $row1['fecha'] . " " . $row4['h_retorno'];
                                                                if($fh_r >= $fh_salida)
                                                                {
                                                                    // === MODIFICACION A ASISTENCIA ===
                                                                        $iu = RrhhAsistencia::find($row1['id']);

                                                                        $iu->salida_id_s1          = $row4['id'];
                                                                        $iu->log_marcaciones_id_s1 = NULL;
                                                                        // $iu->h1_descuento          = '0';
                                                                        $iu->h1_s_omision_registro = '2';

                                                                        if($marcacion_e_o_sw)
                                                                        {
                                                                            $iu->horario_1_i = $this->omision['1'];
                                                                            $iu->h1_falta    = '2';
                                                                        }

                                                                        $iu->horario_1_s = $this->tipo_salida[$row4['tipo_salida']];

                                                                        $iu->save();

                                                                    // === MODIFICACION A LOG DE MARCACIONES ===
                                                                        $iu = RrhhSalida::find($row4['id']);

                                                                        $iu->estado = '3';

                                                                        $iu->save();
                                                                    break;
                                                                }
                                                            }
                                                            elseif($row4['con_sin_retorno'] == '2')
                                                            {
                                                                $fh_s = $row1['fecha'] . " " . $row4['h_salida'];
                                                                if(($fh_ingreso <= $fh_s) && ($fh_s <= $fh_salida))
                                                                {
                                                                    // === MODIFICACION A ASISTENCIA ===
                                                                        $iu = RrhhAsistencia::find($row1['id']);

                                                                        $iu->salida_id_s1          = $row4['id'];
                                                                        $iu->log_marcaciones_id_s1 = NULL;
                                                                        // $iu->h1_descuento          = '0';
                                                                        $iu->h1_s_omision_registro = '2';

                                                                        if($marcacion_e_o_sw)
                                                                        {
                                                                            $iu->horario_1_i = $this->omision['1'];
                                                                            $iu->h1_falta    = '2';
                                                                        }

                                                                        $iu->horario_1_s = $this->tipo_salida[$row4['tipo_salida']];

                                                                        $iu->save();

                                                                    // === MODIFICACION A LOG DE MARCACIONES ===
                                                                        $iu = RrhhSalida::find($row4['id']);

                                                                        $iu->estado = '3';

                                                                        $iu->save();
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    }
                                            }
                                        }
                                }

                                $horario_2_sw = FALSE;

                                if($row1['fthc_id_h2'] == '')
                                {
                                    $horario_2_sw = TRUE;
                                }

                                if($horario_2_sw)
                                {
                                    if($row1['horario_id_2'] != '')
                                    {
                                        $consulta2 = RrhhHorario::where("id", "=", $row1['horario_id_2'])
                                            ->first();

                                        // === HORARIO 2 ENTRADA ===
                                            if($row1['h2_i_omitir'] == '1')
                                            {
                                                $fh_ingreso = $row1['fecha'] . " " . $consulta2['h_ingreso'];

                                                $fh_ingreso_limite = $row1['fecha'] . " " . $consulta2['marcacion_ingreso_del'];

                                                $fh_ingreso_tolerancia = date("Y-m-d H:i:s", strtotime('+' . ($consulta2['tolerancia'] + 1) . ' minute', strtotime($fh_ingreso)));

                                                $ingreso_del = $row1['fecha'] . " " . $consulta2['marcacion_ingreso_del'];

                                                $ingreso_al = $row1['fecha'] . " " . $consulta2['marcacion_ingreso_al'];

                                                $marcacion_e_o_sw = TRUE;

                                                $consulta3 = RrhhLogMarcacion::where("persona_id", "=", $row1['persona_id'])
                                                    ->whereBetween('f_marcacion', [$ingreso_del, $ingreso_al])
                                                    ->select('id', 'f_marcacion')
                                                    ->orderBy('f_marcacion', 'asc')
                                                    ->first();

                                                if(count($consulta3) > 0)
                                                {
                                                    if($consulta3['f_marcacion'] < $fh_ingreso_tolerancia)
                                                    {
                                                        // === SALIDAS POR HORAS ===
                                                            $salida_sw_1 = TRUE;
                                                            $salida_sw_3 = TRUE;

                                                            $tabla1 = "rrhh_salidas";
                                                            $tabla2 = "rrhh_tipos_salida";

                                                            $consulta4 = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                                                                ->where("$tabla1.persona_id", "=", $row1['persona_id'])
                                                                ->where("$tabla1.f_salida", "=", $row1['fecha'])
                                                                ->where("$tabla1.estado", "<>", '2')
                                                                ->where("$tabla1.validar_superior", "=", '2')
                                                                ->where("$tabla1.validar_rrhh", "=", '2')
                                                                ->where("a2.tipo_cronograma", "=", '1')
                                                                ->select("$tabla1.id", "$tabla1.h_salida", "$tabla1.h_retorno", "$tabla1.con_sin_retorno", "a2.tipo_salida")
                                                                ->get()
                                                                ->toArray();

                                                            if(count($consulta4) > 0)
                                                            {
                                                                foreach ($consulta4 as $row4)
                                                                {
                                                                    $fh_s = $row1['fecha'] . " " . $row4['h_salida'];

                                                                    if(($fh_s <= $fh_ingreso) && ($fh_ingreso_limite <= $fh_s))
                                                                    {
                                                                        // === MODIFICACION A ASISTENCIA ===
                                                                            $iu = RrhhAsistencia::find($row1['id']);

                                                                            $iu->salida_id_i2          = $row4['id'];
                                                                            $iu->log_marcaciones_id_i2 = NULL;
                                                                            $iu->h2_min_retrasos       = '0';
                                                                            $iu->h2_descuento          = '0';
                                                                            $iu->h2_i_omision_registro = '2';
                                                                            $iu->h2_falta              = '2';

                                                                            $iu->horario_2_i = $this->tipo_salida[$row4['tipo_salida']];
                                                                            $iu->horario_2_s = $this->omision['1'];

                                                                            $iu->save();

                                                                        // === MODIFICACION A LOG DE MARCACIONES ===
                                                                            $iu = RrhhSalida::find($row4['id']);

                                                                            $iu->estado = '3';

                                                                            $iu->save();

                                                                        $salida_sw_1 = FALSE;
                                                                        $salida_sw_3 = FALSE;

                                                                        $marcacion_e_o_sw = FALSE;
                                                                        break;
                                                                    }
                                                                }
                                                            }

                                                        // === SALIDAS POR DIAS ===
                                                            if($salida_sw_1)
                                                            {
                                                                $tabla1 = "rrhh_salidas";
                                                                $tabla2 = "rrhh_tipos_salida";

                                                                $consulta5 = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                                                                    ->where("$tabla1.persona_id", "=", $row1['persona_id'])
                                                                    ->where("$tabla1.f_salida", "<=", $row1['fecha'])
                                                                    ->where("$tabla1.f_retorno", ">=", $row1['fecha'])
                                                                    ->where("$tabla1.estado", "<>", '2')
                                                                    ->where("$tabla1.validar_superior", "=", '2')
                                                                    ->where("$tabla1.validar_rrhh", "=", '2')
                                                                    ->where("a2.tipo_cronograma", "=", '2')
                                                                    ->select("$tabla1.id", "$tabla1.f_salida", "$tabla1.f_retorno", "$tabla1.periodo_salida", "$tabla1.periodo_retorno", "a2.tipo_salida")
                                                                    ->orderBy("$tabla1.f_salida", 'asc')
                                                                    ->get()
                                                                    ->toArray();

                                                                if(count($consulta5) > 0)
                                                                {
                                                                    $salida_sw_2 = FALSE;
                                                                    foreach($consulta5 as $row5)
                                                                    {
                                                                        if(($row5['f_salida'] == $row1['fecha']) && ($row5['f_retorno'] == $row1['fecha']))
                                                                        {
                                                                            if($row5['periodo_salida'] == '2')
                                                                            {
                                                                                $salida_sw_2 = TRUE;
                                                                            }
                                                                            elseif(($row5['periodo_salida'] == '2') && ($row5['periodo_retorno'] == '1'))
                                                                            {
                                                                                $salida_sw_2 = TRUE;
                                                                            }
                                                                            elseif(($row5['periodo_salida'] == '') && ($row5['periodo_retorno'] == ''))
                                                                            {
                                                                                $salida_sw_2 = TRUE;
                                                                            }
                                                                        }
                                                                        elseif(($row5['f_salida'] == $row1['fecha']))
                                                                        {
                                                                            if($row5['periodo_retorno'] == '2')
                                                                            {
                                                                                $salida_sw_2 = TRUE;
                                                                            }
                                                                            elseif(($row5['periodo_salida'] == '') && ($row5['periodo_retorno'] == ''))
                                                                            {
                                                                                $salida_sw_2 = TRUE;
                                                                            }
                                                                        }
                                                                        elseif(($row5['f_retorno'] == $row1['fecha']))
                                                                        {
                                                                            if( ! ($row5['periodo_retorno'] == '1'))
                                                                            {
                                                                                $salida_sw_2 = TRUE;
                                                                            }
                                                                        }
                                                                        else
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }

                                                                        if($salida_sw_2)
                                                                        {
                                                                            // === MODIFICACION A ASISTENCIA ===
                                                                                $iu = RrhhAsistencia::find($row1['id']);

                                                                                $iu->salida_id_i2          = $row5['id'];
                                                                                $iu->salida_id_s2          = $row5['id'];
                                                                                $iu->log_marcaciones_id_i2 = NULL;
                                                                                $iu->log_marcaciones_id_s2 = NULL;
                                                                                $iu->h2_min_retrasos       = '0';

                                                                                $descuento = 0;
                                                                                if($row5['tipo_salida'] == '5')
                                                                                {
                                                                                    $descuento = 0.5;
                                                                                }

                                                                                $iu->h1_descuento = $descuento;

                                                                                $iu->h2_i_omision_registro = '2';
                                                                                $iu->h2_s_omision_registro = '2';
                                                                                $iu->h2_falta              = '2';

                                                                                $iu->horario_2_i = $this->tipo_salida[$row5['tipo_salida']];
                                                                                $iu->horario_2_s = $this->tipo_salida[$row5['tipo_salida']];

                                                                                $iu->save();

                                                                            // === MODIFICACION A LOG DE MARCACIONES ===
                                                                                $iu = RrhhSalida::find($row5['id']);

                                                                                $iu->estado = '3';

                                                                                $iu->save();

                                                                            $salida_sw_3 = FALSE;

                                                                            $marcacion_e_o_sw = FALSE;
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            }

                                                        // === MODIFICACION A ASISTENCIA ===
                                                            if($salida_sw_3)
                                                            {
                                                                $iu = RrhhAsistencia::find($row1['id']);

                                                                $iu->log_marcaciones_id_i2 = $consulta3['id'];
                                                                $iu->salida_id_i2          = NULL;
                                                                $iu->h2_min_retrasos       = '0';
                                                                $iu->h2_descuento          = '0';
                                                                $iu->h2_i_omision_registro = '2';
                                                                $iu->h2_falta              = '2';

                                                                $iu->horario_2_i = date("H:i:s", strtotime($consulta3['f_marcacion']));
                                                                $iu->horario_2_s = $this->omision['1'];

                                                                $iu->save();

                                                                // === MODIFICACION A LOG DE MARCACIONES ===
                                                                    $iu = RrhhLogMarcacion::find($consulta3['id']);

                                                                    $iu->estado = '2';

                                                                    $iu->save();

                                                                $marcacion_e_o_sw = FALSE;
                                                            }
                                                    }
                                                    else
                                                    {
                                                        // === SALIDAS POR HORAS ===
                                                            $salida_sw_1 = TRUE;
                                                            $salida_sw_3 = TRUE;

                                                            $tabla1 = "rrhh_salidas";
                                                            $tabla2 = "rrhh_tipos_salida";

                                                            $consulta4 = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                                                                ->where("$tabla1.persona_id", "=", $row1['persona_id'])
                                                                ->where("$tabla1.f_salida", "=", $row1['fecha'])
                                                                ->where("$tabla1.estado", "<>", '2')
                                                                ->where("$tabla1.validar_superior", "=", '2')
                                                                ->where("$tabla1.validar_rrhh", "=", '2')
                                                                ->where("a2.tipo_cronograma", "=", '1')
                                                                ->select("$tabla1.id", "$tabla1.h_salida", "$tabla1.h_retorno", "$tabla1.con_sin_retorno", "a2.tipo_salida")
                                                                ->get()
                                                                ->toArray();

                                                            if(count($consulta4) > 0)
                                                            {
                                                                foreach ($consulta4 as $row4)
                                                                {
                                                                    $fh_s = $row1['fecha'] . " " . $row4['h_salida'];

                                                                    if(($fh_s <= $fh_ingreso) && ($fh_ingreso_limite <= $fh_s))
                                                                    {
                                                                        // === MODIFICACION A ASISTENCIA ===
                                                                            $iu = RrhhAsistencia::find($row1['id']);

                                                                            $iu->salida_id_i2          = $row4['id'];
                                                                            $iu->log_marcaciones_id_i2 = NULL;
                                                                            $iu->h2_min_retrasos       = '0';
                                                                            $iu->h2_descuento          = '0';
                                                                            $iu->h2_i_omision_registro = '2';
                                                                            $iu->h2_falta              = '2';

                                                                            $iu->horario_2_i = $this->tipo_salida[$row4['tipo_salida']];
                                                                            $iu->horario_2_s = $this->omision['1'];

                                                                            $iu->save();

                                                                        // === MODIFICACION A LOG DE MARCACIONES ===
                                                                            $iu = RrhhSalida::find($row4['id']);

                                                                            $iu->estado = '3';

                                                                            $iu->save();

                                                                        $salida_sw_1 = FALSE;
                                                                        $salida_sw_3 = FALSE;

                                                                        $marcacion_e_o_sw = FALSE;
                                                                        break;
                                                                    }
                                                                }
                                                            }

                                                        // === SALIDAS POR DIAS ===
                                                            if($salida_sw_1)
                                                            {
                                                                $tabla1 = "rrhh_salidas";
                                                                $tabla2 = "rrhh_tipos_salida";

                                                                $consulta5 = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                                                                    ->where("$tabla1.persona_id", "=", $row1['persona_id'])
                                                                    ->where("$tabla1.f_salida", "<=", $row1['fecha'])
                                                                    ->where("$tabla1.f_retorno", ">=", $row1['fecha'])
                                                                    ->where("$tabla1.estado", "<>", '2')
                                                                    ->where("$tabla1.validar_superior", "=", '2')
                                                                    ->where("$tabla1.validar_rrhh", "=", '2')
                                                                    ->where("a2.tipo_cronograma", "=", '2')
                                                                    ->select("$tabla1.id", "$tabla1.f_salida", "$tabla1.f_retorno", "$tabla1.periodo_salida", "$tabla1.periodo_retorno", "a2.tipo_salida")
                                                                    ->orderBy("$tabla1.f_salida", 'asc')
                                                                    ->get()
                                                                    ->toArray();

                                                                if(count($consulta5) > 0)
                                                                {
                                                                    $salida_sw_2 = FALSE;
                                                                    foreach($consulta5 as $row5)
                                                                    {
                                                                        if(($row5['f_salida'] == $row1['fecha']) && ($row5['f_retorno'] == $row1['fecha']))
                                                                        {
                                                                            if($row5['periodo_salida'] == '2')
                                                                            {
                                                                                $salida_sw_2 = TRUE;
                                                                            }
                                                                            elseif(($row5['periodo_salida'] == '2') && ($row5['periodo_retorno'] == '1'))
                                                                            {
                                                                                $salida_sw_2 = TRUE;
                                                                            }
                                                                            elseif(($row5['periodo_salida'] == '') && ($row5['periodo_retorno'] == ''))
                                                                            {
                                                                                $salida_sw_2 = TRUE;
                                                                            }
                                                                        }
                                                                        elseif(($row5['f_salida'] == $row1['fecha']))
                                                                        {
                                                                            if($row5['periodo_retorno'] == '2')
                                                                            {
                                                                                $salida_sw_2 = TRUE;
                                                                            }
                                                                            elseif(($row5['periodo_salida'] != '') && ($row5['periodo_retorno'] != ''))
                                                                            {
                                                                                $salida_sw_2 = TRUE;
                                                                            }
                                                                        }
                                                                        elseif(($row5['f_retorno'] == $row1['fecha']))
                                                                        {
                                                                            if( ! ($row5['periodo_retorno'] == '1'))
                                                                            {
                                                                                $salida_sw_2 = TRUE;
                                                                            }
                                                                        }
                                                                        else
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }

                                                                        if($salida_sw_2)
                                                                        {
                                                                            // === MODIFICACION A ASISTENCIA ===
                                                                                $iu = RrhhAsistencia::find($row1['id']);

                                                                                $iu->salida_id_i2          = $row5['id'];
                                                                                $iu->salida_id_s2          = $row5['id'];
                                                                                $iu->log_marcaciones_id_i2 = NULL;
                                                                                $iu->log_marcaciones_id_s2 = NULL;
                                                                                $iu->h2_min_retrasos       = '0';

                                                                                $descuento = 0;
                                                                                if($row5['tipo_salida'] == '5')
                                                                                {
                                                                                    $descuento = 0.5;
                                                                                }

                                                                                $iu->h1_descuento = $descuento;

                                                                                $iu->h2_i_omision_registro = '2';
                                                                                $iu->h2_s_omision_registro = '2';
                                                                                $iu->h2_falta              = '2';

                                                                                $iu->horario_2_i = $this->tipo_salida[$row5['tipo_salida']];
                                                                                $iu->horario_2_s = $this->tipo_salida[$row5['tipo_salida']];

                                                                                $iu->save();

                                                                            // === MODIFICACION A LOG DE MARCACIONES ===
                                                                                $iu = RrhhSalida::find($row5['id']);

                                                                                $iu->estado = '3';

                                                                                $iu->save();

                                                                            $salida_sw_3 = FALSE;

                                                                            $marcacion_e_o_sw = FALSE;
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            }

                                                        // === RETRASO MINUTOS ===
                                                            if($salida_sw_3)
                                                            {
                                                                // === OJO REDONDEO HACIA ABAJO O USAR ESTE OTRO A CONSULTA CEIL ===
                                                                $min_retrasos = floor((strtotime($consulta3['f_marcacion']) - strtotime($fh_ingreso)) / 60);

                                                                // === MODIFICACION A ASISTENCIA ===
                                                                    $iu = RrhhAsistencia::find($row1['id']);

                                                                    $iu->log_marcaciones_id_i2 = $consulta3['id'];
                                                                    $iu->salida_id_i2          = NULL;
                                                                    $iu->h2_min_retrasos       = $min_retrasos;
                                                                    $iu->h2_descuento          = '0';
                                                                    $iu->h2_i_omision_registro = '2';
                                                                    $iu->h2_falta              = '2';

                                                                    $iu->horario_2_i = date("H:i:s", strtotime($consulta3['f_marcacion']));
                                                                    $iu->horario_2_s = $this->omision['1'];

                                                                    $iu->save();

                                                                // === MODIFICACION A LOG DE MARCACIONES ===
                                                                    $iu = RrhhLogMarcacion::find($consulta3['id']);

                                                                    $iu->estado = '2';

                                                                    $iu->save();

                                                                $marcacion_e_o_sw = FALSE;
                                                            }
                                                    }
                                                }
                                                else
                                                {
                                                    // === SALIDAS POR HORAS ===
                                                        $salida_sw_1 = TRUE;

                                                        $tabla1 = "rrhh_salidas";
                                                        $tabla2 = "rrhh_tipos_salida";

                                                        $consulta4 = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                                                            ->where("$tabla1.persona_id", "=", $row1['persona_id'])
                                                            ->where("$tabla1.f_salida", "=", $row1['fecha'])
                                                            ->where("$tabla1.estado", "<>", '2')
                                                            ->where("$tabla1.validar_superior", "=", '2')
                                                            ->where("$tabla1.validar_rrhh", "=", '2')
                                                            ->where("a2.tipo_cronograma", "=", '1')
                                                            ->select("$tabla1.id", "$tabla1.h_salida", "$tabla1.h_retorno", "$tabla1.con_sin_retorno", "a2.tipo_salida")
                                                            ->get()
                                                            ->toArray();

                                                        if(count($consulta4) > 0)
                                                        {
                                                            foreach ($consulta4 as $row4)
                                                            {
                                                                $fh_s = $row1['fecha'] . " " . $row4['h_salida'];

                                                                if(($fh_s <= $fh_ingreso) && ($fh_ingreso_limite <= $fh_s))
                                                                {
                                                                    // === MODIFICACION A ASISTENCIA ===
                                                                        $iu = RrhhAsistencia::find($row1['id']);

                                                                        $iu->salida_id_i2          = $row4['id'];
                                                                        $iu->log_marcaciones_id_i2 = NULL;
                                                                        $iu->h2_min_retrasos       = '0';
                                                                        $iu->h2_descuento          = '0';
                                                                        $iu->h2_i_omision_registro = '2';
                                                                        $iu->h2_falta              = '2';

                                                                        $iu->horario_2_i = $this->tipo_salida[$row4['tipo_salida']];
                                                                        $iu->horario_2_s = $this->omision['1'];

                                                                        $iu->save();

                                                                    // === MODIFICACION A LOG DE MARCACIONES ===
                                                                        $iu = RrhhSalida::find($row4['id']);

                                                                        $iu->estado = '3';

                                                                        $iu->save();

                                                                    $salida_sw_1 = FALSE;

                                                                    $marcacion_e_o_sw = FALSE;
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                    // === SALIDAS POR DIAS ===
                                                        if($salida_sw_1)
                                                        {
                                                            $tabla1 = "rrhh_salidas";
                                                            $tabla2 = "rrhh_tipos_salida";

                                                            $consulta5 = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                                                                ->where("$tabla1.persona_id", "=", $row1['persona_id'])
                                                                ->where("$tabla1.f_salida", "<=", $row1['fecha'])
                                                                ->where("$tabla1.f_retorno", ">=", $row1['fecha'])
                                                                ->where("$tabla1.estado", "<>", '2')
                                                                ->where("$tabla1.validar_superior", "=", '2')
                                                                ->where("$tabla1.validar_rrhh", "=", '2')
                                                                ->where("a2.tipo_cronograma", "=", '2')
                                                                ->select("$tabla1.id", "$tabla1.f_salida", "$tabla1.f_retorno", "$tabla1.periodo_salida", "$tabla1.periodo_retorno", "a2.tipo_salida")
                                                                ->orderBy("$tabla1.f_salida", 'asc')
                                                                ->get()
                                                                ->toArray();

                                                            if(count($consulta5) > 0)
                                                            {
                                                                $salida_sw_2 = FALSE;
                                                                foreach($consulta5 as $row5)
                                                                {
                                                                    if(($row5['f_salida'] == $row1['fecha']) && ($row5['f_retorno'] == $row1['fecha']))
                                                                    {
                                                                        if($row5['periodo_salida'] == '2')
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                        elseif(($row5['periodo_salida'] == '2') && ($row5['periodo_retorno'] == '1'))
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                        elseif(($row5['periodo_salida'] == '') && ($row5['periodo_retorno'] == ''))
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                    }
                                                                    elseif(($row5['f_salida'] == $row1['fecha']))
                                                                    {
                                                                        if($row5['periodo_salida'] == '2')
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                        elseif(($row5['periodo_salida'] == '') && ($row5['periodo_retorno'] == ''))
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                    }
                                                                    elseif(($row5['f_retorno'] == $row1['fecha']))
                                                                    {
                                                                        if( ! ($row5['periodo_retorno'] == '1'))
                                                                        {
                                                                            $salida_sw_2 = TRUE;
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        $salida_sw_2 = TRUE;
                                                                    }

                                                                    if($salida_sw_2)
                                                                    {
                                                                        // === MODIFICACION A ASISTENCIA ===
                                                                            $iu = RrhhAsistencia::find($row1['id']);

                                                                            $iu->salida_id_i2          = $row5['id'];
                                                                            $iu->salida_id_s2          = $row5['id'];
                                                                            $iu->log_marcaciones_id_i2 = NULL;
                                                                            $iu->log_marcaciones_id_s2 = NULL;
                                                                            $iu->h2_min_retrasos       = '0';

                                                                            $descuento = 0;
                                                                            if($row5['tipo_salida'] == '5')
                                                                            {
                                                                                $descuento = 0.5;
                                                                            }

                                                                            $iu->h1_descuento = $descuento;

                                                                            $iu->h2_i_omision_registro = '2';
                                                                            $iu->h2_s_omision_registro = '2';
                                                                            $iu->h2_falta              = '2';

                                                                            $iu->horario_2_i = $this->tipo_salida[$row5['tipo_salida']];
                                                                            $iu->horario_2_s = $this->tipo_salida[$row5['tipo_salida']];

                                                                            $iu->save();

                                                                        // === MODIFICACION A LOG DE MARCACIONES ===
                                                                            $iu = RrhhSalida::find($row5['id']);

                                                                            $iu->estado = '3';

                                                                            $iu->save();

                                                                        $marcacion_e_o_sw = FALSE;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                }
                                            }

                                        // === HORARIO 2 SALIDA ===
                                            if($row1['h2_s_omitir'] == '1')
                                            {
                                                $fh_salida = $row1['fecha'] . " " . $consulta2['h_salida'];

                                                $salida_del = $row1['fecha'] . " " . $consulta2['marcacion_salida_del'];

                                                $salida_al = $row1['fecha'] . " " . $consulta2['marcacion_salida_al'];
                                                $salida_al = date("Y-m-d H:i:s", strtotime('+59 second', strtotime($salida_al)));

                                                $consulta3 = RrhhLogMarcacion::where("persona_id", "=", $row1['persona_id'])
                                                    ->whereBetween('f_marcacion', [$salida_del, $salida_al])
                                                    ->select('id', 'f_marcacion')
                                                    ->orderBy('f_marcacion', 'asc')
                                                    ->first();

                                                if(count($consulta3) > 0)
                                                {
                                                    // === MODIFICACION A ASISTENCIA ===
                                                        $iu = RrhhAsistencia::find($row1['id']);

                                                        $iu->log_marcaciones_id_s2 = $consulta3['id'];
                                                        $iu->salida_id_s2          = NULL;
                                                        $iu->h2_descuento          = '0';
                                                        $iu->h2_s_omision_registro = '2';

                                                        if($marcacion_e_o_sw)
                                                        {
                                                            $iu->horario_2_i = $this->omision['1'];
                                                            $iu->h2_falta    = '2';
                                                        }

                                                        $iu->horario_2_s = date("H:i:s", strtotime($consulta3['f_marcacion']));

                                                        $iu->save();

                                                    // === MODIFICACION A LOG DE MARCACIONES ===
                                                        $iu = RrhhLogMarcacion::find($consulta3['id']);

                                                        $iu->estado = '2';

                                                        $iu->save();
                                                }
                                                else
                                                {
                                                    // === SALIDAS POR HORAS ===
                                                        $tabla1 = "rrhh_salidas";
                                                        $tabla2 = "rrhh_tipos_salida";

                                                        $consulta4 = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                                                            ->where("$tabla1.persona_id", "=", $row1['persona_id'])
                                                            ->where("$tabla1.f_salida", "=", $row1['fecha'])
                                                            ->where("$tabla1.estado", "<>", '2')
                                                            ->where("$tabla1.validar_superior", "=", '2')
                                                            ->where("$tabla1.validar_rrhh", "=", '2')
                                                            ->where("a2.tipo_cronograma", "=", '1')
                                                            ->select("$tabla1.id", "$tabla1.h_salida", "$tabla1.h_retorno", "$tabla1.con_sin_retorno", "a2.tipo_salida")
                                                            ->get()
                                                            ->toArray();

                                                        if(count($consulta4) > 0)
                                                        {
                                                            foreach ($consulta4 as $row4)
                                                            {
                                                                if($row4['h_retorno'] != '')
                                                                {
                                                                    $fh_r = $row1['fecha'] . " " . $row4['h_retorno'];
                                                                    if($fh_r >= $fh_salida)
                                                                    {
                                                                        // === MODIFICACION A ASISTENCIA ===
                                                                            $iu = RrhhAsistencia::find($row1['id']);

                                                                            $iu->salida_id_s2          = $row4['id'];
                                                                            $iu->log_marcaciones_id_s2 = NULL;
                                                                            // $iu->h2_descuento          = '0';
                                                                            $iu->h2_s_omision_registro = '2';

                                                                            if($marcacion_e_o_sw)
                                                                            {
                                                                                $iu->horario_2_i = $this->omision['1'];
                                                                                $iu->h2_falta    = '2';
                                                                            }

                                                                            $iu->horario_2_s = $this->tipo_salida[$row4['tipo_salida']];

                                                                            $iu->save();

                                                                        // === MODIFICACION A LOG DE MARCACIONES ===
                                                                            $iu = RrhhSalida::find($row4['id']);

                                                                            $iu->estado = '3';

                                                                            $iu->save();
                                                                        break;
                                                                    }
                                                                }
                                                                elseif($row4['con_sin_retorno'] == '2')
                                                                {
                                                                    $fh_s = $row1['fecha'] . " " . $row4['h_salida'];
                                                                    if(($fh_ingreso <= $fh_s) && ($fh_s <= $fh_salida))
                                                                    {
                                                                        // === MODIFICACION A ASISTENCIA ===
                                                                            $iu = RrhhAsistencia::find($row1['id']);

                                                                            $iu->salida_id_s2          = $row4['id'];
                                                                            $iu->log_marcaciones_id_s2 = NULL;
                                                                            // $iu->h1_descuento          = '0';
                                                                            $iu->h2_s_omision_registro = '2';

                                                                            if($marcacion_e_o_sw)
                                                                            {
                                                                                $iu->horario_2_i = $this->omision['1'];
                                                                                $iu->h2_falta    = '2';
                                                                            }

                                                                            $iu->horario_2_s = $this->tipo_salida[$row4['tipo_salida']];

                                                                            $iu->save();

                                                                        // === MODIFICACION A LOG DE MARCACIONES ===
                                                                            $iu = RrhhSalida::find($row4['id']);

                                                                            $iu->estado = '3';

                                                                            $iu->save();
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                }
                                            }
                                    }
                                }

                                $sw_cerrado = FALSE;
                            }
                        }

                        if($sw_cerrado)
                        {
                            $respuesta['respuesta'] .= "Las ASISTENCIAS se encuentran CERRADAS, no se logró sincronizar.";
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "Se logró sincronizar las asistencias.";
                            $respuesta['sw']         = 1;
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "No existe ASISTENCIAS.";
                    }

                return json_encode($respuesta);
                break;

            // === LICENCIA POR VACACIONES ===
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
                        'titulo'     => '<div class="text-center"><strong>Vacaciones</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo
                    );

                // === PERMISOS ===
                    if(!in_array(['codigo' => '1304'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR VACACIONES.";
                        return json_encode($respuesta);
                    }


                // === ANALISIS DE LAS VARIABLES ===
                    if( ! ($request->has('id')))
                    {
                        $respuesta['respuesta'] .= "La ID es obligatorio.";
                        return json_encode($respuesta);
                    }

                    if( ! ($request->has('horario')))
                    {
                        $respuesta['respuesta'] .= "El HORARIO es obligatorio.";
                        return json_encode($respuesta);
                    }

                //=== CARGAR VARIABLES ===
                    $data1['id']      = trim($request->input('id'));
                    $data1['horario'] = trim($request->input('horario'));

                //=== ANALISIS DE LAS VACACIONES ===

                    $tabla1  = "rrhh_asistencias";

                    $select = "
                        $tabla1.id,
                        $tabla1.persona_id,

                        $tabla1.persona_id_rrhh_h1_i,
                        $tabla1.persona_id_rrhh_h1_s,
                        $tabla1.persona_id_rrhh_h2_i,
                        $tabla1.persona_id_rrhh_h2_s,

                        $tabla1.cargo_id,
                        $tabla1.unidad_desconcentrada_id,

                        $tabla1.log_marcaciones_id_i1,
                        $tabla1.log_marcaciones_id_s1,
                        $tabla1.log_marcaciones_id_i2,
                        $tabla1.log_marcaciones_id_s2,

                        $tabla1.horario_id_1,
                        $tabla1.horario_id_2,

                        $tabla1.salida_id_i1,
                        $tabla1.salida_id_s1,
                        $tabla1.salida_id_i2,
                        $tabla1.salida_id_s2,

                        $tabla1.fthc_id_h1,
                        $tabla1.fthc_id_h2,

                        $tabla1.estado,
                        $tabla1.fecha,

                        $tabla1.h1_i_omitir,
                        $tabla1.h1_s_omitir,
                        $tabla1.h2_i_omitir,
                        $tabla1.h2_s_omitir,

                        $tabla1.h1_min_retrasos,
                        $tabla1.h2_min_retrasos,

                        $tabla1.h1_descuento,
                        $tabla1.h2_descuento,

                        $tabla1.h1_i_omision_registro,
                        $tabla1.h1_s_omision_registro,
                        $tabla1.h2_i_omision_registro,
                        $tabla1.h2_s_omision_registro,

                        $tabla1.f_omision_registro,
                        $tabla1.e_omision_registro,

                        $tabla1.h1_falta,
                        $tabla1.h2_falta,

                        $tabla1.observaciones,
                        $tabla1.justificacion,

                        $tabla1.horario_1_i,
                        $tabla1.horario_1_s,

                        $tabla1.horario_2_i,
                        $tabla1.horario_2_s
                    ";

                    $array_where = "$tabla1.id=" . $data1['id'];

                    $consulta1 = RrhhAsistencia::whereRaw($array_where)
                        ->select(DB::raw($select))
                        ->first();

                    if(count($consulta1) > 0)
                    {
                        if($consulta1['estado'] != '3')
                        {
                            $persona_id = Auth::user()->persona_id;

                            $iu = RrhhAsistencia::find($data1['id']);

                            if($data1['horario'] == '1')
                            {
                                $iu->persona_id_rrhh_h1_i = $persona_id;
                                $iu->persona_id_rrhh_h1_s = $persona_id;

                                if($consulta1['h1_falta'] == '1')
                                {
                                    $iu->h1_i_omitir = '2';
                                    $iu->h1_s_omitir = '2';
                                    $iu->h1_falta    = '2';

                                    $iu->horario_1_i = $this->omitir['2'];
                                    $iu->horario_1_s = $this->omitir['2'];

                                    $respuesta['respuesta'] .= "Se logró registrar las VACACIONES en el HORARIO 1.";

                                    $respuesta['sw'] = 1;
                                }
                                elseif(($consulta1['h1_i_omitir'] == '2') && ($consulta1['h1_s_omitir'] == '2'))
                                {
                                    $iu->h1_i_omitir = '1';
                                    $iu->h1_s_omitir = '1';
                                    $iu->h1_falta    = '1';

                                    $iu->horario_1_i = $this->falta['1'];
                                    $iu->horario_1_s = $this->falta['1'];

                                    $respuesta['respuesta'] .= "Se logró quitar las VACACIONES en el HORARIO 1.";

                                    $respuesta['sw'] = 1;
                                }
                            }
                            elseif($data1['horario'] == '2')
                            {
                                $iu->persona_id_rrhh_h2_i = $persona_id;
                                $iu->persona_id_rrhh_h2_s = $persona_id;

                                if($consulta1['h2_falta'] == '1')
                                {
                                    $iu->h2_i_omitir = '2';
                                    $iu->h2_s_omitir = '2';
                                    $iu->h2_falta    = '2';

                                    $iu->horario_2_i = $this->omitir['2'];
                                    $iu->horario_2_s = $this->omitir['2'];

                                    $respuesta['respuesta'] .= "Se logró registrar las VACACIONES en el HORARIO 2.";

                                    $respuesta['sw'] = 1;
                                }
                                elseif(($consulta1['h2_i_omitir'] == '2') && ($consulta1['h2_s_omitir'] == '2'))
                                {
                                    $iu->h2_i_omitir = '1';
                                    $iu->h2_s_omitir = '1';
                                    $iu->h2_falta    = '1';

                                    $iu->horario_2_i = $this->falta['1'];
                                    $iu->horario_2_s = $this->falta['1'];

                                    $respuesta['respuesta'] .= "Se logró quitar las VACACIONES en el HORARIO 2.";

                                    $respuesta['sw'] = 1;
                                }
                            }
                            else
                            {
                                $respuesta['respuesta'] .= "No existe HORARIO.";
                                return json_encode($respuesta);
                            }

                            $iu->save();
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "La ASISTENCIA se encuentran CERRADA.";
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "No existe la ASISTENCIA.";
                    }

                return json_encode($respuesta);
                break;

            // === LICENCIA POR MIGRACION ===
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
                        'titulo'     => '<div class="text-center"><strong>Migración</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo
                    );

                // === PERMISOS ===
                    if(!in_array(['codigo' => '1305'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR/QUITAR MIGRACION.";
                        return json_encode($respuesta);
                    }

                // === ANALISIS DE LAS VARIABLES ===
                    if( ! ($request->has('id')))
                    {
                        $respuesta['respuesta'] .= "La ID es obligatorio.";
                        return json_encode($respuesta);
                    }

                    if( ! ($request->has('horario')))
                    {
                        $respuesta['respuesta'] .= "El HORARIO es obligatorio.";
                        return json_encode($respuesta);
                    }

                    if( ! ($request->has('salida_entrada')))
                    {
                        $respuesta['respuesta'] .= "El SALIDA/ENTRADA es obligatorio.";
                        return json_encode($respuesta);
                    }

                //=== CARGAR VARIABLES ===
                    $data1['id']             = trim($request->input('id'));
                    $data1['horario']        = trim($request->input('horario'));
                    $data1['salida_entrada'] = trim($request->input('salida_entrada'));

                //=== ANALISIS DE LAS VACACIONES ===
                    $tabla1  = "rrhh_asistencias";

                    $select = "
                        $tabla1.id,
                        $tabla1.persona_id,

                        $tabla1.persona_id_rrhh_h1_i,
                        $tabla1.persona_id_rrhh_h1_s,
                        $tabla1.persona_id_rrhh_h2_i,
                        $tabla1.persona_id_rrhh_h2_s,

                        $tabla1.cargo_id,
                        $tabla1.unidad_desconcentrada_id,

                        $tabla1.log_marcaciones_id_i1,
                        $tabla1.log_marcaciones_id_s1,
                        $tabla1.log_marcaciones_id_i2,
                        $tabla1.log_marcaciones_id_s2,

                        $tabla1.horario_id_1,
                        $tabla1.horario_id_2,

                        $tabla1.salida_id_i1,
                        $tabla1.salida_id_s1,
                        $tabla1.salida_id_i2,
                        $tabla1.salida_id_s2,

                        $tabla1.fthc_id_h1,
                        $tabla1.fthc_id_h2,

                        $tabla1.estado,
                        $tabla1.fecha,

                        $tabla1.h1_i_omitir,
                        $tabla1.h1_s_omitir,
                        $tabla1.h2_i_omitir,
                        $tabla1.h2_s_omitir,

                        $tabla1.h1_min_retrasos,
                        $tabla1.h2_min_retrasos,

                        $tabla1.h1_descuento,
                        $tabla1.h2_descuento,

                        $tabla1.h1_i_omision_registro,
                        $tabla1.h1_s_omision_registro,
                        $tabla1.h2_i_omision_registro,
                        $tabla1.h2_s_omision_registro,

                        $tabla1.f_omision_registro,
                        $tabla1.e_omision_registro,

                        $tabla1.h1_falta,
                        $tabla1.h2_falta,

                        $tabla1.observaciones,
                        $tabla1.justificacion,

                        $tabla1.horario_1_i,
                        $tabla1.horario_1_s,

                        $tabla1.horario_2_i,
                        $tabla1.horario_2_s
                    ";

                    $array_where = "$tabla1.id=" . $data1['id'];

                    $consulta1 = RrhhAsistencia::whereRaw($array_where)
                        ->select(DB::raw($select))
                        ->first();

                    if(count($consulta1) > 0)
                    {
                        if($consulta1['estado'] != '3')
                        {
                            $persona_id = Auth::user()->persona_id;

                            $iu = RrhhAsistencia::find($data1['id']);

                            if($data1['horario'] == '1')
                            {
                                if($data1['salida_entrada'] == "1")
                                {
                                    $iu->persona_id_rrhh_h1_i = $persona_id;

                                    if($consulta1['h1_i_omitir'] != "2")
                                    {
                                        if(($consulta1['h1_falta'] == '1') || ($consulta1['h1_i_omision_registro'] == '1') || ($consulta1['log_marcaciones_id_i1'] != ''))
                                        {
                                            $iu->h1_i_omitir           = '3';
                                            $iu->h1_falta              = '2';
                                            $iu->h1_i_omision_registro = '2';
                                            $iu->h1_min_retrasos       = 0;

                                            $iu->horario_1_i = $this->omitir['3'];

                                            $respuesta['respuesta'] .= "Se logró registrar la MIGRACIÓN en el HORARIO 1 de la ENTRADA.";

                                            $respuesta['sw'] = 1;
                                        }
                                        elseif($consulta1['h1_i_omitir'] == '3')
                                        {
                                            $iu->h1_i_omitir = '1';
                                            if(($consulta1['log_marcaciones_id_s1'] != '') || ($consulta1['salida_id_s1'] != ''))
                                            {
                                                $iu->h1_falta              = '2';
                                                $iu->h1_i_omision_registro = '1';

                                                $iu->horario_1_i = $this->omision['1'];

                                                $respuesta['respuesta'] .= "Se logró quitar las MIGRACIÓN en el HORARIO 1 de la ENTRADA.";

                                                $respuesta['sw'] = 1;
                                            }
                                            else
                                            {
                                                $iu->h1_falta              = '1';
                                                $iu->h1_i_omision_registro = '1';

                                                $iu->horario_1_i = $this->falta['1'];
                                            }

                                            $respuesta['respuesta'] .= "Se logró quitar las MIGRACIÓN en el HORARIO 1 de la ENTRADA.";

                                            $respuesta['sw'] = 1;
                                        }
                                    }
                                    else
                                    {
                                        $respuesta['respuesta'] .= "La ASISTENCIA se cambio a VACACIONES.";
                                        return json_encode($respuesta);
                                    }
                                }
                                elseif($data1['salida_entrada'] == "2")
                                {
                                    $iu->persona_id_rrhh_h1_s = $persona_id;

                                    if($consulta1['h1_s_omitir'] != "2")
                                    {
                                        if(($consulta1['h1_falta'] == '1') || ($consulta1['h1_s_omision_registro'] == '1'))
                                        {
                                            $iu->h1_s_omitir           = '3';
                                            $iu->h1_falta              = '2';
                                            $iu->h1_s_omision_registro = '2';

                                            $iu->horario_1_s = $this->omitir['3'];

                                            $respuesta['respuesta'] .= "Se logró registrar la MIGRACIÓN en el HORARIO 1 de la SALIDA.";

                                            $respuesta['sw'] = 1;
                                        }
                                        elseif($consulta1['h1_s_omitir'] == '3')
                                        {
                                            $iu->h1_s_omitir = '1';
                                            if(($consulta1['log_marcaciones_id_i1'] != '') || ($consulta1['salida_id_i1'] != ''))
                                            {
                                                $iu->h1_falta              = '2';
                                                $iu->h1_s_omision_registro = '1';

                                                $iu->horario_1_s = $this->omision['1'];

                                                $respuesta['respuesta'] .= "Se logró quitar las MIGRACIÓN en el HORARIO 1 de la SALIDA.";

                                                $respuesta['sw'] = 1;
                                            }
                                            else
                                            {
                                                $iu->h1_falta              = '1';
                                                $iu->h1_s_omision_registro = '1';

                                                $iu->horario_1_s = $this->falta['1'];
                                            }

                                            $respuesta['respuesta'] .= "Se logró quitar las MIGRACIÓN en el HORARIO 1 de la ENTRADA.";

                                            $respuesta['sw'] = 1;
                                        }
                                    }
                                    else
                                    {
                                        $respuesta['respuesta'] .= "La ASISTENCIA se cambio a VACACIONES.";
                                        return json_encode($respuesta);
                                    }
                                }
                                else
                                {
                                    $respuesta['respuesta'] .= "No existe ENTRADA/SALIDA.";
                                    return json_encode($respuesta);
                                }
                            }
                            elseif($data1['horario'] == '2')
                            {
                                if($data1['salida_entrada'] == "1")
                                {
                                    $iu->persona_id_rrhh_h2_i = $persona_id;

                                    if($consulta1['h2_i_omitir'] != "2")
                                    {
                                        if(($consulta1['h2_falta'] == '1') || ($consulta1['h2_i_omision_registro'] == '1') || ($consulta1['log_marcaciones_id_i2'] != ''))
                                        {
                                            $iu->h2_i_omitir           = '3';
                                            $iu->h2_falta              = '2';
                                            $iu->h2_i_omision_registro = '2';
                                            $iu->h2_min_retrasos       = 0;

                                            $iu->horario_2_i = $this->omitir['3'];

                                            $respuesta['respuesta'] .= "Se logró registrar la MIGRACIÓN en el HORARIO 2 de la ENTRADA.";

                                            $respuesta['sw'] = 1;
                                        }
                                        elseif($consulta1['h2_i_omitir'] == '3')
                                        {
                                            $iu->h2_i_omitir = '1';
                                            if(($consulta1['log_marcaciones_id_s2'] != '') || ($consulta1['log_marcaciones_id_s2'] != ''))
                                            {
                                                $iu->h2_falta              = '2';
                                                $iu->h2_i_omision_registro = '1';

                                                $iu->horario_1_i = $this->omision['1'];

                                                $respuesta['respuesta'] .= "Se logró quitar las MIGRACIÓN en el HORARIO 1 de la ENTRADA.";

                                                $respuesta['sw'] = 1;
                                            }
                                            else
                                            {
                                                $iu->h2_falta              = '1';
                                                $iu->h2_i_omision_registro = '1';

                                                $iu->horario_2_i = $this->falta['1'];
                                            }

                                            $respuesta['respuesta'] .= "Se logró quitar las MIGRACIÓN en el HORARIO 2 de la ENTRADA.";

                                            $respuesta['sw'] = 1;
                                        }
                                    }
                                    else
                                    {
                                        $respuesta['respuesta'] .= "La ASISTENCIA se cambio a VACACIONES.";
                                        return json_encode($respuesta);
                                    }
                                }
                                elseif($data1['salida_entrada'] == "2")
                                {
                                    $iu->persona_id_rrhh_h2_s = $persona_id;

                                    if($consulta1['h2_s_omitir'] != "2")
                                    {
                                        if(($consulta1['h2_falta'] == '1') || ($consulta1['h2_s_omision_registro'] == '1'))
                                        {
                                            $iu->h2_s_omitir           = '3';
                                            $iu->h2_falta              = '2';
                                            $iu->h2_s_omision_registro = '2';

                                            $iu->horario_2_s = $this->omitir['3'];

                                            $respuesta['respuesta'] .= "Se logró registrar la MIGRACIÓN en el HORARIO 2 de la SALIDA.";

                                            $respuesta['sw'] = 1;
                                        }
                                        elseif($consulta1['h2_s_omitir'] == '3')
                                        {
                                            $iu->h2_s_omitir = '1';
                                            if(($consulta1['log_marcaciones_id_i2'] != '') || ($consulta1['salida_id_i2'] != ''))
                                            {
                                                $iu->h2_falta              = '2';
                                                $iu->h2_s_omision_registro = '1';

                                                $iu->horario_2_s = $this->omision['1'];

                                                $respuesta['respuesta'] .= "Se logró quitar las MIGRACIÓN en el HORARIO 2 de la SALIDA.";

                                                $respuesta['sw'] = 1;
                                            }
                                            else
                                            {
                                                $iu->h2_falta              = '1';
                                                $iu->h2_s_omision_registro = '1';

                                                $iu->horario_2_s = $this->falta['1'];
                                            }

                                            $respuesta['respuesta'] .= "Se logró quitar las MIGRACIÓN en el HORARIO 2 de la ENTRADA.";

                                            $respuesta['sw'] = 1;
                                        }
                                    }
                                    else
                                    {
                                        $respuesta['respuesta'] .= "La ASISTENCIA se cambio a VACACIONES.";
                                        return json_encode($respuesta);
                                    }
                                }
                                else
                                {
                                    $respuesta['respuesta'] .= "No existe ENTRADA/SALIDA.";
                                    return json_encode($respuesta);
                                }
                            }
                            else
                            {
                                $respuesta['respuesta'] .= "No existe HORARIO.";
                                return json_encode($respuesta);
                            }

                            $iu->save();
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "La ASISTENCIA se encuentran CERRADA.";
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "No existe la ASISTENCIA.";
                    }

                return json_encode($respuesta);
                break;

            // === ELIMINAR ASISTENCIA ===
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
                        'titulo'     => '<div class="text-center"><strong>Eliminar asistencia</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo
                    );

                // === PERMISOS ===
                    if(!in_array(['codigo' => '1306'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para ELIMINAR ASISTENCIA.";
                        return json_encode($respuesta);
                    }

                // === ANALISIS DE LAS VARIABLES ===
                    if( ! ($request->has('id') || ($request->has('fecha_del') && $request->has('fecha_al'))))
                    {
                        $respuesta['respuesta'] .= "La ID o la fecha del y fecha al son obligatorios.";
                        return json_encode($respuesta);
                    }

                //=== CARGAR VARIABLES ===
                    $data1['id'] = trim($request->input('id'));

                    $data1['fecha_del']                        = trim($request->input('fecha_del'));
                    $data1['fecha_al']                         = trim($request->input('fecha_al'));
                    $data1['persona_id']                       = trim($request->input('persona_id'));
                    $data1['lugar_dependencia_id_funcionario'] = trim($request->input('lugar_dependencia_id_funcionario'));

                //=== ANALISIS DE LAS VACACIONES ===
                    $tabla1 = "rrhh_asistencias";
                    $tabla2 = "inst_unidades_desconcentradas";

                    $array_where = "$tabla1.estado <> '3'";
                    if($request->has('id'))
                    {
                        $array_where .= " AND $tabla1.id=" . $data1['id'];
                    }

                    if($request->has('fecha_del'))
                    {
                        $array_where .= " AND $tabla1.fecha >= '" . $data1['fecha_del'] . "'";
                    }

                    if($request->has('fecha_al'))
                    {
                        $array_where .= " AND $tabla1.fecha <= '" . $data1['fecha_al'] . "'";
                    }

                    if($request->has('lugar_dependencia_id_funcionario'))
                    {
                        $array_where .= " AND a2.lugar_dependencia_id=" . $data1['lugar_dependencia_id_funcionario'];
                    }

                    if($request->has('persona_id'))
                    {
                        $array_where .= " AND $tabla1.persona_id=" . $data1['persona_id'];
                    }

                    $select = "
                        $tabla1.id,
                        $tabla1.estado
                    ";

                    $consulta1 = RrhhAsistencia::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.unidad_desconcentrada_id")
                        ->whereRaw($array_where)
                        ->select(DB::raw($select))
                        ->get()
                        ->toArray();

                    if(count($consulta1) > 0)
                    {
                        $sw_asistencia = FALSE;
                        foreach($consulta1 as $row1)
                        {
                            if($row1['estado'] != '3')
                            {
                                $iu = RrhhAsistencia::find($row1['id']);
                                $iu->delete();

                                $sw_asistencia = TRUE;
                            }
                        }

                        if($sw_asistencia)
                        {
                            $respuesta['respuesta'] .= "Se logró eliminar la(s) ASISTENCIA(S).";

                            $respuesta['sw'] = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "No logró eliminar la(s) ASISTENCIA(S).";
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "No existe la ASISTENCIA.";
                    }

                return json_encode($respuesta);
                break;

            // === CERRAR ASISTENCIA ===
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
                        'titulo'     => '<div class="text-center"><strong>Cerrar asistencia</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo
                    );

                // === PERMISOS ===
                    if(!in_array(['codigo' => '1308'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para CERRAR ASISTENCIA.";
                        return json_encode($respuesta);
                    }

                // === ANALISIS DE LAS VARIABLES ===
                    if( ! ($request->has('id') || ($request->has('fecha_del') && $request->has('fecha_al'))))
                    {
                        $respuesta['respuesta'] .= "La ID o la fecha del y fecha al, son obligatorios.";
                        return json_encode($respuesta);
                    }

                //=== CARGAR VARIABLES ===
                    $data1['id'] = trim($request->input('id'));

                    $data1['fecha_del']                        = trim($request->input('fecha_del'));
                    $data1['fecha_al']                         = trim($request->input('fecha_al'));
                    $data1['persona_id']                       = trim($request->input('persona_id'));
                    $data1['lugar_dependencia_id_funcionario'] = trim($request->input('lugar_dependencia_id_funcionario'));

                //=== ANALISIS ===
                    $tabla1 = "rrhh_asistencias";
                    $tabla2 = "inst_unidades_desconcentradas";

                    $array_where = "$tabla1.estado <> '3'";
                    if($request->has('id'))
                    {
                        $array_where .= " AND $tabla1.id=" . $data1['id'];
                    }

                    if($request->has('fecha_del'))
                    {
                        $array_where .= " AND $tabla1.fecha >= '" . $data1['fecha_del'] . "'";
                    }

                    if($request->has('fecha_al'))
                    {
                        $array_where .= " AND $tabla1.fecha <= '" . $data1['fecha_al'] . "'";
                    }

                    if($request->has('lugar_dependencia_id_funcionario'))
                    {
                        $array_where .= " AND a2.lugar_dependencia_id=" . $data1['lugar_dependencia_id_funcionario'];
                    }

                    if($request->has('persona_id'))
                    {
                        $array_where .= " AND $tabla1.persona_id=" . $data1['persona_id'];
                    }

                    $select = "
                        $tabla1.id,
                        $tabla1.estado
                    ";

                    $consulta1 = RrhhAsistencia::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.unidad_desconcentrada_id")
                        ->whereRaw($array_where)
                        ->select(DB::raw($select))
                        ->get()
                        ->toArray();

                    if(count($consulta1) > 0)
                    {
                        $sw_asistencia = FALSE;
                        foreach($consulta1 as $row1)
                        {
                            if($row1['estado'] != '3')
                            {
                                $iu = RrhhAsistencia::find($row1['id']);

                                $iu->estado = '3';

                                $iu->save();

                                $sw_asistencia = TRUE;
                            }
                        }

                        if($sw_asistencia)
                        {
                            $respuesta['respuesta'] .= "Se logró cerrar la(s) ASISTENCIA(S).";

                            $respuesta['sw'] = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "No logró cerar la(s) ASISTENCIA(S).";
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "No existe la ASISTENCIA.";
                    }

                return json_encode($respuesta);
                break;

            // === DONDE ASISTIO ===
            case '50':
                $respuesta = [
                    'tipo' => $tipo,
                    'sw'   => 1
                ];
                if($request->has('id'))
                {
                    $id = $request->input('id');

                    $tabla1 = "rrhh_log_marcaciones";
                    $tabla2 = "rrhh_biometricos";
                    $tabla3 = "inst_unidades_desconcentradas";
                    $tabla4 = "inst_lugares_dependencia";

                    $select = "
                        $tabla1.id,
                        $tabla1.biometrico_id,
                        $tabla1.persona_id,
                        $tabla1.f_marcacion,

                        a2.unidad_desconcentrada_id,
                        a2.codigo_af,
                        a2.ip,

                        a3.lugar_dependencia_id,
                        a3.nombre AS unidad_desconcentrada,

                        a4.nombre AS lugar_dependencia
                    ";

                    $array_where = "$tabla1.id=" . $id;

                    $query = RrhhLogMarcacion::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.biometrico_id")
                        ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.unidad_desconcentrada_id")
                        ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                        ->whereRaw($array_where)
                        ->select(DB::raw($select))
                        ->first();
                    if(count($query) > 0)
                    {
                        $respuesta['consulta'] = $query;
                        $respuesta['sw']       = 2;
                    }
                }
                return json_encode($respuesta);
                break;
            // === FERIADO, TOLERANCIA, HORARIO CONTINUO ===
            case '51':
                $respuesta = [
                    'tipo' => $tipo,
                    'sw'   => 1
                ];
                if($request->has('id'))
                {
                    $id = $request->input('id');

                    $tabla1 = "rrhh_fthc";
                    $tabla2 = "inst_lugares_dependencia";
                    $tabla3 = "inst_unidades_desconcentradas";

                    $select = "
                        $tabla1.id,
                        $tabla1.lugar_dependencia_id,
                        $tabla1.unidad_desconcentrada_id,
                        $tabla1.horario_id,

                        $tabla1.estado,
                        $tabla1.fecha,
                        $tabla1.nombre,
                        $tabla1.tipo_fthc,
                        $tabla1.tipo_horario,
                        $tabla1.sexo,

                        a2.nombre AS lugar_dependencia,

                        a3.nombre AS unidad_desconcentrada
                    ";

                    $array_where = "$tabla1.id=" . $id;

                    $query = RrhhFthc::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.lugar_dependencia_id")
                        ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.unidad_desconcentrada_id")
                        ->whereRaw($array_where)
                        ->select(DB::raw($select))
                        ->first();
                    if(count($query) > 0)
                    {
                        $respuesta['consulta'] = $query;
                        $respuesta['sw']       = 2;
                    }
                }
                return json_encode($respuesta);
                break;
            // === MOSTRAR USUARIO QUE MODIFICO LA ASISTENCIA ===
            case '52':
                $respuesta = [
                    'tipo' => $tipo,
                    'sw'   => 1
                ];
                if($request->has('id'))
                {
                    $id = $request->input('id');

                    $query = RrhhPersona::where("id", "=", $id)
                        ->select(DB::raw("id, CONCAT_WS(' - ', n_documento, CONCAT_WS(' ', nombre, ap_paterno, ap_materno)) AS text"))
                        ->get()
                        ->first();

                    if(count($query) > 0)
                    {
                        $respuesta['consulta'] = $query;
                        $respuesta['sw']       = 2;
                    }
                }
                return json_encode($respuesta);
                break;

            // === SELECT2 PERSONA ===
            case '100':
                if($request->has('q'))
                {
                    $nombre     = $request->input('q');
                    $estado     = trim($request->input('estado'));
                    $page_limit = trim($request->input('page_limit'));

                    $query = RrhhPersona::whereRaw("CONCAT_WS(' - ', n_documento, CONCAT_WS(' ', ap_paterno, ap_materno, nombre)) ilike '%$nombre%'")
                                ->where("estado", "=", $estado)
                                ->select(DB::raw("id, CONCAT_WS(' - ', n_documento, CONCAT_WS(' ', ap_paterno, ap_materno, nombre)) AS text"))
                                ->orderByRaw("CONCAT_WS(' ', ap_paterno, ap_materno, nombre) ASC")
                                ->limit($page_limit)
                                ->get()
                                ->toArray();

                    if(count($query) > 0)
                    {
                        $respuesta = [
                            "results"  => $query,
                            "paginate" => [
                                "more" =>true
                            ]
                        ];
                        return json_encode($respuesta);
                    }
                }
                break;

            // === SELECT2 AUO POR LUGAR DE DEPENDENCIA ===
            case '101':
                $respuesta = [
                    'tipo' => $tipo,
                    'sw'   => 1
                ];
                if($request->has('lugar_dependencia_id'))
                {
                    $lugar_dependencia_id  = $request->input('lugar_dependencia_id');

                    $query = InstAuo::where("lugar_dependencia_id", "=", $lugar_dependencia_id)
                        ->where("estado", "=", 1)
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

            // === SELECT2 CARGOS POR AUO ===
            case '102':
                $respuesta = [
                    'tipo' => $tipo,
                    'sw'   => 1
                ];
                if($request->has('auo_id'))
                {
                    $auo_id = $request->input('auo_id');

                    $query = InstCargo::where("auo_id", "=", $auo_id)
                        ->where("estado", "=", 1)
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

            // === SELECT2 UNIDAD DESCONCENTRADA POR LUGAR DE DEPENDENCIA ===
            case '103':
                $respuesta = [
                    'tipo' => $tipo,
                    'sw'   => 1
                ];
                if($request->has('lugar_dependencia_id'))
                {
                    $lugar_dependencia_id = $request->input('lugar_dependencia_id');

                    $query = InstUnidadDesconcentrada::where("lugar_dependencia_id", "=", $lugar_dependencia_id)
                        ->select('id', 'nombre')
                        ->get()
                        ->toArray();

                    $horario_1 = RrhhHorario::where("lugar_dependencia_id", "=", $lugar_dependencia_id)
                        ->where("tipo_horario", "=", '1')
                        ->select('id', 'nombre', 'defecto')
                        ->get()
                        ->toArray();

                    $horario_2 = RrhhHorario::where("lugar_dependencia_id", "=", $lugar_dependencia_id)
                        ->where("tipo_horario", "=", '2')
                        ->select('id', 'nombre', 'defecto')
                        ->get()
                        ->toArray();
                    if(count($query) > 0)
                    {
                        $respuesta['consulta'] = $query;
                        $respuesta['sw']       = 2;

                        $respuesta['sw_horario_1'] = 1;
                        if(count($horario_1) > 0)
                        {
                            $respuesta['horario_1']    = $horario_1;
                            $respuesta['sw_horario_1'] = 2;
                        }

                        $respuesta['sw_horario_2'] = 1;
                        if(count($horario_2) > 0)
                        {
                            $respuesta['horario_2']    = $horario_2;
                            $respuesta['sw_horario_2'] = 2;
                        }
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
                        $respuesta = '<span class="label label-success font-sm">' . $this->estado[$valor['estado']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
                        return($respuesta);
                        break;
                }
                break;
            case '2':
                if($valor['min_retrasos'] > 0)
                {
                    $respuesta = '<span class="label label-danger font-sm">' . $valor['min_retrasos'] . '</span>';
                }
                else
                {
                    $respuesta = '<span class="label label-primary font-sm">' . $valor['min_retrasos'] . '</span>';
                }

                return($respuesta);
                break;
            case '3':
                $respuesta = "";
                if($valor['log_marcaciones_id'] != '')
                {
                    $respuesta = '<button class="btn btn-xs btn-success" onclick="utilitarios([18, ' . $valor['log_marcaciones_id'] . ', ' . $valor['id'] . ']);" title="Ver donde asistio" style="margin:0px 0px 0px 0px; padding-top: 0px; padding-bottom: 0.2px;">
                            <i class="fa fa-eye"></i>
                            <strong>' . $valor['horario'] . '</strong>
                        </button>';
                }
                elseif($valor['salida_id'] != '')
                {
                    $respuesta = '<button class="btn btn-xs btn-primary" onclick="utilitarios([17, ' . $valor['salida_id'] . ', ' . $valor['id'] . ']);" title="Ver licencia o salida" style="margin:0px 0px 0px 0px; padding-top: 0px; padding-bottom: 0.2px;">
                            <i class="fa fa-eye"></i>
                            <strong>' . $valor['horario'] . '</strong>
                        </button>';
                }
                elseif($valor['fthc_id'] != '')
                {
                    if($this->falta['4'] == $valor['horario'])
                    {
                        $respuesta = '<span class="label font-sm">' . $valor['horario'] . '</span>';
                    }
                    else
                    {
                        $respuesta = '<button class="btn btn-xs btn-info" onclick="utilitarios([19, ' . $valor['fthc_id'] . ', ' . $valor['id'] . ', ' . "'" .  $valor['horario'] . "'" . ']);" title="Ver ' . $valor['horario'] . '" style="margin:0px 0px 0px 0px; padding-top: 0px; padding-bottom: 0.2px;">
                                <i class="fa fa-eye"></i>
                                <strong>' . $valor['horario'] . '</strong>
                            </button>';
                    }
                }
                else
                {
                    if($this->falta['1'] == $valor['horario'])
                    {
                        $respuesta = '<span class="label label-danger font-sm">' . $valor['horario'] . '</span>';
                    }
                    elseif($this->omision['1'] == $valor['horario'])
                    {
                        $respuesta = '<button class="btn btn-xs btn-warning" onclick="utilitarios([22, ' . $valor['id'] . ', ' . "'" . $valor['fecha'] . "'" . ', ' . $valor['persona_id'] . ']);" title="Ver marcaciones" style="margin:0px 0px 0px 0px; padding-top: 0px; padding-bottom: 0.2px;">
                            <i class="fa fa-table"></i>
                            <strong>' . $valor['horario'] . '</strong>
                        </button>';
                    }
                    elseif($this->falta['4'] == $valor['horario'])
                    {
                        $respuesta = '<span class="label font-sm">' . $valor['horario'] . '</span>';
                    }
                    else
                    {
                        $respuesta = '<button class="btn btn-xs btn-warning" onclick="utilitarios([24, ' . $valor['id'] . ', ' . $valor['persona_id_rrhh'] . ']);" title="Ver USUARIO que modificó la ASISTENCIA" style="margin:0px 0px 0px 0px; padding-top: 0px; padding-bottom: 0.2px;">
                            <i class="fa fa-eye"></i>
                            <strong>' . $valor['horario'] . '</strong>
                        </button>';
                    }
                }
                return($respuesta);
                break;

            case '4':
                switch($valor['pdf'])
                {
                    case '1':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->no_si[$valor['pdf']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<button class="btn btn-xs btn-primary" onclick="utilitarios([21, ' . $valor['id'] . ', ' . $valor['dia_hora'] . ']);" title="Clic ver el documento">
                            <i class="fa fa-cloud-download"></i>
                            <strong>' . $this->no_si[$valor['pdf']] . '</strong>
                        </button>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
                        return($respuesta);
                        break;
                }
                break;

            case '100':
                PDF::Image(
                    $valor['file'],     // file: nombre del archivo
                    $valor['x'],        // x: abscisa de la esquina superior izquierda LTR, esquina superior derecha RTL
                    $valor['y'],        // y: ordenada de la esquina superior izquierda LTR, esquina superior derecha RTL
                    $valor['w'],        // w: ancho de la imagen, 0=se calcula automaticamente
                    $valor['h'],        // w: altura de la imagen, 0=se calcula automaticamente
                    $valor['type'],     // type: formato de la imagen, JPEG, PNG, GIF  y otros. Si no se especifica, el tipo se infiere de la extensión del archivo
                    $valor['link'],     // link: URL o enlace
                    $valor['align'],    // align: indica la alineacion del puntero junto a la insercion de imagenes en relacion de su altura. T=parte superior derecha LTR o de arriba a la izquierda para RTL, M=de mediana adecuado para LTR o media izquierda para RTL, B=para inferior derecha de LTR o de abajo hacia la izquierda para RTL, N=linea siguiente
                    $valor['resize'],   // resize: TRUE=reduce al tamaño de x-y, FALSE=no reduce nada
                    $valor['dpi'],      // dpi: puntos por pulgada de resolucion utilizado en redimensionamiento
                    $valor['palign'],   // palign: permite centra y alinear. L=alinear a la izquierda, C=centro, R=Alinear a la derecha, ''=cadena vacia, LTR o RTL
                    $valor['ismask'],   // ismask: TRUE=es mascara, FALSE=no es mascara
                    $valor['imgsmask'], // imgsmask: imagen objeto, FALSE=contrario
                    $valor['border'],   // border: borde de la celda 0,1 o L=Left, T=Top, R= Rigth, B=Bottom
                    $valor['fitbox'],   // fitbox: borde de la celda 0,1 o L=Left, T=Top, R= Rigth, B=Bottom
                    $valor['hidden'],   // hidden: TRUE=no muestra la imagen, FALSE=muestra la imagen
                    $valor['fitonpage'] // fitonpage: TRUE=la imagen se redimensiona para no exceder las dimensiones de la pagina, FALSE=no pasa nada
                );
                break;
            case '101':
                PDF::Rect(
                    $valor['x'],        // x: abscisa de la esquina superior izquierda LTR, esquina superior derecha RTL
                    $valor['y'],        // y: ordenada de la esquina superior izquierda LTR, esquina superior derecha RTL
                    $valor['w'],        // w: ancho
                    $valor['h'],        // w: altura
                    $valor['style'],    // Estilo de renderizado Los valores posibles son:
                                        // D o cadena vacía: Dibujar (predeterminado).
                                        // F: llenar.
                                        // DF o FD: Dibujar y llenar.
                                        // CNZ: modo de recorte (usando la regla par impar para determinar qué regiones se encuentran dentro del trazado de recorte).
                                        // CEO: modo de recorte (utilizando la regla del número de devanado distinto de cero para determinar qué regiones se encuentran dentro del trazado de recorte)
                    $valor['border_style'], // Estilo del borde del rectángulo Arreglar como para SetLineStyle . Valor predeterminado: estilo de línea predeterminado (matriz vacía).
                    $valor['fill_color'] // Color de relleno. Formato: matriz (GRIS) o matriz (R, G, B) o matriz (C, M, Y, K). Valor predeterminado: color predeterminado (matriz vacía).
                );
                break;
            case '102':
                PDF::Line(
                    $valor['x1'],   // x1: Abscisa del primer punto.
                    $valor['y1'],   // y1: Ordenado del primer punto.
                    $valor['x2'],   // x2: Abscisa del segundo punto.
                    $valor['y2'],   // y2: Ordenado del segundo punto
                    $valor['style'] // Estilo de línea Arreglar como para SetLineStyle. Valor predeterminado: estilo de línea predeterminado (matriz vacía).
                );
                break;
            case '110':
                PDF::Write(
                    $valor['h'],        // Altura de la línea
                    $valor['txt'],      // Cadena para mostrar
                    $valor['link'],     // URL o identificador devuelto por AddLink()
                    $valor['fill'],     // Indica si el fondo debe estar pintado (1) o transparente (0). Valor predeterminado: 0.
                    $valor['align'],    // Permite centrar o alinear el texto. Los valores posibles son:
                                        // L o cadena vacía: alineación izquierda (valor predeterminado)
                                        // C: centro
                                        // R: alinear a la derecha
                                        // J: justificar
                    $valor['ln'],       // Si es verdadero, coloque el cursor en la parte inferior de la línea; de lo contrario, coloque el cursor en la parte superior de la línea. Si no se especifica, el tipo se infiere de la extensión del archivo
                    $valor['stretch'],  // estirar el modo los caracteres:
                                        // 0 = deshabilitado
                                        // 1 = escala horizontal solo si es necesario
                                        // 2 = escala horizontal forzada
                                        // 3 = espaciado de caracteres solo si es necesario
                                        // 4 = espaciado de caracteres forzado
                    $valor['firstline'],// Si es verdadero imprime solo la primera línea y devuelve la cadena restante.
                    $valor['firstblock'],// Si es verdadero, la cadena es el comienzo de una línea.
                    $valor['maxh']      // Altura máxima. El texto restante no impreso será devuelto. Debe se > = $ h y menos espacio restante en la parte inferior de la página, o 0 para desactivar esta función.
                );
                break;
            case '111':
                PDF::MultiCell(
                    $valor['x1'],       // Ancho celda
                    $valor['y1'],       // Alto celda
                    $valor['txt'],      // Texto a mostrar
                    $valor['border'],   // Border: 0,1 o L=Left, T=Top, R= Rigth, B=Bottom
                    $valor['align'],    // Align: L=Left, C=Center, R=Rigth, J=Justification
                    $valor['fill'],     // Relleno: TRUE, FALSE
                    $valor['ln'],       // Posicion: 0=a la derecha, 1=a la siguiente linea, 2=a continuacion
                    "",                 // X: Posición en unidades de usuario
                    "",                 // Y: Posición en unidades de usuario
                    true,               // reseth: restablece la altura de la ultima celda
                    $valor['stretch'],  // stretch: estiramiento de la fuente, 0=desactivado, 1=horizontal-ancho de la celda, 2=obligatorio horizontal-ancho de la celda, 3= espacio-ancho de la celda, 4=obligatorio espacio-ancho de la celda
                    $valor['ishtml'],   // ishtml: TRUE=texto HTML, FALSE=texto plano
                    true,               // autopadding: TRUE=ajuste interno automatico, FALSE=ajuste manual
                    $valor['y1'],       // maxh: Altura maxima, 0 si ishtml=TRUE.
                    $valor['valign'],   // valign: Alineación del texto T=Top, M=Middle, B=Bottom, si ishtml=TRUE no funciona
                    $valor['fitcell']   // fitcell: TRUE=intenta encajar en la celda. FALSE=desactivado, si ishtml=TRUE no funciona
                );
                break;
            case '112':
                PDF::write2DBarcode(
                    $valor['code'], // Código para imprimir
                    $valor['type'], // Tipo de código de barras
                    $valor['x'],    // x posición
                    $valor['y'],    // y posición
                    $valor['w'],    // Ancho
                    $valor['h'],    // Altura
                    $valor['style'],// conjunto de opciones:
                    $valor['align'],// Indica la alineación del puntero al lado de la inserción del código de barras con respecto a la altura del código de barras. El valor puede ser:
                                    // T: arriba a la derecha para LTR o arriba a la izquierda para RTL
                                    // M: medio-derecha para LTR o middle-left para RTL
                                    // B: abajo a la derecha para LTR o abajo a la izquierda para RTL
                                    // N: siguiente línea
                    $valor['distort']   // FALSE
                );
                break;
            default:
                break;
        }
    }

    public function reportes(Request $request)
    {
        $tipo = $request->input('tipo');

        switch($tipo)
        {
            case '1':
                if($request->has('salida_id'))
                {
                    $salida_id = trim($request->input('salida_id'));

                    $fh_actual            = date("Y-m-d H-i-s");
                    $dir_logo_institucion = public_path($this->public_dir) . '/' . 'logo_fge_256.png';
                    $dir_logo_pais        = public_path($this->public_dir) . '/' . 'escudo_logo_300.png';
                    $dir_marca_agua       = public_path($this->public_dir) . '/' . 'marca_agua_500.png';

                    // === VALIDAR IMAGENES ===
                        if( ! file_exists($dir_logo_institucion))
                        {
                            return "No existe el logo de la institución " . $dir_logo_institucion;
                        }

                        if( ! file_exists($dir_logo_pais))
                        {
                            return "No existe el logo deL pais " . $dir_logo_pais;
                        }

                        if( ! file_exists($dir_marca_agua))
                        {
                            return "No existe la marca de agua " . $dir_marca_agua;
                        }

                    // === CONSULTA A LA BASE DE DATOS ===
                        $consulta1 = RrhhSalida::where('id', '=', $salida_id)
                            ->first();

                        if( ! (count($consulta1) > 0))
                        {
                            return "No existe la PAPELETA DE SALIDA";
                        }

                        if($consulta1['estado'] == '2')
                        {
                            return "La de PAPELETA DE SALIDA fue ANULADA";
                        }

                        $tabla1 = "rrhh_personas";
                        $tabla2 = "rrhh_funcionarios";

                        $tabla3 = "inst_unidades_desconcentradas";
                        $tabla4 = "inst_lugares_dependencia";

                        $tabla5 = "inst_cargos";
                        $tabla6 = "inst_tipos_cargo";
                        $tabla7 = "inst_auos";

                        $select = "
                            $tabla1.id,
                            $tabla1.n_documento,
                            $tabla1.nombre AS nombre_persona,
                            $tabla1.ap_paterno,
                            $tabla1.ap_materno,
                            $tabla1.sexo,
                            $tabla1.f_nacimiento,

                            a2.id AS funcionario_id,
                            a2.cargo_id,
                            a2.unidad_desconcentrada_id,
                            a2.horario_id_1,
                            a2.horario_id_2,
                            a2.situacion,
                            a2.documento_sw,
                            a2.f_ingreso,
                            a2.f_salida,
                            a2.sueldo,
                            a2.observaciones,
                            a2.documento_file,

                            a3.lugar_dependencia_id AS lugar_dependencia_id_funcionario,
                            a3.nombre AS ud_funcionario,

                            a4.nombre AS lugar_dependencia_funcionario,

                            a5.auo_id,
                            a5.tipo_cargo_id,
                            a5.item_contrato,
                            a5.acefalia,
                            a5.nombre AS cargo,

                            a6.nombre AS tipo_cargo,

                            a7.lugar_dependencia_id AS lugar_dependencia_id_cargo,
                            a7.nombre AS auo_cargo
                        ";

                        $consulta2 = RrhhPersona::leftJoin("$tabla2 AS a2", "a2.persona_id", "=", "$tabla1.id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.unidad_desconcentrada_id")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                            ->leftJoin("$tabla5 AS a5", "a5.id", "=", "a2.cargo_id")
                            ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.tipo_cargo_id")
                            ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a5.auo_id")
                            ->where("$tabla1.id", '=', $consulta1['persona_id'])
                            ->select(DB::raw($select))
                            ->first();

                        if( ! (count($consulta2) > 0))
                        {
                            return "No existe la PERSONA.";
                        }

                        $consulta3 = RrhhPersona::leftJoin("$tabla2 AS a2", "a2.persona_id", "=", "$tabla1.id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.unidad_desconcentrada_id")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                            ->leftJoin("$tabla5 AS a5", "a5.id", "=", "a2.cargo_id")
                            ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.tipo_cargo_id")
                            ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a5.auo_id")
                            ->where("$tabla1.id", '=', $consulta1['persona_id_superior'])
                            ->select(DB::raw($select))
                            ->first();

                        if( ! (count($consulta3) > 0))
                        {
                            return "No existe la INMEDIATO SUPERIOR.";
                        }

                        $persona_id = Auth::user()->persona_id;

                        $consulta4 = RrhhPersona::leftJoin("$tabla2 AS a2", "a2.persona_id", "=", "$tabla1.id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.unidad_desconcentrada_id")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                            ->leftJoin("$tabla5 AS a5", "a5.id", "=", "a2.cargo_id")
                            ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.tipo_cargo_id")
                            ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a5.auo_id")
                            ->where("$tabla1.id", '=', $persona_id)
                            ->select(DB::raw($select))
                            ->first();

                        if( ! (count($consulta4) > 0))
                        {
                            return "Usted no es funcionario del MINISTERIO PUBLICO.";
                        }

                        $consulta5 = RrhhTipoSalida::where("id", "=", $consulta1['tipo_salida_id'])
                            ->select("id", "nombre", "tipo_salida", "tipo_cronograma", "hd_mes")
                            ->first();

                    // === CARGAR VALORES ===
                        $data1 = array(
                            'salida_id' => $salida_id,
                        );

                        $data2 = array(
                            'dir_logo_institucion' => $dir_logo_institucion,
                            'dir_logo_pais'        => $dir_logo_pais,
                            'dir_marca_agua'       => $dir_marca_agua,
                            'consulta4'            => $consulta4,
                            'consulta5'            => $consulta5
                        );

                        $data3 = array(
                            'consulta1' => $consulta1
                        );

                        $style_qrcode = array(
                            'border'        => 0,
                            'vpadding'      => 'auto',
                            'hpadding'      => 'auto',
                            'fgcolor'       => array(0, 0, 0),
                            'bgcolor'       => false, //array(255,255,255)
                            'module_width'  => 1, // width of a single module in points
                            'module_height' => 1 // height of a single module in points
                        );

                    set_time_limit(3600);
                    ini_set('memory_limit','-1');

                    // == HEADER ==
                        PDF::setHeaderCallback(function($pdf) use($data2){
                            $this->utilitarios(array(
                                'tipo'         => '101',
                                'x'            => 7,
                                'y'            => 7,
                                'w'            => 202,
                                'h'            => 126,
                                'style'        => '',
                                'border_style' => array(),
                                'fill_color'   => array()
                            ));

                            $this->utilitarios(array(
                                'tipo'  => '102',
                                'x1'    => 7,
                                'y1'    => 33,
                                'x2'    => 209,
                                'y2'    => 33,
                                'style' => array()
                            ));

                            $this->utilitarios(array(
                                'tipo'      => '100',
                                'file'      => $data2['dir_logo_institucion'],
                                'x'         => 10,
                                'y'         => 10,
                                'w'         => 0,
                                'h'         => 20,
                                'type'      => 'PNG',
                                'link'      => '',
                                'align'     => '',
                                'resize'    => FALSE,
                                'dpi'       => 300,
                                'palign'    => '',
                                'ismask'    => FALSE,
                                'imgsmask'  => FALSE,
                                'border'    => 0,
                                'fitbox'    => FALSE,
                                'hidden'    => FALSE,
                                'fitonpage' => FALSE
                            ));

                            $this->utilitarios(array(
                                'tipo'      => '100',
                                'file'      => $data2['dir_logo_pais'],
                                'x'         => 171,
                                'y'         => 10,
                                'w'         => 0,
                                'h'         => 20,
                                'type'      => 'PNG',
                                'link'      => '',
                                'align'     => '',
                                'resize'    => FALSE,
                                'dpi'       => 300,
                                'palign'    => '',
                                'ismask'    => FALSE,
                                'imgsmask'  => FALSE,
                                'border'    => 0,
                                'fitbox'    => FALSE,
                                'hidden'    => FALSE,
                                'fitonpage' => FALSE
                            ));

                            $this->utilitarios(array(
                                'tipo'      => '100',
                                'file'      => $data2['dir_marca_agua'],
                                'x'         => 63,
                                'y'         => 39,
                                'w'         => 0,
                                'h'         => 90,
                                'type'      => '',
                                'link'      => '',
                                'align'     => '',
                                'resize'    => TRUE,
                                'dpi'       => 140,
                                'palign'    => '',
                                'ismask'    => FALSE,
                                'imgsmask'  => FALSE,
                                'border'    => 0,
                                'fitbox'    => FALSE,
                                'hidden'    => FALSE,
                                'fitonpage' => FALSE
                            ));

                            $pdf->Ln(10);
                            // $pdf->SetFont('times', 'B', 14);
                            // $this->utilitarios(array(
                            //     'tipo'       => '110',
                            //     'h'          => 0,
                            //     'txt'        => 'MINISTERIO PÚBLICO',
                            //     'link'       => '',
                            //     'fill'       => FALSE,
                            //     'align'      => 'C',
                            //     'ln'         => TRUE,
                            //     'stretch'    => 0,
                            //     'firstline'  => FALSE,
                            //     'firstblock' => FALSE,
                            //     'maxh'       => 0
                            // ));

                            $pdf->SetFont('times', 'B', 12);
                            $this->utilitarios(array(
                                'tipo'       => '110',
                                'h'          => 0,
                                'txt'        => $data2['consulta4']['lugar_dependencia_funcionario'],
                                'link'       => '',
                                'fill'       => FALSE,
                                'align'      => 'C',
                                'ln'         => TRUE,
                                'stretch'    => 0,
                                'firstline'  => FALSE,
                                'firstblock' => FALSE,
                                'maxh'       => 0
                            ));

                            $pdf->SetFont('times', '', 10);
                            $this->utilitarios(array(
                                'tipo'       => '110',
                                'h'          => 0,
                                'txt'        => $data2['consulta4']['ud_funcionario'],
                                'link'       => '',
                                'fill'       => FALSE,
                                'align'      => 'C',
                                'ln'         => TRUE,
                                'stretch'    => 0,
                                'firstline'  => FALSE,
                                'firstblock' => FALSE,
                                'maxh'       => 0
                            ));

                            $pdf->Ln(4);

                            $pdf->SetFont('times', 'B', 12);
                            $this->utilitarios(array(
                                'tipo'       => '110',
                                'h'          => 0,
                                'txt'        => $data2['consulta5']['nombre'],
                                'link'       => '',
                                'fill'       => FALSE,
                                'align'      => 'C',
                                'ln'         => TRUE,
                                'stretch'    => 0,
                                'firstline'  => FALSE,
                                'firstblock' => FALSE,
                                'maxh'       => 0
                            ));
                        });

                    // == FOOTER ==
                        PDF::setFooterCallback(function($pdf) use($data3){
                            $y_n = 139.7;
                            // == FIRMAS ==
                                $pdf->SetY(-(43.5 + $y_n));

                                $fill = FALSE;
                                $x1   = 65;
                                $x2   = 66;
                                $y1   = 4;

                                $pdf->SetFont('times', 'B', 7);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "SOLICITANTE",
                                    'border'  => 'LRTB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "INMEDIATO SUPERIOR",
                                    'border'  => 'LRTB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x2,
                                    'y1'      => $y1,
                                    'txt'     => "AUTORIZADO POR R.R.H.H.",
                                    'border'  => 'LRTB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $pdf->Ln();

                                $fill = FALSE;
                                $y1   = 29;

                                $pdf->SetFont('times', 'B', 7);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "",
                                    'border'  => 'LRTB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "",
                                    'border'  => 'LRTB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x2,
                                    'y1'      => $y1,
                                    'txt'     => "",
                                    'border'  => 'LRTB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $pdf->Ln();

                            // == LEYENDA ==
                                $pdf->SetY(-(10.5 + $y_n));

                                $fill = FALSE;
                                $x1   = 98;
                                $y1   = 4;

                                $pdf->SetFont('times', 'I', 7);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "NOTA: El presente formulario no debe tener borrones, enmiendas y/o correcciones.",
                                    'border'  => '',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Fecha de solicitud: " . date("d/m/Y H:i", strtotime($data3['consulta1']['created_at'])),
                                    'border'  => '',
                                    'align'   => 'R',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));
                        });

                    PDF::setPageUnit('mm');

                    PDF::SetMargins(10, 36, 10);
                    PDF::getAliasNbPages();
                    PDF::SetCreator('MINISTERIO PUBLICO');
                    PDF::SetAuthor('TRITON');
                    PDF::SetTitle('PAPELETA DE SALIDA');
                    PDF::SetSubject('DOCUMENTO');
                    PDF::SetKeywords('PAPELETA DE SALIDA');

                    // PDF::SetFontSubsetting(false);

                    PDF::SetAutoPageBreak(TRUE, 10);
                    // PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                    // === BODY ===
                        // PDF::AddPage('L', 'MEMO');
                        PDF::AddPage('P', 'LETTER');

                        // === FUNCIONARIO E INMEDIATO SUPERIOR ===
                            $fill = FALSE;
                            $tt2  = 10;
                            $x1   = 85;
                            $x2   = 26;
                            $y1   = 4;

                            PDF::SetFont('times', 'B', 8);

                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x1,
                                'y1'      => $y1,
                                'txt'     => "Nombre del funcionario:",
                                'border'  => 'LRT',
                                'align'   => 'L',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x1,
                                'y1'      => $y1,
                                'txt'     => "Inmediato superior:",
                                'border'  => 'LRT',
                                'align'   => 'L',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x2,
                                'y1'      => $y1,
                                'txt'     => "",
                                'border'  => 'LRT',
                                'align'   => 'L',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            PDF::Ln();

                            $fill = FALSE;
                            $x1   = 85;
                            $x2   = 26;
                            $y1   = 8;

                            PDF::SetFont('times', '', 9);

                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x1,
                                'y1'      => $y1,
                                'txt'     => $consulta2['n_documento'] . " - " . $consulta2['nombre_persona'] . " " . trim($consulta2['ap_paterno'] . " " . $consulta2['ap_materno']),
                                'border'  => 'LRB',
                                'align'   => 'C',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            $this->utilitarios(array(
                                'tipo'      => '111',
                                'x1'        => $x1,
                                'y1'        => $y1,
                                'txt'       => $consulta3['n_documento'] . " - " . $consulta3['nombre_persona'] . " " . trim($consulta3['ap_paterno'] . " " . $consulta3['ap_materno']),
                                'border'    => 'LRB',
                                'align'     => 'C',
                                'fill'      => $fill,
                                'ln'        => 0,
                                'stretch'   => 0,
                                'ishtml'    => FALSE,
                                'fitcell'   => FALSE,
                                'valign'    => 'M'
                            ));

                            $this->utilitarios(array(
                                'tipo'      => '111',
                                'x1'        => $x2,
                                'y1'        => $y1,
                                'txt'       => "",
                                'border'    => 'LR',
                                'align'     => 'C',
                                'fill'      => $fill,
                                'ln'        => 0,
                                'stretch'   => 0,
                                'ishtml'    => FALSE,
                                'fitcell'   => FALSE,
                                'valign'    => 'M'
                            ));

                            PDF::Ln();

                        if($consulta5['tipo_cronograma'] == '1')
                        {
                            // === FECHA DE SALIDA, HORA DE SALIDA, HORA DE RETORNO Y SU SALIDA ES CON ===
                                $fill = FALSE;
                                $tt2  = 10;
                                $x1   = 42.5;
                                $x2   = 26;
                                $y1   = 4;

                                PDF::SetFont('times', 'B', 8);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Fecha de salida:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Hora de salida:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Hora de retorno:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Su salida es:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x2,
                                    'y1'      => $y1,
                                    'txt'     => "",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                PDF::Ln();

                                $fill = FALSE;
                                $x1   = 42.5;
                                $x2   = 26;
                                $y1   = 8;

                                PDF::SetFont('times', '', 9);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => date("d/m/Y", strtotime($consulta1["f_salida"])),
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => date("H:i", strtotime($consulta1["h_salida"])),
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                if($consulta1["h_retorno"] == '')
                                {
                                    $h_retorno = '';
                                }
                                else
                                {
                                    $h_retorno = date("H:i", strtotime($consulta1["h_retorno"]));
                                }

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => $h_retorno,
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => ($consulta1["con_sin_retorno"] == '')? '' : $this->con_sin_retorno[$consulta1["con_sin_retorno"]],
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'      => '111',
                                    'x1'        => $x2,
                                    'y1'        => $y1,
                                    'txt'       => "",
                                    'border'    => 'LR',
                                    'align'     => 'C',
                                    'fill'      => $fill,
                                    'ln'        => 0,
                                    'stretch'   => 0,
                                    'ishtml'    => FALSE,
                                    'fitcell'   => FALSE,
                                    'valign'    => 'M'
                                ));

                                PDF::Ln();
                        }
                        else
                        {
                            // === FECHA DE SALIDA, PERIODO, FECHA DE RETORNO Y PERIODO ===
                                $fill = FALSE;
                                $tt2  = 10;
                                $x1   = 42.5;
                                $x2   = 26;
                                $y1   = 4;

                                PDF::SetFont('times', 'B', 8);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Fecha de salida:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Periodo:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Fecha de retorno:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Periodo:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x2,
                                    'y1'      => $y1,
                                    'txt'     => "",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                PDF::Ln();

                                $fill = FALSE;
                                $x1   = 42.5;
                                $x2   = 26;
                                $y1   = 8;

                                PDF::SetFont('times', '', 9);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => date("d/m/Y", strtotime($consulta1["f_salida"])),
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => ($consulta1["periodo_salida"] == '')? '' : $this->periodo[$consulta1["periodo_salida"]],
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => date("d/m/Y", strtotime($consulta1["f_retorno"])),
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => ($consulta1["periodo_retorno"] == '')? '' : $this->periodo[$consulta1["periodo_retorno"]],
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'      => '111',
                                    'x1'        => $x2,
                                    'y1'        => $y1,
                                    'txt'       => "",
                                    'border'    => 'LR',
                                    'align'     => 'C',
                                    'fill'      => $fill,
                                    'ln'        => 0,
                                    'stretch'   => 0,
                                    'ishtml'    => FALSE,
                                    'fitcell'   => FALSE,
                                    'valign'    => 'M'
                                ));

                                PDF::Ln();
                        }

                        // === TIPO DE SALIDA, MINUTOS O MINUTOS DE SALIDA Y CODIGO ===
                            $fill = FALSE;
                            $tt2  = 10;
                            $x1   = 85;
                            $x2   = 26;
                            $y1   = 4;

                            PDF::SetFont('times', 'B', 8);

                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x1,
                                'y1'      => $y1,
                                'txt'     => "Tipo de salida:",
                                'border'  => 'LR',
                                'align'   => 'L',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            if($consulta5['tipo_cronograma'] == '1')
                            {
                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Minutos de salida:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));
                            }
                            else
                            {
                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Número de días:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));
                            }

                            if($consulta1['pdf'] == '1')
                            {
                                $txt     = $consulta1['codigo'];
                                $ishtml  = FALSE;
                            }
                            else
                            {
                                $url_pdf = url("storage/rrhh/salidas/solicitud_salida/" . $consulta1['papeleta_pdf']);
                                $txt    = '&nbsp;<a href="' . $url_pdf . '" style="text-decoration: none;" target="_blank">' . $consulta1['codigo'] . "</a>";
                                $ishtml = TRUE;
                            }

                            PDF::SetFont('times', 'B', 9);

                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x2,
                                'y1'      => $y1,
                                'txt'     => $txt,
                                'border'  => 'LRB',
                                'align'   => 'C',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => $ishtml,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            PDF::Ln();

                            $fill = FALSE;
                            $x1   = 85;
                            $x2   = 111;
                            $y1   = 8;

                            PDF::SetFont('times', '', 9);

                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x1,
                                'y1'      => $y1,
                                'txt'     => ($consulta5["tipo_salida"] == '')? '' : $this->tipo_salida[$consulta5["tipo_salida"]],
                                'border'  => 'LRB',
                                'align'   => 'C',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            if($consulta5['tipo_cronograma'] == '1')
                            {
                                $txt = '';

                                if($consulta5['tipo_salida'] == '2')
                                {
                                    $txt = round($consulta1['n_horas'] * 60, 0);
                                }
                            }
                            else
                            {
                                $txt = $consulta1['n_dias'];
                            }

                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x2,
                                'y1'      => $y1,
                                'txt'     => $txt,
                                'border'  => 'LRB',
                                'align'   => 'C',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            PDF::Ln();

                        // === DESTINO ===
                            if($consulta1['destino'] != '')
                            {
                                $fill = FALSE;
                                $tt2  = 10;
                                $x1   = 196;
                                $y1   = 4;

                                PDF::SetFont('times', 'B', 8);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Destino:",
                                    'border'  => 'LRT',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                PDF::Ln();

                                $fill = FALSE;
                                $x1   = 196;
                                $y1   = 8;

                                PDF::SetFont('times', '', 9);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => $consulta1['destino'],
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                PDF::Ln();
                            }

                        // === MOTIVO ===
                            if($consulta1['destino'] != '')
                            {
                                $fill = FALSE;
                                $tt2  = 10;
                                $x1   = 196;
                                $y1   = 4;

                                PDF::SetFont('times', 'B', 8);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Motivo:",
                                    'border'  => 'LRT',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                PDF::Ln();

                                $fill = FALSE;
                                $x1   = 196;
                                $y1   = 8;

                                PDF::SetFont('times', '', 9);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => $consulta1['motivo'],
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                PDF::Ln();
                            }

                        // === CODIGO QR ===
                            $url_reporte = url("solicitud_salida/reportes?tipo=1&salida_id=" . $salida_id);
                            $this->utilitarios(array(
                                'tipo'    => '112',
                                'code'    => $url_reporte,
                                'type'    => 'QRCODE,L',
                                'x'       => 180.5,
                                'y'       => 35.5,
                                'w'       => 25,
                                'h'       => 25,
                                'style'   => $style_qrcode,
                                'align'   => '',
                                'distort' => FALSE
                            ));

                        // PDF::lastPage();

                    PDF::Output('papeleta_salida_' . date("YmdHis") . '.pdf', 'I');;
                }
                else
                {
                    return "La BOLETA DE SALIDA no existe";
                }
                break;

            case '10':
                // === SEGURIDAD ===
                    $this->rol_id   = Auth::user()->rol_id;
                    $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                                        ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                                        ->select("seg_permisos.codigo")
                                        ->get()
                                        ->toArray();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();

                // === PERMISOS ===
                    if(!in_array(['codigo' => '1307'], $this->permisos))
                    {
                        return "No tiene permiso para GENERAR REPORTES.";
                    }

                // === ANALISIS DE LAS VARIABLES ===
                    if( ! (($request->has('fecha_del') && $request->has('fecha_al'))))
                    {
                        return "La FECHA DEL y FECHA AL son obligatorios.";
                    }

                //=== CARGAR VARIABLES ===
                    $data1['fecha_del']                        = trim($request->input('fecha_del'));
                    $data1['fecha_al']                         = trim($request->input('fecha_al'));
                    $data1['persona_id']                       = trim($request->input('persona_id'));
                    $data1['lugar_dependencia_id_funcionario'] = trim($request->input('lugar_dependencia_id_funcionario'));

                //=== CONSULTA BASE DE DATOS ===
                    $tabla1 = "rrhh_asistencias";
                    $tabla2 = "rrhh_personas";
                    $tabla3 = "inst_unidades_desconcentradas";
                    $tabla4 = "inst_lugares_dependencia";

                    $array_where = "$tabla1.estado <> '2'";
                    if($request->has('fecha_del'))
                    {
                        $array_where .= " AND $tabla1.fecha >= '" . $data1['fecha_del'] . "'";
                    }

                    if($request->has('fecha_al'))
                    {
                        $array_where .= " AND $tabla1.fecha <= '" . $data1['fecha_al'] . "'";
                    }

                    if($request->has('lugar_dependencia_id_funcionario'))
                    {
                        $array_where .= " AND a3.lugar_dependencia_id=" . $data1['lugar_dependencia_id_funcionario'];
                    }

                    if($request->has('persona_id'))
                    {
                        $array_where .= " AND $tabla1.persona_id=" . $data1['persona_id'];
                    }

                    $select = "
                        $tabla1.id,
                        $tabla1.persona_id,

                        $tabla1.persona_id_rrhh_h1_i,
                        $tabla1.persona_id_rrhh_h1_s,
                        $tabla1.persona_id_rrhh_h2_i,
                        $tabla1.persona_id_rrhh_h2_s,

                        $tabla1.cargo_id,
                        $tabla1.unidad_desconcentrada_id,

                        $tabla1.log_marcaciones_id_i1,
                        $tabla1.log_marcaciones_id_s1,
                        $tabla1.log_marcaciones_id_i2,
                        $tabla1.log_marcaciones_id_s2,

                        $tabla1.horario_id_1,
                        $tabla1.horario_id_2,

                        $tabla1.salida_id_i1,
                        $tabla1.salida_id_s1,
                        $tabla1.salida_id_i2,
                        $tabla1.salida_id_s2,

                        $tabla1.fthc_id_h1,
                        $tabla1.fthc_id_h2,

                        $tabla1.estado,
                        $tabla1.fecha,

                        $tabla1.h1_i_omitir,
                        $tabla1.h1_s_omitir,
                        $tabla1.h2_i_omitir,
                        $tabla1.h2_s_omitir,

                        $tabla1.h1_min_retrasos,
                        $tabla1.h2_min_retrasos,

                        $tabla1.h1_descuento,
                        $tabla1.h2_descuento,

                        $tabla1.h1_i_omision_registro,
                        $tabla1.h1_s_omision_registro,
                        $tabla1.h2_i_omision_registro,
                        $tabla1.h2_s_omision_registro,

                        $tabla1.f_omision_registro,
                        $tabla1.e_omision_registro,

                        $tabla1.h1_falta,
                        $tabla1.h2_falta,

                        $tabla1.observaciones,
                        $tabla1.justificacion,

                        $tabla1.horario_1_i,
                        $tabla1.horario_1_s,

                        $tabla1.horario_2_i,
                        $tabla1.horario_2_s,

                        a2.n_documento,
                        a2.nombre AS nombre_persona,
                        a2.ap_paterno,
                        a2.ap_materno,

                        a3.lugar_dependencia_id AS lugar_dependencia_id_funcionario,
                        a3.nombre AS ud_funcionario,

                        a4.nombre AS lugar_dependencia_funcionario
                    ";

                    $consulta1 = RrhhAsistencia::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.persona_id")
                        ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.unidad_desconcentrada_id")
                        ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                        ->whereRaw($array_where)
                        ->select(DB::raw($select))
                        ->orderByRaw("a2.ap_paterno ASC, a2.ap_materno ASC, a2.nombre ASC, $tabla1.fecha")
                        ->get()
                        ->toArray();

                //=== EXCEL ===
                    if(count($consulta1) > 0)
                    {
                        set_time_limit(3600);
                        ini_set('memory_limit','-1');
                        Excel::create('resumen_asistencia_' . date('Y-m-d_H-i-s'), function($excel) use($consulta1){
                            $excel->sheet('Resumen Asistencias', function($sheet) use($consulta1){
                                $sheet->row(1, [
                                    'No',
                                    'CI',
                                    'NOMBRE COMPLETO',


                                    'DIAS TRABAJADOS',
                                    'FERIADOS',
                                    'VACACIONES',
                                    'LICENCIA CON GOCE DE HABER',
                                    'LICENCIA SIN GOCE DE HABER',
                                    'FALTAS',
                                    'TOTAL DIAS',

                                    'DIAS DESCUENTO',


                                    'HORARIO 1 INGRESOS MARCADOS',
                                    'HORARIO 1 INGRESOS NO MARCADOS',
                                    'HORARIO 1 SALIDAS MARCADAS',
                                    'HORARIO 1 SALIDAS NO MARCADAS',

                                    'HORARIO 2 INGRESOS MARCADOS',
                                    'HORARIO 2 INGRESOS NO MARCADOS',
                                    'HORARIO 2 SALIDAS MARCADAS',
                                    'HORARIO 2 SALIDAS NO MARCADAS',

                                    'SALIDA PARTICULAR SALIDA NO MARCADAS',
                                    'SALIDA PARTICULAR RETORNO NO MARCADOS',

                                    'TOTAL NO MARCADAS',

                                    'DIAS DESCUENTO',


                                    'TOTAL ATRASOS',

                                    'DIAS DESCUENTO',


                                    'TOTAL DIAS DESCUENTO'
                                ]);

                                $sheet->row(1, function($row){
                                    $row->setBackground('#CCCCCC');
                                    $row->setFontWeight('bold');
                                    $row->setAlignment('center');
                                });

                                $sheet->freezeFirstRow();
                                $sheet->setAutoFilter();

                                $sheet->getStyle("D1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("E1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("F1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("G1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("H1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("I1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("J1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("K1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("L1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("M1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("N1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("O1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("P1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("Q1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("R1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("S1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("T1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("U1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("V1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("W1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("X1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("Y1")->getAlignment()->setTextRotation(90);
                                $sheet->getStyle("Z1")->getAlignment()->setTextRotation(90);

                                // $sheet->setColumnFormat([
                                //     'A' => 'yyyy-mm-dd hh:mm:ss'
                                // ]);

                                $sw         = FALSE;
                                $c          = 1;
                                $persona_id = 0;
                                $sw_calculo = FALSE;

                                $n_documento    = "";
                                $nombre_persona = "";

                                $dias_trabajados         = 0;
                                $feriados                = 0;
                                $vacaciones              = 0;
                                $licencia_con_goce_haber = 0;
                                $licencia_sin_goce_haber = 0;
                                $faltas                  = 0;
                                $total_dias              = 0;
                                $dias_descuento_1        = 0;

                                $h1_ingresos_marcados    = 0;
                                $h1_ingresos_no_marcados = 0;
                                $h1_salidas_marcados     = 0;
                                $h1_salidas_no_marcados  = 0;
                                $h2_ingresos_marcados    = 0;
                                $h2_ingresos_no_marcados = 0;
                                $h2_salidas_marcados     = 0;
                                $h2_salidas_no_marcados  = 0;
                                $pph_salida_no_marcada   = 0;
                                $pph_retorno_no_marcada  = 0;
                                $dias_descuento_2        = 0;

                                $total_atrasos    = 0;
                                $dias_descuento_3 = 0;

                                $total_dias_descuento = 0;

                                foreach($consulta1 as $index1 => $row1)
                                {
                                    if($row1['persona_id'] != $persona_id)
                                    {
                                        if($sw_calculo)
                                        {
                                            // === FORMULA DIAS DESCUENTO ===
                                                $dias_descuento_1 = $licencia_sin_goce_haber + $faltas * 2;

                                                $dias_descuento_2 = ($h1_ingresos_no_marcados + $h1_salidas_no_marcados + $h2_ingresos_no_marcados + $h2_salidas_no_marcados + $pph_salida_no_marcada + $pph_retorno_no_marcada) * 0.5;

                                                if($total_atrasos < 21)
                                                {
                                                    $dias_descuento_3 = 0;
                                                }
                                                elseif($total_atrasos < 31)
                                                {
                                                    $dias_descuento_3 = 0.5;
                                                }
                                                elseif($total_atrasos < 51)
                                                {
                                                    $dias_descuento_3 = 1;
                                                }
                                                elseif($total_atrasos < 71)
                                                {
                                                    $dias_descuento_3 = 2;
                                                }
                                                elseif($total_atrasos < 91)
                                                {
                                                    $dias_descuento_3 = 3;
                                                }
                                                elseif($total_atrasos < 121)
                                                {
                                                    $dias_descuento_3 = 4;
                                                }
                                                else
                                                {
                                                    $dias_descuento_3 = 5;
                                                }

                                                $total_dias_descuento = $dias_descuento_1 + $dias_descuento_2 + $dias_descuento_3;

                                            $sheet->row($c+1, [
                                                $c++,
                                                $n_documento,
                                                $nombre_persona,

                                                $dias_trabajados,
                                                $feriados,
                                                $vacaciones,
                                                $licencia_con_goce_haber,
                                                $licencia_sin_goce_haber,
                                                $faltas,
                                                $total_dias,

                                                $dias_descuento_1,


                                                $h1_ingresos_marcados,
                                                $h1_ingresos_no_marcados,
                                                $h1_salidas_marcados,
                                                $h1_salidas_no_marcados,

                                                $h2_ingresos_marcados,
                                                $h2_ingresos_no_marcados,
                                                $h2_salidas_marcados,
                                                $h2_salidas_no_marcados,

                                                $pph_salida_no_marcada,
                                                $pph_retorno_no_marcada,

                                                ($h1_ingresos_no_marcados + $h1_salidas_no_marcados + $h2_ingresos_no_marcados + $h2_salidas_no_marcados + $pph_salida_no_marcada + $pph_retorno_no_marcada),

                                                $dias_descuento_2,


                                                $total_atrasos,

                                                $dias_descuento_3,


                                                $total_dias_descuento
                                            ]);

                                            if($sw)
                                            {
                                                $sheet->row($c, function($row){
                                                    $row->setBackground('#deeaf6');
                                                });

                                                $sw = FALSE;
                                            }
                                            else
                                            {
                                                $sw = TRUE;
                                            }

                                            $dias_trabajados         = 0;
                                            $feriados                = 0;
                                            $vacaciones              = 0;
                                            $licencia_con_goce_haber = 0;
                                            $licencia_sin_goce_haber = 0;
                                            $faltas                  = 0;
                                            $total_dias              = 0;
                                            $dias_descuento_1        = 0;

                                            $h1_ingresos_marcados    = 0;
                                            $h1_ingresos_no_marcados = 0;
                                            $h1_salidas_marcados     = 0;
                                            $h1_salidas_no_marcados  = 0;
                                            $h2_ingresos_marcados    = 0;
                                            $h2_ingresos_no_marcados = 0;
                                            $h2_salidas_marcados     = 0;
                                            $h2_salidas_no_marcados  = 0;
                                            $pph_salida_no_marcada   = 0;
                                            $pph_retorno_no_marcada  = 0;
                                            $dias_descuento_2        = 0;

                                            $total_atrasos    = 0;
                                            $dias_descuento_3 = 0;

                                            $total_dias_descuento = 0;
                                        }
                                        else
                                        {
                                            $sw_calculo = TRUE;
                                        }

                                        $persona_id = $row1['persona_id'];
                                    }

                                    $n_documento    = $row1["n_documento"];
                                    $nombre_persona = trim($row1["ap_paterno"] . " " . $row1["ap_materno"]) . " " . trim($row1["nombre_persona"]);

                                    // === DIAS ===
                                        switch($row1["horario_1_i"])
                                        {
                                            case $this->fthc['1']:
                                                $feriados += 0.5;
                                                break;
                                            case $this->omitir['2']:
                                                $vacaciones += 0.5;
                                                if($row1["horario_2_i"] == $this->fthc['3'])
                                                {
                                                    $vacaciones += 0.5;
                                                }
                                                break;
                                            case $this->tipo_salida['1']:
                                                if($row1["horario_1_s"] == $this->tipo_salida['1'])
                                                {
                                                    $licencia_con_goce_haber += 0.5;
                                                }
                                                else
                                                {
                                                    $dias_trabajados += 0.5;
                                                }
                                                break;
                                            case $this->tipo_salida['4']:
                                                $licencia_con_goce_haber += 0.5;
                                                break;
                                            case $this->tipo_salida['5']:
                                                $licencia_sin_goce_haber += 0.5;
                                                break;
                                            case $this->falta['1']:
                                                $faltas += 0.5;
                                                break;
                                            default:
                                                $dias_trabajados += 0.5;
                                                break;
                                        }

                                        if($row1["horario_2_i"] != $this->falta['4'])
                                        {
                                            switch($row1["horario_2_i"])
                                            {
                                                case $this->fthc['1']:
                                                    $feriados += 0.5;
                                                    break;
                                                case $this->fthc['3']:
                                                    if( ! ($row1["horario_1_i"] == $this->omitir['2']))
                                                    {
                                                        $dias_trabajados += 0.5;
                                                    }
                                                    break;
                                                case $this->omitir['2']:
                                                    $vacaciones += 0.5;
                                                    break;
                                                case $this->tipo_salida['1']:
                                                    if($row1["horario_2_s"] == $this->tipo_salida['1'])
                                                    {
                                                        $licencia_con_goce_haber += 0.5;
                                                    }
                                                    else
                                                    {
                                                        $dias_trabajados += 0.5;
                                                    }
                                                    break;
                                                case $this->tipo_salida['4']:
                                                    $licencia_con_goce_haber += 0.5;
                                                    break;
                                                case $this->tipo_salida['5']:
                                                    $licencia_sin_goce_haber += 0.5;
                                                    break;
                                                case $this->falta['1']:
                                                    $faltas += 0.5;
                                                    break;
                                                case $this->falta['4']:
                                                    break;
                                                default:
                                                    $dias_trabajados += 0.5;
                                                    break;
                                            }
                                        }

                                    // === MARCADOS / NO MARCADOS ===
                                        switch($row1["horario_1_i"])
                                        {
                                            case $this->omision['1']:
                                                $h1_ingresos_no_marcados ++;
                                                break;
                                            default:
                                                $h1_ingresos_marcados ++;
                                                break;
                                        }

                                        switch($row1["horario_1_s"])
                                        {
                                            case $this->omision['1']:
                                                $h1_salidas_no_marcados ++;
                                                break;
                                            default:
                                                $h1_salidas_marcados ++;
                                                break;
                                        }

                                        if($row1["horario_2_i"] != $this->falta['4'])
                                        {
                                            switch($row1["horario_2_i"])
                                            {
                                                case $this->omision['1']:
                                                    $h2_ingresos_no_marcados ++;
                                                    break;
                                                default:
                                                    $h2_ingresos_marcados ++;
                                                    break;
                                            }

                                            switch($row1["horario_2_s"])
                                            {
                                                case $this->omision['1']:
                                                    $h2_salidas_no_marcados ++;
                                                    break;
                                                default:
                                                    $h2_salidas_marcados ++;
                                                    break;
                                            }
                                        }

                                        // === FALTA INCLUIR ===
                                            // $pph_salida_no_marcada   = 0;
                                            // $pph_retorno_no_marcada  = 0;

                                    //=== ATRASOS ===
                                        $total_atrasos += $row1["h1_min_retrasos"] + $row1["h2_min_retrasos"];

                                    if($row1["horario_2_i"] == $this->falta['4'])
                                    {
                                        $total_dias += 0.5;
                                    }
                                    else
                                    {
                                        $total_dias++;
                                    }
                                }

                                // === ULTIMO FUNCIONARIO ===
                                    // === FORMULA DIAS DESCUENTO ===
                                        $dias_descuento_1 = $licencia_sin_goce_haber + $faltas * 2;

                                        $dias_descuento_2 = ($h1_ingresos_no_marcados + $h1_salidas_no_marcados + $h2_ingresos_no_marcados + $h2_salidas_no_marcados + $pph_salida_no_marcada + $pph_retorno_no_marcada) * 0.5;

                                        if($total_atrasos < 21)
                                        {
                                            $dias_descuento_3 = 0;
                                        }
                                        elseif($total_atrasos < 31)
                                        {
                                            $dias_descuento_3 = 0.5;
                                        }
                                        elseif($total_atrasos < 51)
                                        {
                                            $dias_descuento_3 = 1;
                                        }
                                        elseif($total_atrasos < 71)
                                        {
                                            $dias_descuento_3 = 2;
                                        }
                                        elseif($total_atrasos < 91)
                                        {
                                            $dias_descuento_3 = 3;
                                        }
                                        elseif($total_atrasos < 121)
                                        {
                                            $dias_descuento_3 = 4;
                                        }
                                        else
                                        {
                                            $dias_descuento_3 = 5;
                                        }

                                        $total_dias_descuento = $dias_descuento_1 + $dias_descuento_2 + $dias_descuento_3;

                                    $sheet->row($c+1, [
                                        $c++,
                                        $n_documento,
                                        $nombre_persona,

                                        $dias_trabajados,
                                        $feriados,
                                        $vacaciones,
                                        $licencia_con_goce_haber,
                                        $licencia_sin_goce_haber,
                                        $faltas,
                                        $total_dias,

                                        $dias_descuento_1,


                                        $h1_ingresos_marcados,
                                        $h1_ingresos_no_marcados,
                                        $h1_salidas_marcados,
                                        $h1_salidas_no_marcados,

                                        $h2_ingresos_marcados,
                                        $h2_ingresos_no_marcados,
                                        $h2_salidas_marcados,
                                        $h2_salidas_no_marcados,


                                        $pph_salida_no_marcada,
                                        $pph_retorno_no_marcada,

                                        ($h1_ingresos_no_marcados + $h1_salidas_no_marcados + $h2_ingresos_no_marcados + $h2_salidas_no_marcados + $pph_salida_no_marcada + $pph_retorno_no_marcada),

                                        $dias_descuento_2,


                                        $total_atrasos,

                                        $dias_descuento_3,


                                        $total_dias_descuento
                                    ]);

                                    if($sw)
                                    {
                                        $sheet->row($c, function($row){
                                            $row->setBackground('#deeaf6');
                                        });

                                        $sw = FALSE;
                                    }
                                    else
                                    {
                                        $sw = TRUE;
                                    }

                                // $sheet->cells('B1:D' . ($c), function($cells){
                                //     $cells->setAlignment('center');
                                // });

                                $sheet->cells('A2:B' . ($c), function($cells){
                                    $cells->setAlignment('right');
                                });

                                $sheet->setAutoSize(true);
                            });
                        })->export('xlsx');
                    }
                    else
                    {
                        return "No se encontraron resultados.";
                    }
                break;
            case '11':
                // === SEGURIDAD ===
                    $this->rol_id   = Auth::user()->rol_id;
                    $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                                        ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                                        ->select("seg_permisos.codigo")
                                        ->get()
                                        ->toArray();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();

                // === PERMISOS ===
                    if(!in_array(['codigo' => '1307'], $this->permisos))
                    {
                        return "No tiene permiso para GENERAR REPORTES.";
                    }

                // === ANALISIS DE LAS VARIABLES ===
                    if( ! (($request->has('fecha_del') && $request->has('fecha_al'))))
                    {
                        return "La FECHA DEL y FECHA AL son obligatorios.";
                    }

                //=== CARGAR VARIABLES ===
                    $data1['fecha_del']                        = trim($request->input('fecha_del'));
                    $data1['fecha_al']                         = trim($request->input('fecha_al'));
                    $data1['persona_id']                       = trim($request->input('persona_id'));
                    $data1['lugar_dependencia_id_funcionario'] = trim($request->input('lugar_dependencia_id_funcionario'));

                //=== CONSULTA BASE DE DATOS ===
                    $tabla1 = "rrhh_asistencias";
                    $tabla2 = "rrhh_personas";
                    $tabla3 = "inst_unidades_desconcentradas";
                    $tabla4 = "inst_lugares_dependencia";

                    $array_where = "$tabla1.estado <> '2'";
                    if($request->has('fecha_del'))
                    {
                        $array_where .= " AND $tabla1.fecha >= '" . $data1['fecha_del'] . "'";
                    }

                    if($request->has('fecha_al'))
                    {
                        $array_where .= " AND $tabla1.fecha <= '" . $data1['fecha_al'] . "'";
                    }

                    if($request->has('lugar_dependencia_id_funcionario'))
                    {
                        $array_where .= " AND a3.lugar_dependencia_id=" . $data1['lugar_dependencia_id_funcionario'];
                    }

                    if($request->has('persona_id'))
                    {
                        $array_where .= " AND $tabla1.persona_id=" . $data1['persona_id'];
                    }

                    $select = "
                        $tabla1.id,
                        $tabla1.persona_id,

                        $tabla1.persona_id_rrhh_h1_i,
                        $tabla1.persona_id_rrhh_h1_s,
                        $tabla1.persona_id_rrhh_h2_i,
                        $tabla1.persona_id_rrhh_h2_s,

                        $tabla1.cargo_id,
                        $tabla1.unidad_desconcentrada_id,

                        $tabla1.log_marcaciones_id_i1,
                        $tabla1.log_marcaciones_id_s1,
                        $tabla1.log_marcaciones_id_i2,
                        $tabla1.log_marcaciones_id_s2,

                        $tabla1.horario_id_1,
                        $tabla1.horario_id_2,

                        $tabla1.salida_id_i1,
                        $tabla1.salida_id_s1,
                        $tabla1.salida_id_i2,
                        $tabla1.salida_id_s2,

                        $tabla1.fthc_id_h1,
                        $tabla1.fthc_id_h2,

                        $tabla1.estado,
                        $tabla1.fecha,

                        $tabla1.h1_i_omitir,
                        $tabla1.h1_s_omitir,
                        $tabla1.h2_i_omitir,
                        $tabla1.h2_s_omitir,

                        $tabla1.h1_min_retrasos,
                        $tabla1.h2_min_retrasos,

                        $tabla1.h1_descuento,
                        $tabla1.h2_descuento,

                        $tabla1.h1_i_omision_registro,
                        $tabla1.h1_s_omision_registro,
                        $tabla1.h2_i_omision_registro,
                        $tabla1.h2_s_omision_registro,

                        $tabla1.f_omision_registro,
                        $tabla1.e_omision_registro,

                        $tabla1.h1_falta,
                        $tabla1.h2_falta,

                        $tabla1.observaciones,
                        $tabla1.justificacion,

                        $tabla1.horario_1_i,
                        $tabla1.horario_1_s,

                        $tabla1.horario_2_i,
                        $tabla1.horario_2_s,

                        a2.n_documento,
                        a2.nombre AS nombre_persona,
                        a2.ap_paterno,
                        a2.ap_materno,

                        a3.lugar_dependencia_id AS lugar_dependencia_id_funcionario,
                        a3.nombre AS ud_funcionario,

                        a4.nombre AS lugar_dependencia_funcionario
                    ";

                    $consulta1 = RrhhAsistencia::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.persona_id")
                        ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.unidad_desconcentrada_id")
                        ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                        ->whereRaw($array_where)
                        ->select(DB::raw($select))
                        ->orderByRaw("$tabla1.fecha ASC, a2.ap_paterno ASC, a2.ap_materno ASC, a2.nombre ASC")
                        ->get()
                        ->toArray();

                //=== EXCEL ===
                    if(count($consulta1) > 0)
                    {
                        set_time_limit(3600);
                        ini_set('memory_limit','-1');
                        Excel::create('asistencia_' . date('Y-m-d_H-i-s'), function($excel) use($consulta1){
                            $excel->sheet('Resumen Asistencias', function($sheet) use($consulta1){
                                $sheet->row(1, [
                                    'No',
                                    'FECHA',
                                    'CI',
                                    'NOMBRE COMPLETO',

                                    'HORARIO 1 INGRESO',
                                    'HORARIO 1 SALIDA',
                                    'HORARIO 1 RETRASO',

                                    'HORARIO 2 INGRESO',
                                    'HORARIO 2 SALIDA',
                                    'HORARIO 2 RETRASO',

                                    'UNIDAD DESCONCENTRADA',
                                    'LUGAR DE DEPENDENCIA'
                                ]);

                                $sheet->row(1, function($row){
                                    $row->setBackground('#CCCCCC');
                                    $row->setFontWeight('bold');
                                    $row->setAlignment('center');
                                });

                                $sheet->freezeFirstRow();
                                $sheet->setAutoFilter();

                                // $sheet->setColumnFormat([
                                //     'A' => 'yyyy-mm-dd hh:mm:ss'
                                // ]);

                                $sw = FALSE;
                                $c  = 1;

                                foreach($consulta1 as $index1 => $row1)
                                {
                                    $n_documento    = $row1["n_documento"];
                                    $nombre_persona = trim($row1["ap_paterno"] . " " . $row1["ap_materno"]) . " " . trim($row1["nombre_persona"]);
                                    $sheet->row($c+1, [
                                        $c++,
                                        $row1["fecha"],
                                        $n_documento,
                                        $nombre_persona,

                                        $row1["horario_1_i"],
                                        $row1["horario_1_s"],
                                        $row1["h1_min_retrasos"],

                                        $row1["horario_2_i"],
                                        $row1["horario_2_s"],
                                        $row1["h2_min_retrasos"],

                                        $row1["ud_funcionario"],
                                        $row1["lugar_dependencia_funcionario"]
                                    ]);

                                    if($sw)
                                    {
                                        $sheet->row($c, function($row){
                                            $row->setBackground('#deeaf6');
                                        });

                                        $sw = FALSE;
                                    }
                                    else
                                    {
                                        $sw = TRUE;
                                    }
                                }

                                $sheet->cells('A2:A' . ($c), function($cells){
                                    $cells->setAlignment('right');
                                });

                                $sheet->cells('B1:B' . ($c), function($cells){
                                    $cells->setAlignment('center');
                                });

                                $sheet->cells('C2:C' . ($c), function($cells){
                                    $cells->setAlignment('right');
                                });

                                $sheet->cells('E1:L' . ($c), function($cells){
                                    $cells->setAlignment('center');
                                });


                                $sheet->setAutoSize(true);
                            });
                        })->export('xlsx');
                    }
                    else
                    {
                        return "No se encontraron resultados.";
                    }
                break;
            default:
                break;
        }
    }
}