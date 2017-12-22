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

use Maatwebsite\Excel\Facades\Excel;

use Exception;

class SolicitudSalidaController extends Controller
{
    private $estado;
    private $tipo_salida;
    private $con_sin_retorno;
    private $periodo;
    private $no_si;

    private $rol_id;
    private $permisos;

    private $reporte_1;
    private $reporte_data_1;

    public function __construct()
    {
        $this->middleware('auth');

        $this->estado = [
            '1' => 'HABILITADO',
            '2' => 'ANULADO'
        ];

        $this->tipo_salida = [
            '1' => 'OFICIAL',
            '2' => 'PARTICULAR',
            '4' => 'CUMPLEAÑOS'
        ];

        $this->con_sin_retorno = [
            '1' => 'CON RETORNO',
            '2' => 'SIN RETORNO'
        ];

        $this->periodo = [
            '1' => 'MAÑANA',
            '2' => 'TARDE'
        ];

        $this->no_si = [
            '1' => 'NO',
            '2' => 'SI'
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

        $funcionario_sw = FALSE;
        $persona_id     = Auth::user()->persona_id;
        if($this->persona_id != '')
        {
            $tabla1 = "rrhh_funcionarios";
            $tabla2 = "rrhh_personas";

            $tabla3 = "inst_unidades_desconcentradas";
            $tabla4 = "inst_lugares_dependencia";

            $tabla5 = "inst_cargos";
            $tabla6 = "inst_tipos_cargo";
            $tabla7 = "inst_auos";

            $tabla8 = "rrhh_horarios";

            $select = "
                $tabla1.id,
                $tabla1.persona_id,
                $tabla1.cargo_id,
                $tabla1.unidad_desconcentrada_id,
                $tabla1.horario_id_1,
                $tabla1.horario_id_2,
                $tabla1.situacion,
                $tabla1.documento_sw,
                $tabla1.f_ingreso,
                $tabla1.f_salida,
                $tabla1.sueldo,
                $tabla1.observaciones,
                $tabla1.documento_file,

                a2.n_documento,
                a2.nombre AS nombre_persona,
                a2.ap_paterno,
                a2.ap_materno,
                a2.sexo,
                a2.f_nacimiento,

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
                a7.nombre AS auo_cargo,

                a8.nombre AS lugar_dependencia_cargo,

                a9.nombre AS horario_1,
                a10.nombre AS horario_2
            ";

            $consulta1 = RrhhFuncionario::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.persona_id")
                ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.unidad_desconcentrada_id")
                ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                ->leftJoin("$tabla5 AS a5", "a5.id", "=", "$tabla1.cargo_id")
                ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.tipo_cargo_id")
                ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a5.auo_id")
                ->leftJoin("$tabla4 AS a8", "a8.id", "=", "a7.lugar_dependencia_id")
                ->leftJoin("$tabla8 AS a9", "a9.id", "=", "$tabla1.horario_id_1")
                ->leftJoin("$tabla8 AS a10", "a10.id", "=", "$tabla1.horario_id_2")
                ->where("$tabla1.persona_id", '=', $persona_id)
                ->first()
                ->toArray();

            if(count($consulta1) > 0)
            {
                $funcionario_sw = TRUE;
            }
        }

        if(in_array(['codigo' => '1001'], $this->permisos) && ($funcionario_sw))
        {
            $data = [
                'rol_id'                      => $this->rol_id,
                'permisos'                    => $this->permisos,
                'title'                       => 'Solicitud de salida',
                'home'                        => 'Inicio',
                'sistema'                     => 'Recursos humanos',
                'modulo'                      => 'Solicitud de salida',
                'title_table'                 => 'Solicitudes de salida por hora',
                'title_table_1'               => 'Solicitudes de salida por días',
                'estado_array'                => $this->estado,
                'tipo_salida_array'           => $this->tipo_salida,
                'con_sin_retorno_array'       => $this->con_sin_retorno,
                'periodo_array'               => $this->periodo,
                'no_si_array'                 => $this->no_si,$consulta1
                'funcionario_array'           => $consulta1,
                'funcionario_sw'              => $funcionario_sw,
                'tipo_salida_por_horas_array' => RrhhTipoSalida::where("lugar_dependencia_id", "=", $consulta1['lugar_dependencia_id_funcionario'])
                    ->where("estado", "=", '1')
                    ->where("tipo_cronograma", "=", '1')
                    ->select("id", "nombre", "tipo_salida")
                    ->orderBy("nombre")
                    ->get()
                    ->toArray(),
                'tipo_salida_por_dias_array'  => RrhhTipoSalida::where("lugar_dependencia_id", "=", $consulta1['lugar_dependencia_id_funcionario'])
                    ->where("estado", "=", '1')
                    ->where("tipo_cronograma", "=", '2')
                    ->select("id", "nombre", "tipo_salida")
                    ->orderBy("nombre")
                    ->get()
                    ->toArray()
            ];
            return view('rrhh.solicitud_salida.solicitud_salida')->with($data);
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
                $persona_id = Auth::user()->persona_id;

                if($persona_id != '')
                {
                    $jqgrid = new JqgridClass($request);

                    $tabla1 = "rrhh_salidas";
                    $tabla2 = "rrhh_tipos_salida";
                    $tabla3 = "rrhh_funcionarios";
                    $tabla4 = "rrhh_personas";

                    $select = "
                        $tabla1.id,
                        $tabla1.funcionario_id,
                        $tabla1.tipo_salida_id,
                        $tabla1.funcionario_id_superior,

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

                        a2.nombre AS papeleta_salida,
                        a2.tipo_cronograma,
                        a2.tipo_salida,

                        a3.persona_id,

                        a4.n_documento,
                        a4.nombre AS nombre_persona,
                        a4.ap_paterno,
                        a4.ap_materno
                    ";

                    $array_where = "a3.persona_id=" . $persona_id  . " AND a2.tipo_cronograma=1";

                    $array_where .= $jqgrid->getWhere();

                    $count = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                        ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.funcionario_id_superior")
                        ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.persona_id")
                        ->whereRaw($array_where)
                        ->count();

                    $limit_offset = $jqgrid->getLimitOffset($count);

                    $query = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                        ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.funcionario_id_superior")
                        ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.persona_id")
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
                            'funcionario_id'          => $row["funcionario_id"],
                            'tipo_salida_id'          => $row["tipo_salida_id"],
                            'funcionario_id_superior' => $row["funcionario_id_superior"],
                            'estado'                  => $row["estado"],
                            'n_horas'                 => $row["n_horas"],
                            'con_sin_retorno'         => $row["con_sin_retorno"],
                            'validar_superior'        => $row["validar_superior"],
                            'f_validar_superior'      => $row["f_validar_superior"],
                            'validar_rrhh'            => $row["validar_rrhh"],
                            'f_validar_rrhh'          => $row["f_validar_rrhh"],
                            'pdf'                     => $row["pdf"],
                            'papeleta_pdf'            => $row["papeleta_pdf"],
                            'tipo_cronograma'         => $row["tipo_cronograma"],
                            'tipo_salida'             => $row["tipo_salida"]
                        );

                        $respuesta['rows'][$i]['id'] = $row["id"];
                        $respuesta['rows'][$i]['cell'] = array(
                            '',

                            $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                            $this->utilitarios(array('tipo' => '2', 'validar_superior' => $row["validar_superior"])),
                            $this->utilitarios(array('tipo' => '3', 'validar_rrhh' => $row["validar_rrhh"])),
                            $this->utilitarios(array('tipo' => '4', 'pdf' => $row["pdf"])),

                            $row["papeleta_salida"],
                            ($row["tipo_salida"] == '')? '' : $this->tipo_salida[$row["tipo_salida"]],
                            $row["codigo"],

                            $row["n_documento"],
                            $row["nombre_persona"],
                            $row["ap_paterno"],
                            $row["ap_materno"],

                            $row["destino"],
                            $row["motivo"],

                            $row["f_salida"],
                            $row["h_salida"],
                            $row["h_retorno"],
                            $row["con_sin_retorno"],

                            //=== VARIABLES OCULTOS ===
                                json_encode($val_array)
                        );
                        $i++;
                    }
                    return json_encode($respuesta);
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
                        'titulo'     => '<div class="text-center"><strong>Horario</strong></div>',
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
                        if(!in_array(['codigo' => '1403'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '1402'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                // === VALIDATE ===
                    try
                    {
                        $validator = $this->validate($request,[
                            'lugar_dependencia_id'  => 'required',
                            'nombre'                => 'required|max:500',
                            'h_ingreso'             => 'required',
                            'h_salida'              => 'required',
                            'tolerancia'            => 'required|min:0',
                            'marcacion_ingreso_del' => 'required',
                            'marcacion_ingreso_al'  => 'required',
                            'marcacion_salida_del'  => 'required',
                            'marcacion_salida_al'   => 'required'
                        ],
                        [
                            'lugar_dependencia_id.required' => 'El campo LUGAR DE DEPENDENCIA es obligatorio.',

                            'nombre.required' => 'El campo NOMBRE es obligatorio.',
                            'nombre.max'     => 'El campo NOMBRE debe contener :max caracteres como máximo.',

                            'h_ingreso.required' => 'El campo HORA DE INGRESO es obligatorio.',

                            'h_salida.required' => 'El campo HORA DE SALIDA es obligatorio.',

                            'tolerancia.required' => 'El campo TOLERANCIA es obligatorio.',
                            'tolerancia.min'      => 'El campo TOLERANCIA debe tener al menos :min.',

                            'marcacion_ingreso_del.required' => 'El campo MARCACION DE INGRESO DEL es obligatorio.',

                            'marcacion_ingreso_al.required' => 'El campo MARCACION DE INGRESO AL es obligatorio.',

                            'marcacion_salida_del.required' => 'El campo MARCACION DE SALIDA DEL es obligatorio.',

                            'marcacion_salida_al.required' => 'El campo MARCACION DE SALIDA AL es obligatorio.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['estado']               = trim($request->input('estado'));
                    $data1['defecto']              = trim($request->input('defecto'));
                    $data1['tipo_horario']         = trim($request->input('tipo_horario'));
                    $data1['lugar_dependencia_id'] = trim($request->input('lugar_dependencia_id'));
                    $data1['nombre']               = strtoupper($util->getNoAcentoNoComilla(trim($request->input('nombre'))));

                    $data1['h_ingreso']  = trim($request->input('h_ingreso'));
                    $data1['h_salida']   = trim($request->input('h_salida'));
                    $data1['tolerancia'] = trim($request->input('tolerancia'));

                    $data1['marcacion_ingreso_del'] = trim($request->input('marcacion_ingreso_del'));
                    $data1['marcacion_ingreso_al']  = trim($request->input('marcacion_ingreso_al'));
                    $data1['marcacion_salida_del']  = trim($request->input('marcacion_salida_del'));
                    $data1['marcacion_salida_al']   = trim($request->input('marcacion_salida_al'));

                    $data1['lunes']     = trim($request->input('lunes', 1));
                    $data1['martes']    = trim($request->input('martes', 1));
                    $data1['miercoles'] = trim($request->input('miercoles', 1));
                    $data1['jueves']    = trim($request->input('jueves', 1));
                    $data1['viernes']   = trim($request->input('viernes', 1));
                    $data1['sabado']    = trim($request->input('sabado', 1));
                    $data1['domingo']   = trim($request->input('domingo', 1));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === VALIDAR POR CAMPO ===
                    $sw_dias = TRUE;

                    if($data1['lunes'] == '2')
                    {
                        $sw_dias = FALSE;
                    }
                    if($data1['martes'] == '2')
                    {
                        $sw_dias = FALSE;
                    }
                    if($data1['miercoles'] == '2')
                    {
                        $sw_dias = FALSE;
                    }
                    if($data1['jueves'] == '2')
                    {
                        $sw_dias = FALSE;
                    }
                    if($data1['viernes'] == '2')
                    {
                        $sw_dias = FALSE;
                    }
                    if($data1['sabado'] == '2')
                    {
                        $sw_dias = FALSE;
                    }
                    if($data1['domingo'] == '2')
                    {
                        $sw_dias = FALSE;
                    }

                    if($sw_dias)
                    {
                        $respuesta['respuesta'] .= "¡Por lo menos seleccione un día!";
                        return json_encode($respuesta);
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $consulta1 = RrhhHorario::where('nombre', '=', $data1['nombre'])
                            ->where('lugar_dependencia_id', '=', $data1['lugar_dependencia_id'])
                            ->count();

                        if($consulta1 < 1)
                        {
                            $iu                       = new RrhhHorario;
                            $iu->estado               = $data1['estado'];
                            $iu->defecto              = $data1['defecto'];
                            $iu->tipo_horario         = $data1['tipo_horario'];
                            $iu->lugar_dependencia_id = $data1['lugar_dependencia_id'];
                            $iu->nombre               = $data1['nombre'];

                            $iu->h_ingreso  = $data1['h_ingreso'];
                            $iu->h_salida   = $data1['h_salida'];
                            $iu->tolerancia = $data1['tolerancia'];

                            $iu->marcacion_ingreso_del = $data1['marcacion_ingreso_del'];
                            $iu->marcacion_ingreso_al  = $data1['marcacion_ingreso_al'];
                            $iu->marcacion_salida_del  = $data1['marcacion_salida_del'];
                            $iu->marcacion_salida_al   = $data1['marcacion_salida_al'];

                            $iu->lunes     = $data1['lunes'];
                            $iu->martes    = $data1['martes'];
                            $iu->miercoles = $data1['miercoles'];
                            $iu->jueves    = $data1['jueves'];
                            $iu->viernes   = $data1['viernes'];
                            $iu->sabado    = $data1['sabado'];
                            $iu->domingo   = $data1['domingo'];

                            $iu->save();

                            $respuesta['respuesta'] .= "El HORARIO fue registrado con éxito.";
                            $respuesta['sw']         = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El NOMBRE del horario ya fue registrado.";
                        }
                    }
                    else
                    {
                        $consulta1 = RrhhHorario::where('nombre', '=', $data1['nombre'])
                            ->where('lugar_dependencia_id', '=', $data1['lugar_dependencia_id'])
                            ->where('id', '<>', $id)
                            ->count();

                        if($consulta1 < 1)
                        {
                            $iu                       = RrhhHorario::find($id);
                            $iu->estado               = $data1['estado'];
                            $iu->defecto              = $data1['defecto'];
                            $iu->tipo_horario         = $data1['tipo_horario'];
                            $iu->lugar_dependencia_id = $data1['lugar_dependencia_id'];
                            $iu->nombre               = $data1['nombre'];

                            $iu->h_ingreso  = $data1['h_ingreso'];
                            $iu->h_salida   = $data1['h_salida'];
                            $iu->tolerancia = $data1['tolerancia'];

                            $iu->marcacion_ingreso_del = $data1['marcacion_ingreso_del'];
                            $iu->marcacion_ingreso_al  = $data1['marcacion_ingreso_al'];
                            $iu->marcacion_salida_del  = $data1['marcacion_salida_del'];
                            $iu->marcacion_salida_al   = $data1['marcacion_salida_al'];

                            $iu->lunes     = $data1['lunes'];
                            $iu->martes    = $data1['martes'];
                            $iu->miercoles = $data1['miercoles'];
                            $iu->jueves    = $data1['jueves'];
                            $iu->viernes   = $data1['viernes'];
                            $iu->sabado    = $data1['sabado'];
                            $iu->domingo   = $data1['domingo'];

                            $iu->save();

                            $respuesta['respuesta'] .= "El HORARIO se edito con éxito.";
                            $respuesta['sw']         = 1;
                            $respuesta['iu']         = 2;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El NOMBRE del horario ya fue registrado.";
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
                switch($valor['dias'])
                {
                    case '1':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->dias[$valor['dias']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-primary font-sm">' . $this->dias[$valor['dias']] . '</span>';
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
            default:
                break;
        }
    }
}