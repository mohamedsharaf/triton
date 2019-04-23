<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Intervention\Image\Facades\Image;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;
use App\Libraries\UtilClass;

use App\Models\Seguridad\SegPermisoRol;

use App\Models\UbicacionGeografica\UbgeMunicipio;

use App\Models\Institucion\InstLugarDependencia;

use App\Models\Rrhh\RrhhPersona;
use App\Models\Rrhh\RrhhFuncionario;
use App\Models\Rrhh\RrhhFthc;
use App\Models\Rrhh\RrhhTipoSalida;
use App\Models\Rrhh\RrhhSalida;
use App\Models\Rrhh\RrhhAsistencia;
use App\Models\Rrhh\RrhhLogMarcacion;

use App\User;

use PDF;

use Exception;

class HomeController extends Controller
{
    private $estado_civil;
    private $sexo;
    private $estado;
    private $omision;
    private $falta;
    private $fthc;

    private $rol_id;
    private $permisos;

    private $public_dir;
    private $public_url;

    public function __construct()
    {
        $this->middleware('auth');

        $this->estado_civil = [
            '1' => 'CASADO(A)',
            '2' => 'DIVORCIADO(A)',
            '3' => 'SOLTERO(A)',
            '4' => 'UNION LIBRE',
            '5' => 'VIUDO(A)'
        ];

        $this->sexo = [
            'F' => 'FEMENINO',
            'M' => 'MASCULINO'
        ];

        $this->public_dir = '/storage/seguridad/user/image';
        $this->public_url = 'storage/seguridad/user/image/';

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
            '3' => 'REGULARIZADA'
        ];

        $this->fthc = [
            '1' => 'FERIADO',
            '2' => 'TOLERANCIA',
            '3' => 'HORARIO CONTINUO'
        ];

        $this->omitir = [
            '1' => 'MIGRACION',
            '2' => 'VACACIONES'
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

        $this->sp_estado = [
            '1' => 'NO MARCADO',
            '2' => 'SALIDA IGUAL AL HORARIO DE INGRESO',
            '3' => 'RETORNO IGUAL AL HORARIO DE SALIDA'
        ];

        $this->public_dir_1 = '/image/logo';
        $this->public_url_1 = 'storage/rrhh/salidas/solicitud_salida/';
    }

    public function index()
    {
        $this->rol_id   = Auth::user()->rol_id;
        $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                            ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                            ->select("seg_permisos.codigo")
                            ->get()
                            ->toArray();

        $id = Auth::user()->id;

        $tabla1 = "users";
        $tabla3 = "seg_roles";

        $select = "
            $tabla1.id,
            $tabla1.rol_id,
            $tabla1.persona_id,
            $tabla1.estado,
            $tabla1.name,
            $tabla1.imagen,
            $tabla1.email,

            a3.nombre AS rol
        ";

        $usuario_array = User::leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.rol_id")
                            ->where("$tabla1.id", "=", $id)
                            ->select(DB::raw($select))
                            ->first();

        if(($usuario_array['persona_id'] == NULL) || ($usuario_array['persona_id'] == ''))
        {
            $persona_array    = [];
            $persona_array_sw = FALSE;
        }
        else
        {
            $tabla1 = "rrhh_personas";
            $tabla2 = "ubge_municipios";
            $tabla3 = "ubge_provincias";
            $tabla4 = "ubge_departamentos";

            $select = "
                $tabla1.id,
                $tabla1.municipio_id_nacimiento,
                $tabla1.municipio_id_residencia,
                $tabla1.estado,
                $tabla1.n_documento,
                $tabla1.nombre,
                $tabla1.ap_paterno,
                $tabla1.ap_materno,
                $tabla1.ap_esposo,
                $tabla1.sexo,
                $tabla1.f_nacimiento,
                $tabla1.estado_civil,
                $tabla1.domicilio,
                $tabla1.telefono,
                $tabla1.celular,
                $tabla1.estado_segip,

                a2.nombre AS municipio_nacimiento,
                a2.provincia_id AS provincia_id_nacimiento,

                a3.nombre AS provincia_nacimiento,
                a3.departamento_id AS departamento_id_nacimiento,

                a4.nombre AS departamento_nacimiento,

                a5.nombre AS municipio_residencia,
                a5.provincia_id AS provincia_id_residencia,

                a6.nombre AS provincia_residencia,
                a6.departamento_id AS departamento_id_residencia,

                a7.nombre AS departamento_residencia
            ";
            $persona_array = RrhhPersona::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.municipio_id_nacimiento")
                ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.provincia_id")
                ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.departamento_id")
                ->leftJoin("$tabla2 AS a5", "a5.id", "=", "$tabla1.municipio_id_residencia")
                ->leftJoin("$tabla3 AS a6", "a6.id", "=", "a5.provincia_id")
                ->leftJoin("$tabla4 AS a7", "a7.id", "=", "a6.departamento_id")
                ->where("$tabla1.id", "=", $usuario_array['persona_id'])
                ->select(DB::raw($select))
                ->first();

            $persona_array_sw = TRUE;
        }

        $asistencia_array = RrhhAsistencia::where("persona_id", "=", $usuario_array['persona_id'])
            ->count();

        $sw_asistencia = FALSE;
        if($asistencia_array > 0)
        {
            $sw_asistencia = TRUE;
        }

        $sw_horario               = FALSE;
        $funcioario_horario_array = [];
        if($usuario_array['persona_id'] != NULL)
        {
            $tabla1 = "rrhh_funcionarios";
            $tabla2 = "rrhh_horarios";

            $select = "
                $tabla1.id,

                a2.nombre AS horario_1,
                a2.h_ingreso AS h_ingreso_1,
                a2.h_salida AS h_salida_1,
                a2.tolerancia AS tolerancia_1,
                a2.marcacion_ingreso_del AS marcacion_ingreso_del_1,
                a2.marcacion_ingreso_al AS marcacion_ingreso_al_1,
                a2.marcacion_salida_del AS marcacion_salida_del_1,
                a2.marcacion_salida_al AS marcacion_salida_al_1,

                a3.nombre AS horario_2,
                a3.h_ingreso AS h_ingreso_2,
                a3.h_salida AS h_salida_2,
                a3.tolerancia AS tolerancia_2,
                a3.marcacion_ingreso_del AS marcacion_ingreso_del_2,
                a3.marcacion_ingreso_al AS marcacion_ingreso_al_2,
                a3.marcacion_salida_del AS marcacion_salida_del_2,
                a3.marcacion_salida_al AS marcacion_salida_al_2
            ";

            $funcioario_horario_array = RrhhFuncionario::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.horario_id_1")
                ->leftJoin("$tabla2 AS a3", "a3.id", "=", "$tabla1.horario_id_2")
                ->where("persona_id", "=", $usuario_array['persona_id'])
                ->select(DB::raw($select))
                ->first();

            if($funcioario_horario_array)
            {
                $sw_horario = TRUE;
            }
        }

        $data = array(
            'rol_id'                   => $this->rol_id,
            'permisos'                 => $this->permisos,
            'title'                    => 'Inicio',
            'home'                     => 'Inicio',
            'sistema'                  => 'Recursos Humanos',
            'modulo'                   => 'Mi perfil',
            'title_table'              => 'Mis asistencias',
            'title_table_2'            => 'Mis papeletas particulares',
            'estado_civil_array'       => $this->estado_civil,
            'sexo_array'               => $this->sexo,
            'usuario_array'            => $usuario_array,
            'persona_array'            => $persona_array,
            'persona_array_sw'         => $persona_array_sw,
            'sw_asistencia'            => $sw_asistencia,
            'public_url'               => $this->public_url,
            'estado_array'             => $this->estado,
            'omision_array'            => $this->omision,
            'falta_array'              => $this->falta,
            'sw_horario'               => $sw_horario,
            'funcioario_horario_array' => $funcioario_horario_array,
            'lugar_dependencia_array'  => InstLugarDependencia::select("id", "nombre")
                                            ->orderBy("nombre")
                                            ->get()
                                            ->toArray()
        );
        return view('home')->with($data);
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

                    a3.lugar_dependencia_id AS lugar_dependencia_id_funcionario,
                    a3.nombre AS ud_funcionario,

                    a4.nombre AS lugar_dependencia_funcionario
                ";

                $persona_id = Auth::user()->persona_id;
                $array_where = "$tabla1.persona_id=" . $persona_id . " AND $tabla1.fecha <= '" . date('Y-m-d') . "'";

                $count = RrhhAsistencia::leftJoin("$tabla5 AS a3", "a3.id", "=", "$tabla1.unidad_desconcentrada_id")
                    ->leftJoin("$tabla6 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = RrhhAsistencia::leftJoin("$tabla5 AS a3", "a3.id", "=", "$tabla1.unidad_desconcentrada_id")
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

                        $this->utilitarios(array('tipo' => '3', 'horario' => $row["horario_1_i"], 'log_marcaciones_id' => $row["log_marcaciones_id_i1"], 'salida_id' => $row["salida_id_i1"], 'fthc_id' => $row["fthc_id_h1"], 'id' => $row["id"], 'fecha' => $row["fecha"], 'persona_id' => $row["persona_id"])),
                        $this->utilitarios(array('tipo' => '3', 'horario' => $row["horario_1_s"], 'log_marcaciones_id' => $row["log_marcaciones_id_s1"], 'salida_id' => $row["salida_id_s1"], 'fthc_id' => $row["fthc_id_h1"], 'id' => $row["id"], 'fecha' => $row["fecha"], 'persona_id' => $row["persona_id"])),
                        $this->utilitarios(array('tipo' => '2', 'min_retrasos' => $row["h1_min_retrasos"])),

                        $this->utilitarios(array('tipo' => '3', 'horario' => $row["horario_2_i"], 'log_marcaciones_id' => $row["log_marcaciones_id_i2"], 'salida_id' => $row["salida_id_i2"], 'fthc_id' => $row["fthc_id_h2"], 'id' => $row["id"], 'fecha' => $row["fecha"], 'persona_id' => $row["persona_id"])),
                        $this->utilitarios(array('tipo' => '3', 'horario' => $row["horario_2_s"], 'log_marcaciones_id' => $row["log_marcaciones_id_s2"], 'salida_id' => $row["salida_id_s2"], 'fthc_id' => $row["fthc_id_h2"], 'id' => $row["id"], 'fecha' => $row["fecha"], 'persona_id' => $row["persona_id"])),
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
            case '3':
                $jqgrid = new JqgridClass($request);

                $tabla1 = "rrhh_salidas";
                $tabla2 = "rrhh_tipos_salida";

                $select = "
                    $tabla1.id,
                    $tabla1.persona_id,
                    $tabla1.tipo_salida_id,
                    $tabla1.persona_id_superior,
                    $tabla1.persona_id_rrhh,

                    $tabla1.cargo_id,
                    $tabla1.unidad_desconcentrada_id,

                    $tabla1.estado,
                    $tabla1.codigo,
                    $tabla1.destino,
                    $tabla1.motivo,
                    $tabla1.f_salida,
                    $tabla1.f_retorno,
                    $tabla1.h_salida,
                    $tabla1.h_retorno,

                    $tabla1.n_horas,
                    $tabla1.con_sin_retorno,

                    $tabla1.validar_superior,
                    $tabla1.f_validar_superior,

                    $tabla1.validar_rrhh,
                    $tabla1.f_validar_rrhh,

                    $tabla1.pdf,
                    $tabla1.papeleta_pdf,

                    $tabla1.log_marcaciones_id_s,
                    $tabla1.log_marcaciones_id_r,

                    $tabla1.salida_s,
                    $tabla1.salida_r,
                    $tabla1.min_retrasos,

                    a2.nombre AS papeleta_salida,
                    a2.tipo_cronograma,
                    a2.tipo_salida
                ";

                $persona_id  = Auth::user()->persona_id;
                $array_where = "$tabla1.persona_id=" . $persona_id . " AND a2.tipo_cronograma=1 AND a2.tipo_salida=2 AND $tabla1.validar_superior=2 AND $tabla1.validar_rrhh=2";

                $array_where .= $jqgrid->getWhere();

                $count = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
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
                        'persona_id'               => $row["persona_id"],
                        'tipo_salida_id'           => $row["tipo_salida_id"],
                        'persona_id_superior'      => $row["persona_id_superior"],
                        'persona_id_rrhh'          => $row["persona_id_rrhh"],
                        'cargo_id'                 => $row["cargo_id"],
                        'unidad_desconcentrada_id' => $row["unidad_desconcentrada_id"],
                        'estado'                   => $row["estado"],
                        'n_horas'                  => $row["n_horas"],
                        'con_sin_retorno'          => $row["con_sin_retorno"],
                        'validar_superior'         => $row["validar_superior"],
                        'f_validar_superior'       => $row["f_validar_superior"],
                        'validar_rrhh'             => $row["validar_rrhh"],
                        'f_validar_rrhh'           => $row["f_validar_rrhh"],
                        'pdf'                      => $row["pdf"],
                        'papeleta_pdf'             => $row["papeleta_pdf"],
                        'log_marcaciones_id_s'     => $row["log_marcaciones_id_s"],
                        'log_marcaciones_id_r'     => $row["log_marcaciones_id_r"],
                        'salida_s'                 => $row["salida_s"],
                        'salida_r'                 => $row["salida_r"],
                        'min_retrasos'             => $row["min_retrasos"],

                        'tipo_cronograma' => $row["tipo_cronograma"],
                        'tipo_salida'     => $row["tipo_salida"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',

                        $this->utilitarios(array('tipo' => '11', 'estado' => $row["estado"])),

                        $row["codigo"],

                        $row["f_salida"],
                        $row["h_salida"],
                        $row["h_retorno"],
                        $this->utilitarios(array('tipo' => '13', 'con_sin_retorno' => $row["con_sin_retorno"])),

                        $this->utilitarios(array('tipo' => '14', 'marcacion' => $row["salida_s"])),
                        $this->utilitarios(array('tipo' => '14', 'marcacion' => $row["salida_r"])),
                        $this->utilitarios(array('tipo' => '12', 'min_retrasos' => $row["min_retrasos"])),

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
                'titulo'    => 'GESTOR DE PERMISOS',
                'respuesta' => 'No es solicitud AJAX.'
            ];
            return json_encode($respuesta);
        }

        $tipo = $request->input('tipo');

        switch($tipo)
        {
            // === INSERT UPDATE GESTOR DE MODULOS ===
            case '1':
                // === SEGURIDAD ===
                    $id = Auth::user()->persona_id;
                // === LIBRERIAS ===
                    $util = new UtilClass();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>INFORMACION PERSONAL</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );
                    $opcion = 'n';
                    $error  = FALSE;

                // === PERMISOS ===
                    if($id == '')
                    {
                        $respuesta['respuesta'] .= "No tiene información personal. Consulte con el Administrador de personal.";
                        return json_encode($respuesta);
                    }

                // === VALIDATE ===
                    // try
                    // {
                    //     $validator = $this->validate($request,[
                    //         'f_nacimiento'            => 'required|date',
                    //         'nombre'                  => 'required|max:50',
                    //         'ap_paterno'              => 'max:50',
                    //         'ap_materno'              => 'max:50',
                    //         'ap_esposo'               => 'max:50',
                    //         'estado_civil'            => 'required',
                    //         'municipio_id_nacimiento' => 'required',
                    //         'domicilio'               => 'required|max:500',
                    //         'telefono'                => 'max:50',
                    //         'celular'                 => 'required|max:50',
                    //         'municipio_id_residencia' => 'required'
                    //     ],
                    //     [
                    //         'f_nacimiento.required' => 'El campo FECHA DE NACIMIENTO es obligatorio.',
                    //         'f_nacimiento.date'    => 'El campo FECHA DE NACIMIENTO no corresponde con una fecha válida.',

                    //         'nombre.required' => 'El campo NOMBRE(S) es obligatorio.',
                    //         'nombre.max'      => 'El campo NOMBRE(S) debe ser :max caracteres como máximo.',


                    //         'ap_paterno.max'      => 'El campo APELLIDO PATERNO debe ser :max caracteres como máximo.',

                    //         'ap_materno.max'      => 'El campo APELLIDO MATERNO debe ser :max caracteres como máximo.',

                    //         'ap_esposo.max'      => 'El campo APELLIDO ESPOSO debe ser :max caracteres como máximo.',

                    //         'estado_civil.required' => 'El campo ESTADO CIVIL es obligatorio.',

                    //         'municipio_id_nacimiento.required' => 'El campo LUGAR DE NACIMIENTO es obligatorio.',

                    //         'domicilio.required' => 'El campo DOMICILIO es obligatorio.',
                    //         'domicilio.max'      => 'El campo DOMICILIO debe ser :max caracteres como máximo.',

                    //         'telefono.max'      => 'El campo TELEFONO debe ser :max caracteres como máximo.',

                    //         'celular.required' => 'El campo CELULAR es obligatorio.',
                    //         'celular.max'      => 'El campo CELULAR debe ser :max caracteres como máximo.',

                    //         'municipio_id_residencia.required' => 'El campo RESIDENCIA ACTUAL es obligatorio.'
                    //     ]);
                    // }
                    // catch (Exception $e)
                    // {
                    //     $respuesta['error_sw'] = 2;
                    //     $respuesta['error']    = $e;
                    //     return json_encode($respuesta);
                    // }

                //=== OPERACION ===
                    $data                            = [];
                    $data['f_nacimiento']            = trim($request->input('f_nacimiento'));
                    $data['sexo']                    = trim($request->input('sexo'));
                    $data['nombre']                  = strtoupper($util->getNoAcentoNoComilla(trim($request->input('nombre'))));
                    $data['ap_paterno']              = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ap_paterno'))));
                    $data['ap_materno']              = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ap_materno'))));
                    $data['ap_esposo']               = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ap_esposo'))));
                    $data['estado_civil']            = trim($request->input('estado_civil'));
                    $data['municipio_id_nacimiento'] = trim($request->input('municipio_id_nacimiento'));
                    $data['domicilio']               = strtoupper($util->getNoAcentoNoComilla(trim($request->input('domicilio'))));
                    $data['telefono']                = trim($request->input('telefono'));
                    $data['celular']                 = trim($request->input('celular'));
                    $data['municipio_id_residencia'] = trim($request->input('municipio_id_residencia'));

                    // === CONVERTIR VALORES VACIOS A NULL ===
                        foreach ($data as $llave => $valor)
                        {
                            if ($valor == '')
                                $data[$llave] = NULL;
                        }

                    $consulta1 = RrhhPersona::where('id', '=', $id)->first();

                    $iu                          = RrhhPersona::find($id);
                    $iu->municipio_id_nacimiento = $data['municipio_id_nacimiento'];
                    $iu->municipio_id_residencia = $data['municipio_id_residencia'];

                    if($consulta1['estado_segip'] == '1')
                    {
                        $iu->nombre                  = $data['nombre'];
                        $iu->ap_paterno              = $data['ap_paterno'];
                        $iu->ap_materno              = $data['ap_materno'];
                        $iu->f_nacimiento            = $data['f_nacimiento'];
                    }

                    $iu->ap_esposo               = $data['ap_esposo'];

                    $iu->estado_civil            = $data['estado_civil'];
                    $iu->sexo                    = $data['sexo'];
                    $iu->domicilio               = $data['domicilio'];
                    $iu->telefono                = $data['telefono'];
                    $iu->celular                 = $data['celular'];
                    $iu->save();

                    $respuesta['respuesta'] .= "Su INFORMACION PERSONAL fue actualizada con éxito.";
                    $respuesta['sw']         = 1;
                    $respuesta['iu']         = 2;

                    if($consulta1['estado_segip'] == '1')
                    {
                        $c_usuario = User::where('persona_id', '=', $id)->select("id")->first();
                        if(!($c_usuario === null))
                        {
                            $iu1       = User::find($c_usuario['id']);
                            $iu1->name = $data['nombre'];
                            $iu1->save();
                        }
                    }
                //=== respuesta ===
                return json_encode($respuesta);
                break;
            // === UPLOAD IMAGE ===
            case '2':
                // === SEGURIDAD ===
                    $id = Auth::user()->id;

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>SUBIR FOTOGRAFIA</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );
                    $opcion = 'n';

                // === PERMISOS ===
                    if($id == '')
                    {
                        $respuesta['respuesta'] .= "No se tiene información de su usuario. Consulte con el Administrador del Sistema.";
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    //=== IMAGEN UPLOAD ===
                        $user_imagen = User::where('id', '=', $id)
                            ->select('imagen')
                            ->first()
                            ->toArray();
                        if($user_imagen['imagen'] != '')
                        {
                            if(file_exists(public_path($this->public_dir) . '/' . $user_imagen['imagen']))
                            {
                                unlink(public_path($this->public_dir) . '/' . $user_imagen['imagen']);
                            }
                        }

                        if($request->hasFile('file'))
                        {
                            $archivo           = $request->file('file');
                            $nombre_archivo    = uniqid('user_', true) . '.' . $archivo->getClientOriginalExtension();
                            $direccion_archivo = public_path($this->public_dir);

                            $archivo->move($direccion_archivo, $nombre_archivo);

                            $image_user   = Image::make($direccion_archivo . '/' . $nombre_archivo);

                            $image_user->resize(512, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });

                            $image_user->resize(null, 512, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });

                            $image_user->save($direccion_archivo . '/' . $nombre_archivo);
                        }

                    $iu                    = User::find($id);
                    $iu->imagen            = $nombre_archivo;
                    $iu->save();

                    $respuesta['respuesta']      .= "La FOTOGRAFIA fue subida.";
                    $respuesta['sw']             = 1;
                    $respuesta['iu']             = 2;
                    $respuesta['nombre_archivo'] = $nombre_archivo;

                return json_encode($respuesta);
                break;
            // === INSERT UPDATE ===
            case '3':
                // === SEGURIDAD ===
                    $id = Auth::user()->id;
                // === LIBRERIAS ===
                    $util = new UtilClass();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>CAMBIO DE CONTRASEÑA</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );
                    $opcion = 'n';

                // === PERMISOS ===
                    if($id == '')
                    {
                        $respuesta['respuesta'] .= "No se tiene información de su usuario. Consulte con el Administrador del Sistema.";
                        return json_encode($respuesta);
                    }

                // === VALIDATE ===
                    try
                    {
                        $validator = $this->validate($request,[
                            'a_contrasenia' => 'required|min:6|max:16',
                            'contrasenia'   => 'required|min:6|max:16'
                        ],
                        [
                            'a_contrasenia.required' => 'El campo CONTRASEÑA ACTUAL es obligatorio.',
                            'a_contrasenia.min'      => 'El campo CONTRASEÑA ACTUAL debe tener al menos :min caracteres.',
                            'a_contrasenia.max'      => 'El campo CONTRASEÑA ACTUAL debe ser :max caracteres como máximo.',

                            'contrasenia.required' => 'El campo NUEVA CONTRASEÑA es obligatorio.',
                            'contrasenia.min'      => 'El campo NUEVA CONTRASEÑA debe tener al menos :min caracteres.',
                            'contrasenia.max'      => 'El campo NUEVA CONTRASEÑA debe ser :max caracteres como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    if(Hash::check($request->input('a_contrasenia'), Auth::user()->password))
                    {
                        if($request->has('contrasenia'))
                        {
                            $password = trim($request->input('contrasenia'));

                            if(!preg_match('/(?=[a-z])/', $password))
                            {
                                $respuesta['respuesta'] .= "La NUEVA CONTRASEÑA debe contener al menos una minuscula.";
                                return json_encode($respuesta);
                            }

                            if(!preg_match('/(?=[A-Z])/', $password))
                            {
                                $respuesta['respuesta'] .= "La NUEVA CONTRASEÑA debe contener al menos una mayuscula.";
                                return json_encode($respuesta);
                            }

                            if(!preg_match('/(?=\d)/', $password))
                            {
                                $respuesta['respuesta'] .= "La NUEVA CONTRASEÑA debe contener al menos un digito.";
                                return json_encode($respuesta);
                            }

                            $password       = bcrypt($password);
                        }

                        $iu           = User::find($id);
                        $iu->password = $password;
                        $iu->save();

                        $respuesta['respuesta'] .= "Se cambio la CONTRASEÑA con éxito.";
                        $respuesta['sw']         = 1;
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "La CONTRASEÑA ACTUAL está incorrecto.";
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
                    if(!($query === null))
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
                    if(!($query === null))
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

                    if(!($query === null))
                    {
                        $respuesta['consulta'] = $query;
                        $respuesta['sw']       = 2;
                    }
                }
                return json_encode($respuesta);
                break;

            // === SELECT2 DEPARTAMENTO, PROVINCIA Y MUNICIPIO ===
            case '100':
                if($request->has('q'))
                {
                    $nombre     = $request->input('q');
                    $estado     = trim($request->input('estado'));
                    $page_limit = trim($request->input('page_limit'));

                    $query = UbgeMunicipio::leftJoin("ubge_provincias", "ubge_provincias.id", "=", "ubge_municipios.provincia_id")
                                ->leftJoin("ubge_departamentos", "ubge_departamentos.id", "=", "ubge_provincias.departamento_id")
                                ->whereRaw("CONCAT_WS(', ', ubge_departamentos.nombre, ubge_provincias.nombre, ubge_municipios.nombre) ilike '%$nombre%'")
                                ->where("ubge_municipios.estado", "=", $estado)
                                ->select(DB::raw("ubge_municipios.id, CONCAT_WS(', ', ubge_departamentos.nombre, ubge_provincias.nombre, ubge_municipios.nombre) AS text"))
                                ->orderByRaw("ubge_municipios.codigo ASC")
                                ->limit($page_limit)
                                ->get()
                                ->toArray();

                    if(!($query === null))
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
                    $respuesta = '<button class="btn btn-xs btn-success" onclick="utilitarios([22, ' . $valor['log_marcaciones_id'] . ', ' . $valor['id'] . ']);" title="Ver donde asistio" style="margin:0px 0px 0px 0px; padding-top: 0px; padding-bottom: 0.2px;">
                            <i class="fa fa-eye"></i>
                            <strong>' . $valor['horario'] . '</strong>
                        </button>';
                }
                elseif($valor['salida_id'] != '')
                {
                    $respuesta = '<button class="btn btn-xs btn-primary" onclick="utilitarios([21, ' . $valor['salida_id'] . ', ' . $valor['id'] . ']);" title="Ver licencia o salida" style="margin:0px 0px 0px 0px; padding-top: 0px; padding-bottom: 0.2px;">
                            <i class="fa fa-eye"></i>
                            <strong>' . $valor['horario'] . '</strong>
                        </button>';
                }
                elseif($valor['fthc_id'] != '')
                {
                    $respuesta = '<button class="btn btn-xs btn-info" onclick="utilitarios([23, ' . $valor['fthc_id'] . ', ' . $valor['id'] . ', ' . "'" .  $valor['horario'] . "'" . ']);" title="Ver ' . $valor['horario'] . '" style="margin:0px 0px 0px 0px; padding-top: 0px; padding-bottom: 0.2px;">
                            <i class="fa fa-eye"></i>
                            <strong>' . $valor['horario'] . '</strong>
                        </button>';
                }
                else
                {
                    if($this->falta['1'] == $valor['horario'])
                    {
                        $respuesta = '<span class="label label-danger font-sm">' . $valor['horario'] . '</span>';
                    }
                    elseif($this->omision['1'] == $valor['horario'])
                    {
                        $respuesta = '<button class="btn btn-xs btn-warning" onclick="utilitarios([24, ' . $valor['id'] . ', ' . "'" . $valor['fecha'] . "'" . ', ' . $valor['persona_id'] . ']);" title="Ver marcaciones" style="margin:0px 0px 0px 0px; padding-top: 0px; padding-bottom: 0.2px;">
                            <i class="fa fa-table"></i>
                            <strong>' . $valor['horario'] . '</strong>
                        </button>';
                    }
                    else
                    {
                        $respuesta = '<button class="btn btn-xs btn-warning" onclick="utilitarios([26, ' . $valor['id'] . ']);" title="Ver USUARIO que modificó" style="margin:0px 0px 0px 0px; padding-top: 0px; padding-bottom: 0.2px;">
                            <i class="fa fa-eye"></i>
                            <strong>' . $valor['horario'] . '</strong>
                        </button>';
                    }
                }
                return($respuesta);
                break;


            case '11':
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
            case '12':
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
            case '13':
                switch($valor['con_sin_retorno'])
                {
                    case '1':
                        $respuesta = '<span class="label label-primary font-sm">' . $this->con_sin_retorno[$valor['con_sin_retorno']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-success
                         font-sm">' . $this->con_sin_retorno[$valor['con_sin_retorno']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '';
                        return($respuesta);
                        break;
                }
                break;
            case '14':
                switch($valor['marcacion'])
                {
                    case $this->sp_estado['1']:
                        $respuesta = '<span class="label label-danger font-sm">' . $valor['marcacion'] . '</span>';
                        return($respuesta);
                        break;
                    case $this->sp_estado['2']:
                        $respuesta = '<span class="label label-success font-sm">' . $valor['marcacion'] . '</span>';
                        return($respuesta);
                        break;
                    case $this->sp_estado['3']:
                        $respuesta = '<span class="label label-info font-sm">' . $valor['marcacion'] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-primary font-sm">' . $valor['marcacion'] . '</span>';
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
                    $dir_logo_institucion = public_path($this->public_dir_1) . '/' . 'logo_fge_256.png';
                    $dir_logo_pais        = public_path($this->public_dir_1) . '/' . 'logo_fge_256_2018_3.png';
                    $dir_marca_agua       = public_path($this->public_dir_1) . '/' . 'marca_agua_500.png';

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

                        if($consulta1 === null)
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

                        if($consulta2 === null)
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

                        if($consulta3 === null)
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

                        if($consulta4 === null)
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

                            // $this->utilitarios(array(
                            //     'tipo'      => '100',
                            //     'file'      => $data2['dir_logo_institucion'],
                            //     'x'         => 10,
                            //     'y'         => 10,
                            //     'w'         => 0,
                            //     'h'         => 20,
                            //     'type'      => 'PNG',
                            //     'link'      => '',
                            //     'align'     => '',
                            //     'resize'    => FALSE,
                            //     'dpi'       => 300,
                            //     'palign'    => '',
                            //     'ismask'    => FALSE,
                            //     'imgsmask'  => FALSE,
                            //     'border'    => 0,
                            //     'fitbox'    => FALSE,
                            //     'hidden'    => FALSE,
                            //     'fitonpage' => FALSE
                            // ));

                            $this->utilitarios(array(
                                'tipo'      => '100',
                                'file'      => $data2['dir_logo_pais'],
                                'x'         => 183,
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

                            // $this->utilitarios(array(
                            //     'tipo'      => '100',
                            //     'file'      => $data2['dir_marca_agua'],
                            //     'x'         => 63,
                            //     'y'         => 39,
                            //     'w'         => 0,
                            //     'h'         => 90,
                            //     'type'      => '',
                            //     'link'      => '',
                            //     'align'     => '',
                            //     'resize'    => TRUE,
                            //     'dpi'       => 140,
                            //     'palign'    => '',
                            //     'ismask'    => FALSE,
                            //     'imgsmask'  => FALSE,
                            //     'border'    => 0,
                            //     'fitbox'    => FALSE,
                            //     'hidden'    => FALSE,
                            //     'fitonpage' => FALSE
                            // ));

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
            default:
                break;
        }
    }
}
