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
use App\Models\Rrhh\RrhhFuncionarioExCargo;
use App\Models\Rrhh\RrhhBiometrico;
use App\Models\Rrhh\RrhhLogMarcacion;
use App\Models\Rrhh\RrhhPersonaBiometrico;
use App\Models\Rrhh\RrhhHorario;

use Maatwebsite\Excel\Facades\Excel;

use Exception;

class FuncionarioController extends Controller
{
    private $estado;
    private $acefalia;
    private $situacion;
    private $documento_sw;

    private $rol_id;
    private $permisos;

    private $public_dir;
    private $public_url;
    private $link_pdf;

    private $reporte_1;
    private $reporte_data_1;

    public function __construct()
    {
        $this->middleware('auth');

        $this->estado = [
            '1' => 'HABILITADO',
            '2' => 'INHABILITADO'
        ];

        $this->acefalia = [
            '1' => 'SI',
            '2' => 'NO'
        ];

        $this->situacion = [
            '1' => 'EVENTUAL',
            '2' => 'INSTITUCIONALIZADO'
        ];

        $this->documento_sw = [
            '1' => 'NO',
            '2' => 'SI'
        ];

        $this->public_dir = '/storage/rrhh/funcionario/documentos/designacion';
        $this->public_url = 'storage/rrhh/funcionario/documentos/designacion/';
        $this->link_pdf   = asset($this->public_url );
    }

    public function index()
    {
        $this->rol_id   = Auth::user()->rol_id;
        $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
            ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
            ->select("seg_permisos.codigo")
            ->get()
            ->toArray();

        if(in_array(['codigo' => '0801'], $this->permisos))
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
                'title'                   => 'Funcionarios',
                'home'                    => 'Inicio',
                'sistema'                 => 'Recursos humanos',
                'modulo'                  => 'Funcionarios',
                'title_table'             => 'Funcionarios',
                'public_url'              => $this->public_url,
                'estado_array'            => $this->estado,
                'acefalia_array'          => $this->acefalia,
                'situacion_array'         => $this->situacion,
                'documento_sw_array'      => $this->documento_sw,
                'tipo_cargo_array'        => InstTipoCargo::where("estado", "=", 1)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray(),
                'lugar_dependencia_array' => InstLugarDependencia::whereRaw($array_where)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray()
            ];
            return view('rrhh.funcionario.funcionario')->with($data);
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

                $tabla1 = "inst_cargos";
                $tabla2 = "inst_tipos_cargo";
                $tabla3 = "inst_auos";
                $tabla4 = "inst_lugares_dependencia";
                $tabla5 = "rrhh_funcionarios";
                $tabla6 = "rrhh_personas";
                $tabla7 = "inst_unidades_desconcentradas";
                $tabla8 = "rrhh_horarios";

                $select = "
                    $tabla1.id,
                    $tabla1.auo_id,
                    $tabla1.tipo_cargo_id,
                    $tabla1.item_contrato,
                    $tabla1.acefalia,
                    $tabla1.nombre As cargo,

                    a2.nombre AS tipo_cargo,

                    a3.lugar_dependencia_id AS lugar_dependencia_id_cargo,
                    a3.nombre AS auo_cargo,

                    a4.nombre AS lugar_dependencia_cargo,

                    a5.id AS funcionario_id,
                    a5.persona_id,
                    a5.cargo_id,
                    a5.unidad_desconcentrada_id,
                    a5.horario_id_1,
                    a5.horario_id_2,
                    a5.situacion,
                    a5.documento_sw,
                    a5.f_ingreso,
                    a5.f_salida,
                    a5.sueldo,
                    a5.observaciones,
                    a5.documento_file,

                    a6.n_documento,
                    a6.nombre AS nombre_persona,
                    a6.ap_paterno,
                    a6.ap_materno,

                    a7.lugar_dependencia_id AS lugar_dependencia_id_funcionario,
                    a7.nombre AS ud_funcionario,

                    a8.nombre AS lugar_dependencia_funcionario,

                    a9.nombre AS horario_1,
                    a10.nombre AS horario_2
                ";

                $array_where = "$tabla1.estado=1";

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

                $count = InstCargo::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_cargo_id")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.auo_id")
                    ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                    ->leftJoin("$tabla5 AS a5", "a5.cargo_id", "=", "$tabla1.id")
                    ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.persona_id")
                    ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a5.unidad_desconcentrada_id")
                    ->leftJoin("$tabla4 AS a8", "a8.id", "=", "a7.lugar_dependencia_id")
                    ->leftJoin("$tabla8 AS a9", "a9.id", "=", "a5.horario_id_1")
                    ->leftJoin("$tabla8 AS a10", "a10.id", "=", "a5.horario_id_2")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = InstCargo::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_cargo_id")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.auo_id")
                    ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                    ->leftJoin("$tabla5 AS a5", "a5.cargo_id", "=", "$tabla1.id")
                    ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.persona_id")
                    ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a5.unidad_desconcentrada_id")
                    ->leftJoin("$tabla4 AS a8", "a8.id", "=", "a7.lugar_dependencia_id")
                    ->leftJoin("$tabla8 AS a9", "a9.id", "=", "a5.horario_id_1")
                    ->leftJoin("$tabla8 AS a10", "a10.id", "=", "a5.horario_id_2")
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
                        'auo_id'                           => $row["auo_id"],
                        'tipo_cargo_id'                    => $row["tipo_cargo_id"],
                        'acefalia'                         => $row["acefalia"],
                        'lugar_dependencia_id_cargo'       => $row["lugar_dependencia_id_cargo"],
                        'funcionario_id'                   => $row["funcionario_id"],
                        'persona_id'                       => $row["persona_id"],
                        'unidad_desconcentrada_id'         => $row["unidad_desconcentrada_id"],
                        'situacion'                        => $row["situacion"],
                        'documento_sw'                     => $row["documento_sw"],
                        'documento_file'                   => $row["documento_file"],
                        'lugar_dependencia_id_funcionario' => $row["lugar_dependencia_id_funcionario"],
                        'horario_id_1'                     => $row["horario_id_1"],
                        'horario_id_2'                     => $row["horario_id_2"]
                    );

                    $ci_nombre = $row["n_documento"] . ' - ' . trim($row["ap_paterno"] . ' ' . $row["ap_materno"]) . ' ' . $row["nombre_persona"];

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',
                        $this->utilitarios(array('tipo' => '2', 'acefalia' => $row["acefalia"], 'id' => $row["funcionario_id"])),
                        $row["tipo_cargo"],
                        ($row["situacion"] == '')? '' : $this->situacion[$row["situacion"]],
                        $this->utilitarios(array('tipo' => '3', 'documento_sw' => $row["documento_sw"], 'id' => $row["funcionario_id"], 'ci_nombre' => $ci_nombre)),
                        // ($row["documento_sw"] == '')? '' : $this->documento_sw[$row["documento_sw"]],
                        $row["item_contrato"],

                        $row["n_documento"],
                        $row["nombre_persona"],
                        $row["ap_paterno"],
                        $row["ap_materno"],

                        $row["f_ingreso"],
                        $row["f_salida"],
                        $row["sueldo"],

                        $row["ud_funcionario"],
                        $row["lugar_dependencia_funcionario"],

                        $row["horario_1"],
                        $row["horario_2"],

                        $row["cargo"],
                        $row["auo_cargo"],
                        $row["lugar_dependencia_cargo"],

                        $row["observaciones"],

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

                $array_where = "$tabla1.persona_id=" . $persona_id . " ";

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
                        'titulo'     => '<div class="text-center"><strong>FUNCIONARIO</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );
                    $opcion = 'n';

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if($id != '')
                    {
                        $opcion = 'e';
                        if(!in_array(['codigo' => '0803'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '0802'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                // === VALIDATE ===
                    try
                    {
                        if($request->has('f_salida'))
                        {
                            $validator = $this->validate($request,[
                                'persona_id'                       => 'required',
                                'f_ingreso'                        => 'required|date',
                                'f_salida'                         => 'date',
                                // 'sueldo'                           => 'required|numeric',62
                                'sueldo'                           => 'numeric',
                                'lugar_dependencia_id_funcionario' => 'required',
                                'unidad_desconcentrada_id'         => 'required',
                                'horario_id_1'                     => 'required'
                            ],
                            [
                                'persona_id.required' => 'El campo FUNCIONARIO es obligatorio.',

                                'f_ingreso.required' => 'El campo FECHA DE INGRESO es obligatorio.',
                                'f_ingreso.date'     => 'El campo FECHA DE INGRESO no corresponde a una fecha válida.',

                                'f_salida.date' => 'El campo FECHA DE SALIDA no corresponde a una fecha válida.',

                                // 'sueldo.required' => 'El campo SUELDO es obligatorio.',
                                'sueldo.numeric'  => 'El campo SUELDO debe ser un número.',

                                'lugar_dependencia_id_funcionario.required' => 'El campo LUGAR DE DEPENDENCIA es obligatorio.',

                                'unidad_desconcentrada_id.required' => 'El campo UNIDAD DESCONCENTRADA es obligatorio.',

                                'horario_id_1.required' => 'El campo HORARIO 1 es obligatorio.'
                            ]);
                        }
                        else
                        {
                            $validator = $this->validate($request,[
                                'persona_id'                       => 'required',
                                'f_ingreso'                        => 'required|date',
                                // 'sueldo'                           => 'required|numeric',
                                'sueldo'                           => 'numeric',
                                'lugar_dependencia_id_funcionario' => 'required',
                                'unidad_desconcentrada_id'         => 'required',
                                'horario_id_1'                     => 'required'
                            ],
                            [
                                'persona_id.required' => 'El campo FUNCIONARIO es obligatorio.',

                                'f_ingreso.required' => 'El campo FECHA DE INGRESO es obligatorio.',
                                'f_ingreso.date'     => 'El campo FECHA DE INGRESO no corresponde a una fecha válida.',

                                // 'sueldo.required' => 'El campo SUELDO es obligatorio.',
                                'sueldo.numeric'  => 'El campo SUELDO debe ser un número.',

                                'lugar_dependencia_id_funcionario.required' => 'El campo LUGAR DE DEPENDENCIA es obligatorio.',

                                'unidad_desconcentrada_id.required' => 'El campo UNIDAD DESCONCENTRADA es obligatorio.',

                                'horario_id_1.required' => 'El campo HORARIO 1 es obligatorio.'
                            ]);
                        }
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['persona_id']               = trim($request->input('persona_id'));
                    $data1['cargo_id']                 = trim($request->input('cargo_id'));
                    $data1['unidad_desconcentrada_id'] = trim($request->input('unidad_desconcentrada_id'));
                    $data1['horario_id_1']             = trim($request->input('horario_id_1'));
                    $data1['horario_id_2']             = trim($request->input('horario_id_2'));
                    $data1['situacion']                = trim($request->input('situacion'));
                    $data1['f_ingreso']                = trim($request->input('f_ingreso'));
                    $data1['f_salida']                 = trim($request->input('f_salida'));
                    $data1['sueldo']                   = trim($request->input('sueldo'));
                    $data1['observaciones']            = strtoupper($util->getNoAcentoNoComilla(trim($request->input('observaciones'))));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $consulta1 = RrhhFuncionario::where('persona_id', '=', $data1['persona_id'])
                            ->count();

                        if($consulta1 < 1)
                        {
                            $iu                           = new RrhhFuncionario;
                            $iu->persona_id               = $data1['persona_id'];
                            $iu->cargo_id                 = $data1['cargo_id'];
                            $iu->unidad_desconcentrada_id = $data1['unidad_desconcentrada_id'];
                            $iu->horario_id_1             = $data1['horario_id_1'];
                            $iu->horario_id_2             = $data1['horario_id_2'];
                            $iu->situacion                = $data1['situacion'];
                            $iu->f_ingreso                = $data1['f_ingreso'];
                            $iu->f_salida                 = $data1['f_salida'];
                            $iu->sueldo                   = $data1['sueldo'];
                            $iu->observaciones            = $data1['observaciones'];
                            $iu->save();

                            $respuesta['respuesta'] .= "El FUNCIONARIO fue registrado con éxito.";
                            $respuesta['sw']         = 1;

                            $iu           = InstCargo::find($data1['cargo_id']);
                            $iu->acefalia = 2;
                            $iu->save();
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "La PERSONA ya fue registrada en otro cargo.";
                        }
                    }
                    else
                    {
                        $consulta1 = RrhhFuncionario::where('persona_id', '=', $data1['persona_id'])
                            ->where('id', '<>', $id)
                            ->count();

                        if($consulta1 < 1)
                        {
                            $iu                           = RrhhFuncionario::find($id);
                            $iu->persona_id               = $data1['persona_id'];
                            $iu->cargo_id                 = $data1['cargo_id'];
                            $iu->unidad_desconcentrada_id = $data1['unidad_desconcentrada_id'];
                            $iu->horario_id_1             = $data1['horario_id_1'];
                            $iu->horario_id_2             = $data1['horario_id_2'];
                            $iu->situacion                = $data1['situacion'];
                            $iu->f_ingreso                = $data1['f_ingreso'];
                            $iu->f_salida                 = $data1['f_salida'];
                            $iu->sueldo                   = $data1['sueldo'];
                            $iu->observaciones            = $data1['observaciones'];
                            $iu->save();

                            $respuesta['respuesta'] .= "El FUNCIONARIO se edito con éxito.";
                            $respuesta['sw']         = 1;
                            $respuesta['iu']         = 2;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "La PERSONA ya fue registrada en otro cargo.";
                        }
                    }
                return json_encode($respuesta);
                break;
            // === UPLOAD IMAGE ===
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
                        'titulo'     => '<div class="text-center"><strong>SUBIR DOCUMENTO</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'error_sw'   => 1
                    );
                    $opcion = 'n';

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if($id != '')
                    {
                        $opcion = 'e';
                        if(!in_array(['codigo' => '0803'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "La ID del FUNCIONARIO es obligatorio.";
                        return json_encode($respuesta);
                    }

                // === VALIDATE ===
                    try
                    {
                       $validator = $this->validate($request,[
                            'file' => 'mimes:pdf|max:5120'
                        ],
                        [
                            'file.mimes' => 'El archivo subido debe de ser de tipo :values.',
                            'file.max'   => 'El archivo debe pesar 5120 kilobytes como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $consulta1 = RrhhFuncionario::where('id', '=', $id)
                        ->select('documento_file')
                        ->first()
                        ->toArray();
                    if($consulta1['documento_file'] != '')
                    {
                        if(file_exists(public_path($this->public_dir) . '/' . $consulta1['documento_file']))
                        {
                            unlink(public_path($this->public_dir) . '/' . $consulta1['documento_file']);
                        }
                    }

                    if($request->hasFile('file'))
                    {
                        $archivo           = $request->file('file');
                        $nombre_archivo    = uniqid('designacion_', true) . '.' . $archivo->getClientOriginalExtension();
                        $direccion_archivo = public_path($this->public_dir);

                        $archivo->move($direccion_archivo, $nombre_archivo);
                    }

                    $iu                 = RrhhFuncionario::find($id);
                    $iu->documento_sw   = 2;
                    $iu->documento_file = $nombre_archivo;
                    $iu->save();

                    $respuesta['respuesta'] .= "El DOCUMENTO DE DESIGNACION se subio con éxito.";
                    $respuesta['sw']         = 1;

                return json_encode($respuesta);
                break;
            // === ELIMINAR FUNCIONARIO DEL CARGO ===
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

                    $fh_servidor = date("Y-m-d H:i:s");
                    $f_servidor = date("Y-m-d");

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if(!in_array(['codigo' => '0805'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para ELIMINAR AL FUNCIONARIO DEL CARGO.";
                        return json_encode($respuesta);
                    }

                // === CONSULTA ===
                    $consulta2 = RrhhFuncionario::where('id', '=', $id)
                            ->count();

                //=== OPERACION ===
                    if($consulta2 == '1')
                    {
                        $consulta1 = RrhhFuncionario::where('id', '=', $id)
                            ->first()
                            ->toArray();

                        $iu                           = new RrhhFuncionarioExCargo;
                        $iu->persona_id               = $consulta1['persona_id'];
                        $iu->cargo_id                 = $consulta1['cargo_id'];
                        $iu->unidad_desconcentrada_id = $consulta1['unidad_desconcentrada_id'];
                        $iu->estado                   = $consulta1['estado'];
                        $iu->situacion                = $consulta1['situacion'];
                        $iu->documento_sw             = $consulta1['documento_sw'];
                        $iu->f_ingreso                = $consulta1['f_ingreso'];
                        $iu->f_salida                 = $f_servidor;
                        $iu->sueldo                   = $consulta1['sueldo'];
                        $iu->observaciones            = $consulta1['observaciones'];
                        $iu->documento_file           = $consulta1['documento_file'];
                        $iu->save();

                        $de = RrhhFuncionario::find($id);
                        $de->delete();

                        $iu           = InstCargo::find($consulta1['cargo_id']);
                        $iu->acefalia = 1;
                        $iu->save();

                        $respuesta['sw'] = 1;
                        $respuesta['respuesta'] .= "Se elimino al FUNCIONARIO DEL CARGO.";
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "Ya se elimino al FUNCIONARIO DEL CARGO.";
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

            // === SELECT2 ORGANIGRAMA CARGOS POR AREA O UNIDAD DESCONCENTRADA ===
            case '101':
                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>ALERTA</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo
                    );
                //=== OPERACION ===
                    if($request->has('auo_id'))
                    {
                        $auo_id     = trim($request->input('auo_id'));

                        $user_id = Auth::user()->id;

                        $consulta1 = SegLdUser::where("seg_ld_users.user_id", "=", $user_id)
                            ->select('lugar_dependencia_id')
                            ->get()
                            ->toArray();

                        $organigrama_array = [];

                        $consulta1 = InstCargo::leftJoin("inst_tipos_cargo AS a2", "a2.id", "=", "inst_cargos.tipo_cargo_id")
                            ->whereNull('inst_cargos.cargo_id')
                            ->where("inst_cargos.estado", "=", 1)
                            ->where("inst_cargos.auo_id", "=", $auo_id)
                            ->select('inst_cargos.id', 'inst_cargos.tipo_cargo_id', 'inst_cargos.item_contrato', 'inst_cargos.nombre', 'inst_cargos.acefalia', 'a2.nombre AS tipo_cargo')
                            ->first()
                            ->toArray();

                        if(count($consulta1) > 0)
                        {
                            if($consulta1['tipo_cargo_id'] == 1)
                            {
                                $organigrama_array['name']  = $consulta1['tipo_cargo'] . ' ' . $consulta1['item_contrato'] . ' - ¿ACEFALO? ' . $this->acefalia[$consulta1['acefalia']];
                            }
                            else
                            {
                                $organigrama_array['name']  = $consulta1['tipo_cargo'] . ' - ¿ACEFALO? ' . $this->acefalia[$consulta1['acefalia']];
                            }
                            $organigrama_array['title'] = $consulta1['nombre'];

                            $organigrama_array['children'] = $this->utilitarios(['tipo' => '10', 'cargo_id' => $consulta1['id']]);

                            $respuesta['respuesta'] = $organigrama_array;
                            $respuesta['sw']        = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "¡No existe CARGOS en el ÁREA O UNIDAD ORGANIZACIONAL seleccionada!<br>¡Verifique!";
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "¡Favor seleccione un ÁREA O UNIDAD ORGANIZACIONAL!<br>¡Verifique!";
                    }

                return json_encode($respuesta);
                break;

            // === SELECT2 CARGOS POR UNIDAD DESCONCENTRADA ===
            case '102':
                $respuesta = [
                    'tipo' => $tipo,
                    'sw'   => 1
                ];
                if($request->has('auo_id'))
                {
                    $auo_id  = $request->input('auo_id');
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

            // === SELECT2 UNIDAD DESCONCENTRADA ===
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
            // === SELECT2 UNIDAD DESCONCENTRADA ===
            case '104':
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
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
                        return($respuesta);
                        break;
                }
                break;
            case '2':
                switch($valor['acefalia'])
                {
                    case '1':
                        $respuesta = '<span class="label label-primary font-sm">' . $this->acefalia[$valor['acefalia']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<button class="btn btn-xs btn-danger" onclick="utilitarios([21, ' . $valor['id'] . ']);" title="Eliminar al funcionario del cargo">
                            <i class="fa fa-trash"></i>
                            <strong>' . $this->acefalia[$valor['acefalia']] . '</strong>
                        </button>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">?</span>';
                        return($respuesta);
                        break;
                }
                break;
            case '3':
                switch($valor['documento_sw'])
                {
                    case '1':
                        $respuesta = '<button class="btn btn-xs btn-danger" onclick="utilitarios([19, ' . $valor['id'] . ', \'' . $valor['ci_nombre'] . '\']);" title="Clic para subir documento">
                            <i class="fa fa-upload"></i>
                            <strong>' . $this->documento_sw[$valor['documento_sw']] . '</strong>
                        </button>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<button class="btn btn-xs btn-primary" onclick="utilitarios([19, ' . $valor['id'] . ']);" title="Clic para remplazar el documento">
                            <i class="fa fa-upload"></i>
                            <strong>' . $this->documento_sw[$valor['documento_sw']] . '</strong>
                        </button>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '';
                        return($respuesta);
                        break;
                }
                break;
            case '4':
                switch($valor['acefalia'])
                {
                    case '1':
                        $respuesta = $this->acefalia[$valor['acefalia']];
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = $this->acefalia[$valor['acefalia']];
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '';
                        return($respuesta);
                        break;
                }
                break;
            case '5':
                switch($valor['documento_sw'])
                {
                    case '1':
                        $respuesta = $this->documento_sw[$valor['documento_sw']];
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = $this->documento_sw[$valor['documento_sw']];
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '';
                        return($respuesta);
                        break;
                }
                break;

            case '10':
                $organigrama_array = [];
                $consulta1 = InstCargo::leftJoin("inst_tipos_cargo AS a2", "a2.id", "=", "inst_cargos.tipo_cargo_id")
                    ->where("inst_cargos.estado", "=", 1)
                    ->where("inst_cargos.cargo_id", "=", $valor['cargo_id'])
                    ->select('inst_cargos.id', 'inst_cargos.tipo_cargo_id', 'inst_cargos.item_contrato', 'inst_cargos.nombre', 'inst_cargos.acefalia', 'a2.nombre AS tipo_cargo')
                    ->orderBy("inst_cargos.nombre")
                    ->get()
                    ->toArray();

                if(count($consulta1) > 0)
                {
                    foreach ($consulta1 as $row1)
                    {
                        if($row1['tipo_cargo_id'] == 1)
                        {
                            $name = $row1['tipo_cargo'] . ' ' . $row1['item_contrato'] . ' - ¿ACEFALO? ' . $this->acefalia[$row1['acefalia']];
                        }
                        else
                        {
                            $name = $row1['tipo_cargo'] . ' - ¿ACEFALO? ' . $this->acefalia[$row1['acefalia']];
                        }
                        $organigrama_array[] = [

                            'name'     => $name,
                            'title'    => $row1['nombre'],
                            'children' => $this->utilitarios(['tipo' => '10', 'cargo_id' => $row1['id']])
                        ];
                    }
                }
                return $organigrama_array;
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
                if($request->has('persona_id'))
                {
                    $this->reporte_data_1 = [
                        'persona_id'               => trim($request->input('persona_id')),
                        'f_marcacion_del'          => trim($request->input('f_marcacion_del')),
                        'f_marcacion_al'           => trim($request->input('f_marcacion_al')),
                        'lugar_dependencia_id'     => trim($request->input('lugar_dependencia_id')),
                        'unidad_desconcentrada_id' => trim($request->input('unidad_desconcentrada_id'))
                    ];
                    Excel::create('Marcaciones_' . date('Y-m-d_H-i-s'), function($excel){
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

                        $array_where = "$tabla1.persona_id=" . $this->reporte_data_1['persona_id'] . "";

                        if($this->reporte_data_1['f_marcacion_del'] != '')
                        {
                            $array_where .= " AND $tabla1.f_marcacion >= '" . $this->reporte_data_1['f_marcacion_del'] . "'";
                        }

                        if($this->reporte_data_1['f_marcacion_al'] != '')
                        {
                            $array_where .= " AND $tabla1.f_marcacion <= '" . $this->reporte_data_1['f_marcacion_al'] . " 23:59:59'";
                        }

                        if($this->reporte_data_1['lugar_dependencia_id'] != '')
                        {
                            $array_where .= " AND a3.lugar_dependencia_id = " . $this->reporte_data_1['lugar_dependencia_id'] . "";
                        }

                        if($this->reporte_data_1['unidad_desconcentrada_id'] != '')
                        {
                            $array_where .= " AND a2.unidad_desconcentrada_id = " . $this->reporte_data_1['unidad_desconcentrada_id'] . "";
                        }

                        $this->reporte_1 = RrhhLogMarcacion::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.biometrico_id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.unidad_desconcentrada_id")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                            ->whereRaw($array_where)
                            ->select(DB::raw($select))
                            ->orderBy("$tabla1.f_marcacion", 'ASC')
                            ->get()
                            ->toArray();

                        $excel->sheet('Marcaciones', function($sheet){
                            $sheet->row(1, [
                                'FECHA Y HORA',
                                'BIOMETRICO',
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

                            $sheet->setColumnFormat([
                                'A' => 'yyyy-mm-dd hh:mm:ss'
                            ]);

                            $sw = FALSE;

                            foreach($this->reporte_1 as $index => $row1)
                            {
                                $sheet->row($index+2, [
                                    $row1["f_marcacion"],
                                    "MP-" . $row1["codigo_af"],
                                    $row1["unidad_desconcentrada"],
                                    $row1["lugar_dependencia"]
                                ]);

                                if($sw)
                                {
                                    $sheet->row($index+2, function($row){
                                        $row->setBackground('#deeaf6');
                                        // $row->setFontColor('#9c0006');
                                    });

                                    $sw = FALSE;
                                }
                                else
                                {
                                    $sw = TRUE;
                                }
                            }

                            $sheet->cells('B1:D' . ($index + 2), function($cells){
                                $cells->setAlignment('center');
                            });

                            // $sheet->cells('A1:A' . ($index + 2), function($cells){
                            //     $cells->setAlignment('center');
                            // });

                            $sheet->setAutoSize(true);
                        });

                        // $excel->sheet('Cargos', function($sheet){
                        //     $sheet->row(1, [
                        //         'LUGAR DE DEPENDENCIA',
                        //         'AREA UNIDAD ORGANIZACIONAL',
                        //         '¿ACEFALO?',
                        //         'TIPO DE CARGO',
                        //         'NUMERO',
                        //         'CARGO'
                        //     ]);

                        //     $sheet->row(1, function($row){
                        //         $row->setBackground('#CCCCCC');
                        //         $row->setFontWeight('bold');
                        //         $row->setAlignment('center');
                        //     });

                        //     $sheet->freezeFirstRow();
                        //     $sheet->setAutoFilter();

                        //     foreach($this->reporte_1 as $index => $row1)
                        //     {
                        //         $sheet->row($index+2, [
                        //             $row1["lugar_dependencia"],
                        //             $row1["auo"],
                        //             $this->acefalia[$row1["acefalia"]],
                        //             $row1["tipo_cargo"],
                        //             $row1["item_contrato"],
                        //             $row1["nombre"]
                        //         ]);

                        //         if($row1["acefalia"] == 1)
                        //         {
                        //             $sheet->row($index+2, function($row){
                        //                 $row->setBackground('#ffc7ce');
                        //                 $row->setFontColor('#9c0006');
                        //             });
                        //         }
                        //     }

                        //     $sheet->cells('C1:D' . ($index + 2), function($cells){
                        //         $cells->setAlignment('center');
                        //     });

                        //     $sheet->setAutoSize(true);
                        // });
                    })->export('xlsx');
                }
                else
                {
                    return "SIN FUNCIONARIO";
                }
                break;
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

                // === PERMISOS ===
                    if(!in_array(['codigo' => '0804'], $this->permisos))
                    {
                        return "No tiene permiso para GENERAR REPORTES.";
                    }

                // === ANALISIS DE LAS VARIABLES ===
                    if( ! (($request->has('lugar_dependencia_id'))))
                    {
                        return "LUGAR DE DEPENDENCIA es obligatorio.";
                    }

                //=== CARGAR VARIABLES ===
                    $data1['lugar_dependencia_id'] = trim($request->input('lugar_dependencia_id'));

                //=== CONSULTA BASE DE DATOS ===
                    $tabla1 = "inst_cargos";
                    $tabla2 = "inst_tipos_cargo";
                    $tabla3 = "inst_auos";
                    $tabla4 = "inst_lugares_dependencia";
                    $tabla5 = "rrhh_funcionarios";
                    $tabla6 = "rrhh_personas";
                    $tabla7 = "inst_unidades_desconcentradas";
                    $tabla8 = "rrhh_horarios";

                    $array_where = "a7.lugar_dependencia_id = " . $data1['lugar_dependencia_id'];

                    $select = "
                        $tabla1.id,
                        $tabla1.auo_id,
                        $tabla1.tipo_cargo_id,
                        $tabla1.item_contrato,
                        $tabla1.acefalia,
                        $tabla1.nombre As cargo,

                        a2.nombre AS tipo_cargo,

                        a3.lugar_dependencia_id AS lugar_dependencia_id_cargo,
                        a3.nombre AS auo_cargo,

                        a4.nombre AS lugar_dependencia_cargo,

                        a5.id AS funcionario_id,
                        a5.persona_id,
                        a5.cargo_id,
                        a5.unidad_desconcentrada_id,
                        a5.horario_id_1,
                        a5.horario_id_2,
                        a5.situacion,
                        a5.documento_sw,
                        a5.f_ingreso,
                        a5.f_salida,
                        a5.sueldo,
                        a5.observaciones,
                        a5.documento_file,

                        a6.n_documento,
                        a6.nombre AS nombre_persona,
                        a6.ap_paterno,
                        a6.ap_materno,

                        a7.lugar_dependencia_id AS lugar_dependencia_id_funcionario,
                        a7.nombre AS ud_funcionario,

                        a8.nombre AS lugar_dependencia_funcionario,

                        a9.nombre AS horario_1,
                        a10.nombre AS horario_2
                    ";

                    $consulta1 = InstCargo::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_cargo_id")
                        ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.auo_id")
                        ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                        ->leftJoin("$tabla5 AS a5", "a5.cargo_id", "=", "$tabla1.id")
                        ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.persona_id")
                        ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a5.unidad_desconcentrada_id")
                        ->leftJoin("$tabla4 AS a8", "a8.id", "=", "a7.lugar_dependencia_id")
                        ->leftJoin("$tabla8 AS a9", "a9.id", "=", "a5.horario_id_1")
                        ->leftJoin("$tabla8 AS a10", "a10.id", "=", "a5.horario_id_2")
                        ->whereRaw($array_where)
                        ->select(DB::raw($select))
                        ->orderBy("a6.ap_paterno", "ASC")
                        ->orderBy("a6.ap_materno", "ASC")
                        ->orderBy("a6.nombre", "ASC")
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
                                    '¿ACEFALO?',
                                    'TIPO DE CARGO',
                                    'SITUACION',
                                    'NUMERO',
                                    '¿CON PDF?',

                                    'CI',
                                    'FUNCIONARIO',

                                    'FECHA DE INGRESO',
                                    'FECHA DE SALIDA',

                                    'SUELDO',

                                    'UNIDAD DESCONCENTRADA DEL FUNCIONARIO',
                                    'LUGAR DE DEPENDENCIA DEL FUNCIONARIO',

                                    'HORARIO 1',
                                    'HORARIO 2',

                                    'CARGO',
                                    'AREA UNIDAD ORGANIZACIONAL',
                                    'LUGAR DE DEPENDENCIA DEL CARGO',

                                    'OBSERVACIONES'
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
                                        $this->utilitarios(array('tipo' => '4', 'acefalia' => $row1["acefalia"])),
                                        $row1["tipo_cargo"],
                                        ($row1["situacion"] == '')? '' : $this->situacion[$row1["situacion"]],
                                        $row1["item_contrato"],
                                        $this->utilitarios(array('tipo' => '5', 'documento_sw' => $row1["documento_sw"])),

                                        $n_documento,
                                        $nombre_persona,

                                        $row1["f_ingreso"],
                                        $row1["f_salida"],

                                        $row1["sueldo"],

                                        $row1["ud_funcionario"],
                                        $row1["lugar_dependencia_funcionario"],

                                        $row1["horario_1"],
                                        $row1["horario_2"],

                                        $row1["cargo"],
                                        $row1["auo_cargo"],
                                        $row1["lugar_dependencia_cargo"],

                                        $row1["observaciones"]
                                    ]);

                                    if($row1["documento_sw"] == '2')
                                    {

                                        $sheet->getCell('F' . $c)
                                            ->getHyperlink()
                                            ->setUrl($this->link_pdf . '/' . $row1["documento_file"])
                                            ->setTooltip('Clic para ver el PDF');

                                        $sheet->getStyle('F' . $c)
                                             ->applyFromArray(array(
                                                'font' => array(
                                                    'color'     => ['rgb' => '0000FF'],
                                                    'underline' => 'single'
                                                )
                                            ));
                                    }

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

                                $sheet->cells('B1:F' . ($c), function($cells){
                                    $cells->setAlignment('center');
                                });

                                $sheet->cells('G2:G' . ($c), function($cells){
                                    $cells->setAlignment('right');
                                });

                                $sheet->cells('H1:H' . ($c), function($cells){
                                    $cells->setAlignment('left');
                                });

                                $sheet->cells('I1:J' . ($c), function($cells){
                                    $cells->setAlignment('center');
                                });

                                $sheet->cells('K2:K' . ($c), function($cells){
                                    $cells->setAlignment('right');
                                });

                                $sheet->cells('L1:R' . ($c), function($cells){
                                    $cells->setAlignment('center');
                                });

                                 $sheet->cells('S1:S' . ($c), function($cells){
                                    $cells->setAlignment('left');
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