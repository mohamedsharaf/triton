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
use PDF;

use Exception;

class SolicitudSalidaController extends Controller
{
    private $estado;
    private $tipo_salida;
    private $con_sin_retorno;
    private $periodo;
    private $no_si;
    private $public_dir;

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
            '3' => 'VACACIONES',
            '4' => 'CUMPLEAÑOS',
            '5' => 'SIN GOCE DE HABER'
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

        $funcionario_sw = FALSE;
        $persona_id     = Auth::user()->persona_id;
        if($persona_id != '')
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
                ->select(DB::raw($select))
                ->first();

            if(count($consulta1) > 0)
            {
                $funcionario_sw = TRUE;
            }
        }

        if(in_array(['codigo' => '1001'], $this->permisos) && ($funcionario_sw))
        {
            // === PRIMER DIA DEL MES Y ULTIMO DIA DEL MES ===
                $f_actual = new \DateTime(date("Y-m-d"));
                $f_actual->modify('first day of this month');
                $primer_dia_mes_salida = $f_actual->format('Y-m-d');

                $f_actual = new \DateTime(date("Y-m-d"));
                $f_actual->modify('last day of this month');
                $ultimo_dia_mes_salida = $f_actual->format('Y-m-d');

                $tabla1 = "rrhh_salidas";
                $tabla2 = "rrhh_tipos_salida";

                $consulta2 = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                    ->where("$tabla1.persona_id", '=', $consulta1['persona_id'])
                    ->where("$tabla1.f_salida", '>=', $primer_dia_mes_salida)
                    ->where("$tabla1.f_salida", '<=', $ultimo_dia_mes_salida)
                    ->where("a2.tipo_salida", '=', '2')
                    ->where("a2.tipo_cronograma", '=', '1')
                    ->where("$tabla1.estado", '=', 1)
                    ->sum("$tabla1.n_horas");

            $data = [
                'rol_id'                      => $this->rol_id,
                'permisos'                    => $this->permisos,
                'title'                       => 'Solicitud de salida',
                'home'                        => 'Inicio',
                'sistema'                     => 'Recursos humanos',
                'modulo'                      => 'Solicitud de salida',
                'title_table'                 => 'Solicitudes de salida por hora',
                'title_table_1'               => 'Solicitudes de salida por días',
                'public_url'                  => $this->public_url,
                'estado_array'                => $this->estado,
                'tipo_salida_array'           => $this->tipo_salida,
                'con_sin_retorno_array'       => $this->con_sin_retorno,
                'periodo_array'               => $this->periodo,
                'no_si_array'                 => $this->no_si,
                'funcionario_array'           => $consulta1,
                'funcionario_sw'              => $funcionario_sw,
                'n_horas'                     => $consulta2,
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
                    $tabla3 = "rrhh_personas";

                    $select = "
                        $tabla1.id,
                        $tabla1.persona_id,
                        $tabla1.tipo_salida_id,
                        $tabla1.persona_id_superior,

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

                        a3.n_documento,
                        a3.nombre AS nombre_persona,
                        a3.ap_paterno,
                        a3.ap_materno
                    ";

                    $array_where = "$tabla1.persona_id=" . $persona_id  . " AND a2.tipo_cronograma=1";

                    $array_where .= $jqgrid->getWhere();

                    $count = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                        ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.persona_id_superior")
                        ->whereRaw($array_where)
                        ->count();

                    $limit_offset = $jqgrid->getLimitOffset($count);

                    $query = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                        ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.persona_id_superior")
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
                            'persona_id'          => $row["persona_id"],
                            'tipo_salida_id'      => $row["tipo_salida_id"],
                            'persona_id_superior' => $row["persona_id_superior"],
                            'estado'              => $row["estado"],
                            'n_horas'             => $row["n_horas"],
                            'con_sin_retorno'     => $row["con_sin_retorno"],
                            'validar_superior'    => $row["validar_superior"],
                            'f_validar_superior'  => $row["f_validar_superior"],
                            'validar_rrhh'        => $row["validar_rrhh"],
                            'f_validar_rrhh'      => $row["f_validar_rrhh"],
                            'pdf'                 => $row["pdf"],
                            'papeleta_pdf'        => $row["papeleta_pdf"],
                            'tipo_cronograma'     => $row["tipo_cronograma"],
                            'tipo_salida'         => $row["tipo_salida"]
                        );

                        $respuesta['rows'][$i]['id'] = $row["id"];
                        $respuesta['rows'][$i]['cell'] = array(
                            '',

                            $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                            $this->utilitarios(array('tipo' => '2', 'validar_superior' => $row["validar_superior"])),
                            $this->utilitarios(array('tipo' => '3', 'validar_rrhh' => $row["validar_rrhh"])),
                            $this->utilitarios(array('tipo' => '4', 'pdf' => $row["pdf"], 'id' => $row["id"], 'dia_hora' => 1)),

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
                            ($row["con_sin_retorno"] == '')? '' : $this->con_sin_retorno[$row["con_sin_retorno"]],

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
            case '2':
                $persona_id = Auth::user()->persona_id;

                if($persona_id != '')
                {
                    $jqgrid = new JqgridClass($request);

                    $tabla1 = "rrhh_salidas";
                    $tabla2 = "rrhh_tipos_salida";
                    $tabla3 = "rrhh_personas";

                    $select = "
                        $tabla1.id,
                        $tabla1.persona_id,
                        $tabla1.tipo_salida_id,
                        $tabla1.persona_id_superior,

                        $tabla1.estado,
                        $tabla1.codigo,
                        $tabla1.destino,
                        $tabla1.motivo,
                        $tabla1.f_salida,
                        $tabla1.f_retorno,

                        $tabla1.n_dias,
                        $tabla1.periodo_salida,
                        $tabla1.periodo_retorno,

                        $tabla1.validar_superior,
                        $tabla1.f_validar_superior,

                        $tabla1.validar_rrhh,
                        $tabla1.f_validar_rrhh,

                        $tabla1.pdf,
                        $tabla1.papeleta_pdf,

                        a2.nombre AS papeleta_salida,
                        a2.tipo_cronograma,
                        a2.tipo_salida,

                        a3.n_documento,
                        a3.nombre AS nombre_persona,
                        a3.ap_paterno,
                        a3.ap_materno
                    ";

                    $array_where = "$tabla1.persona_id=" . $persona_id  . " AND a2.tipo_cronograma=2";

                    $array_where .= $jqgrid->getWhere();

                    $count = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                        ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.persona_id_superior")
                        ->whereRaw($array_where)
                        ->count();

                    $limit_offset = $jqgrid->getLimitOffset($count);

                    $query = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                        ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.persona_id_superior")
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
                            'persona_id'          => $row["persona_id"],
                            'tipo_salida_id'      => $row["tipo_salida_id"],
                            'persona_id_superior' => $row["persona_id_superior"],
                            'estado'              => $row["estado"],
                            'n_dias'              => $row["n_dias"],
                            'periodo_salida'      => $row["periodo_salida"],
                            'periodo_retorno'     => $row["periodo_retorno"],
                            'validar_superior'    => $row["validar_superior"],
                            'f_validar_superior'  => $row["f_validar_superior"],
                            'validar_rrhh'        => $row["validar_rrhh"],
                            'f_validar_rrhh'      => $row["f_validar_rrhh"],
                            'pdf'                 => $row["pdf"],
                            'papeleta_pdf'        => $row["papeleta_pdf"],
                            'tipo_cronograma'     => $row["tipo_cronograma"],
                            'tipo_salida'         => $row["tipo_salida"]
                        );

                        $respuesta['rows'][$i]['id'] = $row["id"];
                        $respuesta['rows'][$i]['cell'] = array(
                            '',

                            $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                            $this->utilitarios(array('tipo' => '2', 'validar_superior' => $row["validar_superior"])),
                            $this->utilitarios(array('tipo' => '3', 'validar_rrhh' => $row["validar_rrhh"])),
                            $this->utilitarios(array('tipo' => '4', 'pdf' => $row["pdf"], 'id' => $row["id"], 'dia_hora' => 2)),

                            $row["papeleta_salida"],
                            ($row["tipo_salida"] == '')? '' : $this->tipo_salida[$row["tipo_salida"]],
                            $row["codigo"],
                            $row["n_dias"],

                            $row["n_documento"],
                            $row["nombre_persona"],
                            $row["ap_paterno"],
                            $row["ap_materno"],

                            $row["destino"],
                            $row["motivo"],

                            $row["f_salida"],
                            ($row["periodo_salida"] == '')? '' : $this->periodo[$row["periodo_salida"]],

                            $row["f_retorno"],
                            ($row["periodo_retorno"] == '')? '' : $this->periodo[$row["periodo_retorno"]],

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
                        'titulo'     => '<div class="text-center"><strong>Solicitud de salida</strong></div>',
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
                        if(!in_array(['codigo' => '1003'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '1002'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                // === VALIDATE ===
                    try
                    {
                        $validator = $this->validate($request,[
                            'tipo_salida_id'      => 'required',
                            'persona_id_superior' => 'required',
                            'destino'             => 'max:500',
                            'motivo'              => 'max:500',
                            'f_salida'            => 'required|date',
                            'h_salida'            => 'required'
                        ],
                        [
                            'tipo_salida_id.required' => 'El campo TIPO DE PAPELETA es obligatorio.',

                            'persona_id_superior.required' => 'El campo INMEDIATO SUPERIOR es obligatorio.',

                            'destino.max' => 'El campo DESTINATARIO debe contener :max caracteres como máximo.',

                            'motivo.max'     => 'El campo MOTIVO debe contener :max caracteres como máximo.',

                            'f_salida.required' => 'El campo FECHA DE SALIDA es obligatorio.',
                            'f_salida.date'     => 'El campo FECHA DE SALIDA no corresponde con una fecha válida.',

                            'h_salida.required' => 'El campo HORA DE SALIDA es obligatorio.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['persona_id']          = trim($request->input('persona_id'));
                    $data1['tipo_salida_id']      = trim($request->input('tipo_salida_id'));
                    $data1['persona_id_superior'] = trim($request->input('persona_id_superior'));
                    $data1['destino']             = strtoupper($util->getNoAcentoNoComilla(trim($request->input('destino'))));
                    $data1['motivo']              = strtoupper($util->getNoAcentoNoComilla(trim($request->input('motivo'))));
                    $data1['f_salida']            = trim($request->input('f_salida'));
                    $data1['h_salida']            = trim($request->input('h_salida'));
                    $data1['h_retorno']           = trim($request->input('h_retorno'));
                    $data1['con_sin_retorno']     = trim($request->input('con_sin_retorno'));
                    $data1['n_horas']             = '';

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === VALIDAR POR CAMPO ===
                    $persona_id = Auth::user()->persona_id;

                    if($persona_id != $data1['persona_id'])
                    {
                        $respuesta['respuesta'] .= "No se puede procesar su SOLICITUD DE SALIDA porque los datos corresponde a otra persona.";
                        return json_encode($respuesta);
                    }

                    $consulta1 = RrhhTipoSalida::where('id', '=', $data1['tipo_salida_id'])
                        ->select('lugar_dependencia_id', 'nombre', 'tipo_salida', 'tipo_cronograma', 'hd_mes')
                        ->first();

                    switch($consulta1['tipo_salida'])
                    {
                        case '1':
                            if($data1['destino'] == '')
                            {
                                $respuesta['respuesta'] .= "El campo DESTINO es obligatorio para tipo de salida OFICIAL.";
                                return json_encode($respuesta);
                            }

                            if($data1['motivo'] == '')
                            {
                                $respuesta['respuesta'] .= "El campo MOTIVO es obligatorio para tipo de salida OFICIAL.";
                                return json_encode($respuesta);
                            }
                            break;
                        case '2':
                            if($data1['h_retorno'] == '')
                            {
                                $respuesta['respuesta'] .= "El campo HORA DE RETORNO es obligatorio para tipo de salida PARTICULAR.";
                                return json_encode($respuesta);
                            }

                            // === PRIMER DIA DEL MES Y ULTIMO DIA DEL MES ===
                                $fecha_salida = new \DateTime($data1['f_salida']);
                                $fecha_salida->modify('first day of this month');
                                $primer_dia_mes_salida = $fecha_salida->format('Y-m-d');

                                $fecha_salida = new \DateTime($data1['f_salida']);
                                $fecha_salida->modify('last day of this month');
                                $ultimo_dia_mes_salida = $fecha_salida->format('Y-m-d');

                                if($opcion == 'n')
                                {
                                    $consulta2 = RrhhSalida::where('persona_id', '=', $data1['persona_id'])
                                        ->where('f_salida', '>=', $primer_dia_mes_salida)
                                        ->where('f_salida', '<=', $ultimo_dia_mes_salida)
                                        ->where('tipo_salida_id', '=', $data1['tipo_salida_id'])
                                        ->where('estado', '=', 1)
                                        ->sum('n_horas');
                                }
                                else
                                {
                                    $consulta2 = RrhhSalida::where('persona_id', '=', $data1['persona_id'])
                                        ->where('f_salida', '>=', $primer_dia_mes_salida)
                                        ->where('f_salida', '<=', $ultimo_dia_mes_salida)
                                        ->where('tipo_salida_id', '=', $data1['tipo_salida_id'])
                                        ->where('estado', '=', 1)
                                        ->where('id', '<>', $id)
                                        ->sum('n_horas');
                                }

                                if($consulta2 == '')
                                {
                                    $consulta2 = 0;
                                }

                            // === CALCULO DE HORAS SOLICITADO ===
                                $h_salida            = new \DateTime($data1['h_salida']);
                                $h_retorno           = new \DateTime($data1['h_retorno']);
                                $diferencia          = $h_salida->diff($h_retorno);
                                $hm_solicitados      = $diferencia->format("%H:%I");
                                $n_horas_solicitadas = $diferencia->format("%h") + $diferencia->format("%i")/60;

                            // === VERFIFICAR CANTIDAD DE HORAS DISPONIBLE Y LAS SOLICITADAS ===
                                if($consulta1['hd_mes'] >= round(($consulta2 + $n_horas_solicitadas), 2))
                                {
                                    $data1['n_horas'] = $n_horas_solicitadas;
                                }
                                else
                                {
                                    $respuesta['respuesta'] .= "Se sobrepasó " . round((($n_horas_solicitadas + $consulta2 - $consulta1['hd_mes']) * 60), 0) . " minutos. Recordarle que tiene " . ($consulta1['hd_mes'] * 60) . " minutos al mes.";
                                    return json_encode($respuesta);
                                }

                            break;
                        default:
                            break;
                    }

                    $consulta3 = RrhhFuncionario::where('persona_id', '=', $data1['persona_id'])
                        ->select('horario_id_1', 'horario_id_2')
                        ->first();

                    if(count($consulta3) > 0)
                    {
                        $sw_horario  = FALSE;
                        $dia_horario = FALSE;

                        $respuesta_horario     = '';
                        $respuesta_dia_horario = '';

                        $fh_salida  = $data1['f_salida'] . ' ' . $data1['h_salida'];
                        $fh_retorno = $data1['f_salida'] . ' ' . $data1['h_retorno'];

                        if($consulta3['horario_id_1'] != '')
                        {
                            $consulta4 = RrhhHorario::where('id', '=', $consulta3['horario_id_1'])
                                ->select('h_ingreso', 'h_salida', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo')
                                ->first();

                            if($data1['h_retorno'] != '')
                            {
                                $fh_horario_1_ingreso = $data1['f_salida'] . ' ' . $consulta4['h_ingreso'];
                                $fh_horario_1_salida  = $data1['f_salida'] . ' ' . $consulta4['h_salida'];

                                if((strtotime($fh_salida) >= strtotime($fh_horario_1_ingreso)) && (strtotime($fh_salida) <= strtotime($fh_horario_1_salida)))
                                {
                                    $sw_horario = TRUE;
                                }
                                else
                                {
                                    $respuesta_horario .= "PRIMER HORARIO: La hora de salida y retorno debe de estar compredido entre " . $consulta4['h_ingreso'] . " y " . $consulta4['h_salida'] . ".";
                                }
                            }
                            else
                            {
                                $sw_horario = TRUE;
                            }

                            switch(date('w', strtotime($data1['f_salida'])))
                            {
                                // === DOMINGO ===
                                case '0':
                                    if($consulta4['domingo'] == '2')
                                    {
                                        $dia_horario = TRUE;
                                    }
                                    else
                                    {
                                        $respuesta_dia_horario .= 'PRIMER HORARIO: En los DOMINGOS no se puede generar PAPELETA DE SALIDA.';
                                    }
                                    break;
                                // === LUNES ===
                                case '1':
                                    if($consulta4['lunes'] == '2')
                                    {
                                        $dia_horario = TRUE;
                                    }
                                    else
                                    {
                                        $respuesta_dia_horario .= 'PRIMER HORARIO: En los LUNES no se puede generar PAPELETA DE SALIDA.';
                                    }
                                    break;
                                // === MARTES ===
                                case '2':
                                    if($consulta4['martes'] == '2')
                                    {
                                        $dia_horario = TRUE;
                                    }
                                    else
                                    {
                                        $respuesta_dia_horario .= 'PRIMER HORARIO: En los MARTES no se puede generar PAPELETA DE SALIDA.';
                                    }
                                    break;
                                // === MIERCOLES ===
                                case '3':
                                    if($consulta4['miercoles'] == '2')
                                    {
                                        $dia_horario = TRUE;
                                    }
                                    else
                                    {
                                        $respuesta_dia_horario .= 'PRIMER HORARIO: En los MIERCOLES no se puede generar PAPELETA DE SALIDA.';
                                    }
                                    break;
                                // === JUEVES ===
                                case '4':
                                    if($consulta4['jueves'] == '2')
                                    {
                                        $dia_horario = TRUE;
                                    }
                                    else
                                    {
                                        $respuesta_dia_horario .= 'PRIMER HORARIO: En los JUEVES no se puede generar PAPELETA DE SALIDA.';
                                    }
                                    break;
                                // === VIERNES ===
                                case '5':
                                    if($consulta4['viernes'] == '2')
                                    {
                                        $dia_horario = TRUE;
                                    }
                                    else
                                    {
                                        $respuesta_dia_horario .= 'PRIMER HORARIO: En los VIERNES no se puede generar PAPELETA DE SALIDA.';
                                    }
                                    break;
                                // === SABADO ===
                                case '6':
                                    if($consulta4['sabado'] == '2')
                                    {
                                        $dia_horario = TRUE;
                                    }
                                    else
                                    {
                                        $respuesta_dia_horario .= 'PRIMER HORARIO: En los SABADOS no se puede generar PAPELETA DE SALIDA.';
                                    }
                                    break;
                                default:
                                    break;
                            }
                        }

                        if(!($sw_horario && $dia_horario))
                        {
                            if($consulta3['horario_id_2'] != '')
                            {
                                $consulta5 = RrhhHorario::where('id', '=', $consulta3['horario_id_2'])
                                    ->select('h_ingreso', 'h_salida', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo')
                                    ->first();

                                if($data1['h_retorno'] != '')
                                {
                                    $fh_horario_2_ingreso = $data1['f_salida'] . ' ' . $consulta5['h_ingreso'];
                                    $fh_horario_2_salida  = $data1['f_salida'] . ' ' . $consulta5['h_salida'];

                                    if((strtotime($fh_salida) >= strtotime($fh_horario_2_ingreso)) && (strtotime($fh_salida) <= strtotime($fh_horario_2_salida)))
                                    {
                                        $sw_horario = TRUE;
                                    }
                                    else
                                    {
                                        if($respuesta_horario == '')
                                        {
                                            $respuesta_horario .= "SEGUNDO HORARIO: La hora de salida y retorno debe de estar compredido entre " . $consulta5['h_ingreso'] . " y " . $consulta5['h_salida'] . ".";
                                        }
                                        else
                                        {
                                            $respuesta_horario .= "<br>SEGUNDO HORARIO: La hora de salida y retorno debe de estar compredido entre " . $consulta5['h_ingreso'] . " y " . $consulta5['h_salida'] . ".";
                                        }
                                    }
                                }
                                else
                                {
                                    $sw_horario = TRUE;
                                }

                                switch(date('w', strtotime($data1['f_salida'])))
                                {
                                    // === DOMINGO ===
                                    case '0':
                                        if($consulta5['domingo'] == '2')
                                        {
                                            $dia_horario = TRUE;
                                        }
                                        else
                                        {
                                            if($respuesta_dia_horario == '')
                                            {
                                                $respuesta_dia_horario .= "SEGUNDO HORARIO: En los DOMINGOS no se puede generar PAPELETA DE SALIDA.";
                                            }
                                            else
                                            {
                                                $respuesta_dia_horario .= "<br>SEGUNDO HORARIO: En los DOMINGOS no se puede generar PAPELETA DE SALIDA.";
                                            }
                                        }
                                        break;
                                    // === LUNES ===
                                    case '1':
                                        if($consulta5['lunes'] == '2')
                                        {
                                            $dia_horario = TRUE;
                                        }
                                        else
                                        {
                                            if($respuesta_dia_horario == '')
                                            {
                                                $respuesta_dia_horario .= "SEGUNDO HORARIO: En los LUNES no se puede generar PAPELETA DE SALIDA.";
                                            }
                                            else
                                            {
                                                $respuesta_dia_horario .= "<br>SEGUNDO HORARIO: En los LUNES no se puede generar PAPELETA DE SALIDA.";
                                            }
                                        }
                                        break;
                                    // === MARTES ===
                                    case '2':
                                        if($consulta5['martes'] == '2')
                                        {
                                            $dia_horario = TRUE;
                                        }
                                        else
                                        {
                                            if($respuesta_dia_horario == '')
                                            {
                                                $respuesta_dia_horario .= "SEGUNDO HORARIO: En los MARTES no se puede generar PAPELETA DE SALIDA.";
                                            }
                                            else
                                            {
                                                $respuesta_dia_horario .= "<br>SEGUNDO HORARIO: En los MARTES no se puede generar PAPELETA DE SALIDA.";
                                            }
                                        }
                                        break;
                                    // === MIERCOLES ===
                                    case '3':
                                        if($consulta5['miercoles'] == '2')
                                        {
                                            $dia_horario = TRUE;
                                        }
                                        else
                                        {
                                            if($respuesta_dia_horario == '')
                                            {
                                                $respuesta_dia_horario .= "SEGUNDO HORARIO: En los MIERCOLES no se puede generar PAPELETA DE SALIDA.";
                                            }
                                            else
                                            {
                                                $respuesta_dia_horario .= "<br>SEGUNDO HORARIO: En los MIERCOLES no se puede generar PAPELETA DE SALIDA.";
                                            }
                                        }
                                        break;
                                    // === JUEVES ===
                                    case '4':
                                        if($consulta5['jueves'] == '2')
                                        {
                                            $dia_horario = TRUE;
                                        }
                                        else
                                        {
                                            if($respuesta_dia_horario == '')
                                            {
                                                $respuesta_dia_horario .= "SEGUNDO HORARIO: En los JUEVES no se puede generar PAPELETA DE SALIDA.";
                                            }
                                            else
                                            {
                                                $respuesta_dia_horario .= "<br>SEGUNDO HORARIO: En los JUEVES no se puede generar PAPELETA DE SALIDA.";
                                            }
                                        }
                                        break;
                                    // === VIERNES ===
                                    case '5':
                                        if($consulta5['viernes'] == '2')
                                        {
                                            $dia_horario = TRUE;
                                        }
                                        else
                                        {
                                            if($respuesta_dia_horario == '')
                                            {
                                                $respuesta_dia_horario .= "SEGUNDO HORARIO: En los VIERNES no se puede generar PAPELETA DE SALIDA.";
                                            }
                                            else
                                            {
                                                $respuesta_dia_horario .= "<br>SEGUNDO HORARIO: En los VIERNES no se puede generar PAPELETA DE SALIDA.";
                                            }
                                        }
                                        break;
                                    // === SABADO ===
                                    case '6':
                                        if($consulta5['sabado'] == '2')
                                        {
                                            $dia_horario = TRUE;
                                        }
                                        else
                                        {
                                            if($respuesta_dia_horario == '')
                                            {
                                                $respuesta_dia_horario .= "SEGUNDO HORARIO: En los SABADO no se puede generar PAPELETA DE SALIDA.";
                                            }
                                            else
                                            {
                                                $respuesta_dia_horario .= "<br>SEGUNDO HORARIO: En los SABADO no se puede generar PAPELETA DE SALIDA.";
                                            }
                                        }
                                        break;
                                    default:
                                        break;
                                }

                                if( ! ($sw_horario && $dia_horario))
                                {
                                    if($respuesta_horario != '' && $respuesta_dia_horario != '')
                                    {
                                        $respuesta['respuesta'] .= $respuesta_horario . "<br>" . $respuesta_dia_horario;
                                    }
                                    else if($respuesta_horario != '')
                                    {
                                        $respuesta['respuesta'] .= $respuesta_horario;
                                    }
                                    else
                                    {
                                        $respuesta['respuesta'] .= $respuesta_dia_horario;
                                    }

                                    return json_encode($respuesta);
                                }
                            }
                            else
                            {
                                if($respuesta_horario != '' && $respuesta_dia_horario != '')
                                {
                                    $respuesta['respuesta'] .= $respuesta_horario . "<br>" . $respuesta_dia_horario;
                                }
                                else if($respuesta_horario != '')
                                {
                                    $respuesta['respuesta'] .= $respuesta_horario;
                                }
                                else
                                {
                                    $respuesta['respuesta'] .= $respuesta_dia_horario;
                                }

                                return json_encode($respuesta);
                            }
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "Usted no es funcionario del MINISTERIO PUBLICO.";
                        return json_encode($respuesta);
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $iu                      = new RrhhSalida;
                        $iu->persona_id          = $data1['persona_id'];
                        $iu->tipo_salida_id      = $data1['tipo_salida_id'];
                        $iu->persona_id_superior = $data1['persona_id_superior'];

                        $anio = date('Y', strtotime($data1['f_salida']));

                        $iu->codigo = str_pad((RrhhSalida::whereRaw("date_part('year', f_salida)='" . $anio . "'")->count())+1, 6, "0", STR_PAD_LEFT) . "/" . $anio;

                        $iu->destino   = $data1['destino'];
                        $iu->motivo    = $data1['motivo'];
                        $iu->f_salida  = $data1['f_salida'];
                        $iu->h_salida  = $data1['h_salida'];
                        $iu->h_retorno = $data1['h_retorno'];

                        $iu->n_horas         = $data1['n_horas'];
                        $iu->con_sin_retorno = $data1['con_sin_retorno'];

                        $iu->save();

                        $respuesta['respuesta'] .= "La SALIDA fue registrada y enviada para su validación.";
                        $respuesta['sw']         = 1;
                    }
                    else
                    {
                        $consulta6 = RrhhSalida::where('id', '=', $id)
                            ->first();

                        if(date('Y', strtotime($consulta6['f_salida'])) == date('Y', strtotime($data1['f_salida'])))
                        {
                            if(($consulta6['validar_superior'] == '1') && ($consulta6['validar_rrhh'] == '1'))
                            {
                                $iu                       = RrhhSalida::find($id);
                                $iu->persona_id          = $data1['persona_id'];
                                $iu->tipo_salida_id      = $data1['tipo_salida_id'];
                                $iu->persona_id_superior = $data1['persona_id_superior'];

                                $iu->destino   = $data1['destino'];
                                $iu->motivo    = $data1['motivo'];
                                $iu->f_salida  = $data1['f_salida'];
                                $iu->h_salida  = $data1['h_salida'];
                                $iu->h_retorno = $data1['h_retorno'];

                                $iu->n_horas         = $data1['n_horas'];
                                $iu->con_sin_retorno = $data1['con_sin_retorno'];

                                $iu->save();

                                $respuesta['respuesta'] .= "La SALIDA se edito con éxito.";
                                $respuesta['sw']         = 1;
                                $respuesta['iu']         = 2;
                            }
                            else
                            {
                                $respuesta['respuesta'] .= "No se puede editar porque ya fue validado. Favor consulte con el personal de Recursos Humanos.";
                            }
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "No se puede cambiar el AÑO de la FECHA DE SALIDA.";
                        }
                    }
                return json_encode($respuesta);
                break;

            // === INSERT UPDATE ===
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
                        'titulo'     => '<div class="text-center"><strong>Solicitud de salida</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );
                    $opcion      = 'n';
                    $anio_actual = date('Y');

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if($id != '')
                    {
                        $opcion = 'e';
                        if(!in_array(['codigo' => '1003'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '1002'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                // === VALIDATE ===
                    try
                    {
                        $validator = $this->validate($request,[
                            'tipo_salida_id'      => 'required',
                            'persona_id_superior' => 'required',
                            'destino'             => 'max:500',
                            'motivo'              => 'max:500',
                            'f_salida'            => 'date',
                            'f_retorno'           => 'date'
                        ],
                        [
                            'tipo_salida_id.required' => 'El campo TIPO DE PAPELETA es obligatorio.',

                            'persona_id_superior.required' => 'El campo INMEDIATO SUPERIOR es obligatorio.',

                            'destino.max' => 'El campo DESTINATARIO debe contener :max caracteres como máximo.',

                            'motivo.max' => 'El campo MOTIVO debe contener :max caracteres como máximo.',

                            'f_salida.date' => 'El campo FECHA DE SALIDA no corresponde con una fecha válida.',

                            'f_retorno.date' => 'El campo FECHA DE RETORNO no corresponde con una fecha válida.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['persona_id']          = trim($request->input('persona_id'));
                    $data1['tipo_salida_id']      = trim($request->input('tipo_salida_id'));
                    $data1['persona_id_superior'] = trim($request->input('persona_id_superior'));
                    $data1['destino']             = strtoupper($util->getNoAcentoNoComilla(trim($request->input('destino'))));
                    $data1['motivo']              = strtoupper($util->getNoAcentoNoComilla(trim($request->input('motivo'))));
                    $data1['f_salida']            = trim($request->input('f_salida'));
                    $data1['f_retorno']           = trim($request->input('f_retorno'));
                    $data1['n_dias']              = trim($request->input('n_dias'));
                    $data1['periodo_salida']      = trim($request->input('periodo_salida'));
                    $data1['periodo_retorno']     = trim($request->input('periodo_retorno'));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === VALIDAR POR CAMPO ===
                    $persona_id = Auth::user()->persona_id;

                    if($persona_id != $data1['persona_id'])
                    {
                        $respuesta['respuesta'] .= "No se puede procesar su SOLICITUD DE SALIDA porque los datos corresponde a otra persona.";
                        return json_encode($respuesta);
                    }

                    // === INFORMACION DEL FUNCIONARIO ===
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
                            ->select(DB::raw($select))
                            ->first();

                        if(!(count($consulta1) > 0))
                        {
                            $respuesta['respuesta'] .= "Usted no es funcionario del MINISTERIO PUBLICO.";
                            return json_encode($respuesta);
                        }


                    $consulta2 = RrhhTipoSalida::where('id', '=', $data1['tipo_salida_id'])
                            ->select('lugar_dependencia_id', 'nombre', 'tipo_salida', 'tipo_cronograma', 'hd_mes')
                            ->first();

                    switch($consulta2['tipo_salida'])
                    {
                        case '1':
                            if($data1['destino'] == '')
                            {
                                $respuesta['respuesta'] .= "El campo DESTINO es obligatorio para tipo de salida OFICIAL.";
                                return json_encode($respuesta);
                            }

                            if($data1['motivo'] == '')
                            {
                                $respuesta['respuesta'] .= "El campo MOTIVO es obligatorio para tipo de salida OFICIAL.";
                                return json_encode($respuesta);
                            }

                            if($data1['f_salida'] == '')
                            {
                                $respuesta['respuesta'] .= "El campo FECHA DE SALIDA es obligatorio para tipo de salida OFICIAL.";
                                return json_encode($respuesta);
                            }

                            if($data1['f_retorno'] == '')
                            {
                                $respuesta['respuesta'] .= "El campo FECHA DE RETORNO es obligatorio para tipo de salida OFICIAL.";
                                return json_encode($respuesta);
                            }
                            break;
                        case '3':
                            if($data1['f_salida'] == '')
                            {
                                $respuesta['respuesta'] .= "El campo FECHA DE SALIDA es obligatorio para tipo de salida VACACIONES.";
                                return json_encode($respuesta);
                            }

                            if($data1['f_retorno'] == '')
                            {
                                $respuesta['respuesta'] .= "El campo FECHA DE RETORNO es obligatorio para tipo de salida VACACIONES.";
                                return json_encode($respuesta);
                            }
                            break;
                        case '4':
                            if($consulta1['f_nacimiento'] != '')
                            {
                                $f_cumple = $anio_actual . '-' . date('m-d', strtotime($consulta1['f_nacimiento']));

                                if($opcion == 'n')
                                {
                                    $consulta6 = RrhhSalida::where('persona_id', '=', $persona_id)
                                        ->whereRaw("date_part('year', f_salida)='" . $anio_actual . "'")
                                        ->where('tipo_salida_id', '=', $data1['tipo_salida_id'])
                                        ->first();
                                }
                                else
                                {
                                    $consulta6 = RrhhSalida::where('persona_id', '=', $persona_id)
                                        ->whereRaw("date_part('year', f_salida)='" . $anio_actual . "'")
                                        ->where('tipo_salida_id', '=', $data1['tipo_salida_id'])
                                        ->where('id', '<>', $id)
                                        ->first();
                                }

                                if(count($consulta6) > 0)
                                {
                                    $respuesta['respuesta'] .= "Ya se genero PAPELETA DE CUMPLEAÑOS para la gestión " . $anio_actual . ".";
                                    return json_encode($respuesta);
                                }

                                $data1['f_salida']       = $f_cumple;
                                $data1['f_retorno']      = $f_cumple;
                                $data1['n_dias']         = 0.5;
                                $data1['periodo_salida'] = 2;
                            }
                            else
                            {
                                $respuesta['respuesta'] .= "No registro su fecha de nacimiento.";
                                return json_encode($respuesta);
                            }
                            break;
                        case '5':
                            if($data1['f_salida'] == '')
                            {
                                $respuesta['respuesta'] .= "El campo FECHA DE SALIDA es obligatorio para tipo de salida VACACIONES SIN GOCE DE HABER.";
                                return json_encode($respuesta);
                            }

                            if($data1['f_retorno'] == '')
                            {
                                $respuesta['respuesta'] .= "El campo FECHA DE RETORNO es obligatorio para tipo de salida VACACIONES SIN GOCE DE HABER.";
                                return json_encode($respuesta);
                            }
                            break;
                        default:
                            break;
                    }

                    if( ! (strtotime($data1['f_retorno']) >= strtotime($data1['f_salida'])))
                    {
                        $respuesta['respuesta'] .= "La FECHA DE RETORNO " . $data1['f_retorno'] . " debe de ser mayor o igual que la FECHA DE SALIDA " . $data1['f_salida'] . ".";
                        return json_encode($respuesta);
                    }

                    // === CONTAR NUMERO DE DIAS ===
                        $numero_dias = (strtotime($data1['f_retorno']) - strtotime($data1['f_salida']))/86400 +1;

                        $f_acu   = $data1['f_salida'];
                        $f_rango = array();

                        for($i=0; $i < $numero_dias; $i++)
                        {
                            $f_rango[$f_acu] = $f_acu;
                            $f_acu           = date("Y-m-d", strtotime($f_acu . "+ 1 days"));
                        }

                    $consulta3 = RrhhFthc::where('lugar_dependencia_id', '=', $consulta1['lugar_dependencia_id_funcionario'])
                        ->where('fecha', '>=', $data1['f_salida'])
                        ->where('fecha', '<=', $data1['f_retorno'])
                        ->where('estado', '=', 1)
                        ->select('unidad_desconcentrada_id', 'horario_id', 'fecha', 'nombre', 'tipo_fthc', 'tipo_horario', 'sexo')
                        ->get()
                        ->toArray();

                    $mensaje_fthc = '';
                    if(count($consulta3) > 0)
                    {
                        foreach ($consulta3 as $row3)
                        {
                            switch($row3['tipo_fthc'])
                            {
                                // === FERIADO ===
                                case '1':
                                    if(($row3['unidad_desconcentrada_id'] == '') || ($consulta1['unidad_desconcentrada_id'] == $row3['unidad_desconcentrada_id']))
                                    {
                                        unset($f_rango[$row3['fecha']]);
                                        $numero_dias = $numero_dias - 1;

                                        if($mensaje_fthc == '')
                                        {
                                            $mensaje_fthc .= "El " . $row3['fecha'] . " es " . $row3['nombre'] . ".";
                                        }
                                        else
                                        {
                                            $mensaje_fthc .= "<br>El " . $row3['fecha'] . " es " . $row3['nombre'] . ".";
                                        }

                                        if($numero_dias <= 0)
                                        {
                                            $respuesta['respuesta'] .= $mensaje_fthc;
                                            return json_encode($respuesta);
                                        }
                                    }
                                    break;
                                // === TOLERANCIA ===
                                case '2':
                                    # code...
                                    break;
                                // === HORARIO CONTINUO ===
                                case '3':
                                    # code...
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }
                    }

                    if($consulta1['horario_id_1'] != '')
                    {
                        $consulta4 = RrhhHorario::where('id', '=', $consulta1['horario_id_1'])
                            ->select('h_ingreso', 'h_salida', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo')
                            ->first();

                        $respuesta_dia_horario = $mensaje_fthc;

                        $f_rango_h = $f_rango;

                        foreach($f_rango_h as $row_f_rango)
                        {
                            switch(date('w', strtotime($row_f_rango)))
                            {
                                // === DOMINGO ===
                                case '0':
                                    if($consulta4['domingo'] == '1')
                                    {
                                        unset($f_rango[$row_f_rango]);
                                        $numero_dias = $numero_dias - 1;

                                        if($respuesta_dia_horario == '')
                                        {
                                            $respuesta_dia_horario .= "El " . $row_f_rango . " es DOMINGO y no es día laborar.";
                                        }
                                        else
                                        {
                                            $respuesta_dia_horario .= "<br>El " . $row_f_rango . " es DOMINGO y no es día laborar.";
                                        }
                                    }
                                    break;
                                // === LUNES ===
                                case '1':
                                    if($consulta4['lunes'] == '1')
                                    {
                                        unset($f_rango[$row_f_rango]);
                                        $numero_dias = $numero_dias - 1;

                                        if($respuesta_dia_horario == '')
                                        {
                                            $respuesta_dia_horario .= "El " . $row_f_rango . " es LUNES y no es día laborar.";
                                        }
                                        else
                                        {
                                            $respuesta_dia_horario .= "<br>El " . $row_f_rango . " es LUNES y no es día laborar.";
                                        }
                                    }
                                    break;
                                // === MARTES ===
                                case '2':
                                    if($consulta4['martes'] == '1')
                                    {
                                        unset($f_rango[$row_f_rango]);
                                        $numero_dias = $numero_dias - 1;

                                        if($respuesta_dia_horario == '')
                                        {
                                            $respuesta_dia_horario .= "El " . $row_f_rango . " es MARTES y no es día laborar.";
                                        }
                                        else
                                        {
                                            $respuesta_dia_horario .= "<br>El " . $row_f_rango . " es MARTES y no es día laborar.";
                                        }
                                    }
                                    break;
                                // === MIERCOLES ===
                                case '3':
                                    if($consulta4['miercoles'] == '1')
                                    {
                                        unset($f_rango[$row_f_rango]);
                                        $numero_dias = $numero_dias - 1;

                                        if($respuesta_dia_horario == '')
                                        {
                                            $respuesta_dia_horario .= "El " . $row_f_rango . " es MIERCOLES y no es día laborar.";
                                        }
                                        else
                                        {
                                            $respuesta_dia_horario .= "<br>El " . $row_f_rango . " es MIERCOLES y no es día laborar.";
                                        }
                                    }
                                    break;
                                // === JUEVES ===
                                case '4':
                                    if($consulta4['jueves'] == '1')
                                    {
                                        unset($f_rango[$row_f_rango]);
                                        $numero_dias = $numero_dias - 1;

                                        if($respuesta_dia_horario == '')
                                        {
                                            $respuesta_dia_horario .= "El " . $row_f_rango . " es JUEVES y no es día laborar.";
                                        }
                                        else
                                        {
                                            $respuesta_dia_horario .= "<br>El " . $row_f_rango . " es JUEVES y no es día laborar.";
                                        }
                                    }
                                    break;
                                // === VIERNES ===
                                case '5':
                                    if($consulta4['viernes'] == '1')
                                    {
                                        unset($f_rango[$row_f_rango]);
                                        $numero_dias = $numero_dias - 1;

                                        if($respuesta_dia_horario == '')
                                        {
                                            $respuesta_dia_horario .= "El " . $row_f_rango . " es VIERNES y no es día laborar.";
                                        }
                                        else
                                        {
                                            $respuesta_dia_horario .= "<br>El " . $row_f_rango . " es VIERNES y no es día laborar.";
                                        }
                                    }
                                    break;
                                // === SABADO ===
                                case '6':
                                    if($consulta4['sabado'] == '1')
                                    {
                                        unset($f_rango[$row_f_rango]);
                                        $numero_dias = $numero_dias - 1;

                                        if($respuesta_dia_horario == '')
                                        {
                                            $respuesta_dia_horario .= "El " . $row_f_rango . " es SABADO y no es día laborar.";
                                        }
                                        else
                                        {
                                            $respuesta_dia_horario .= "<br>El " . $row_f_rango . " es SABADO y no es día laborar.";
                                        }
                                    }
                                    break;
                                default:
                                    break;
                            }
                        }

                        if($numero_dias <= 0)
                        {
                            $respuesta['respuesta'] .= $respuesta_dia_horario;
                            return json_encode($respuesta);
                        }
                    }

                    if($consulta2['tipo_salida'] != '4')
                    {
                        if($data1['f_salida'] == $data1['f_retorno'])
                        {
                            if(!((($data1['periodo_salida'] == '') && ($data1['periodo_retorno'] == '')) || (($data1['periodo_salida'] != '') && ($data1['periodo_retorno'] != ''))))
                            {
                                $numero_dias = $numero_dias - 0.5;
                            }
                        }
                        else
                        {
                            if($data1['periodo_salida'] != '')
                            {
                                $numero_dias = $numero_dias - 0.5;
                            }

                            if($data1['periodo_retorno'] != '')
                            {
                                $numero_dias = $numero_dias - 0.5;
                            }
                        }

                        $data1['n_dias'] = $numero_dias;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $iu                      = new RrhhSalida;
                        $iu->persona_id          = $data1['persona_id'];
                        $iu->tipo_salida_id      = $data1['tipo_salida_id'];
                        $iu->persona_id_superior = $data1['persona_id_superior'];

                        $iu->codigo = str_pad((RrhhSalida::whereRaw("date_part('year', f_salida)='" . $anio_actual . "'")->count())+1, 6, "0", STR_PAD_LEFT) . "/" . $anio_actual;

                        $iu->destino   = $data1['destino'];
                        $iu->motivo    = $data1['motivo'];
                        $iu->f_salida  = $data1['f_salida'];
                        $iu->f_retorno  = $data1['f_retorno'];

                        $iu->n_dias = $data1['n_dias'];

                        $iu->periodo_salida  = $data1['periodo_salida'];
                        $iu->periodo_retorno = $data1['periodo_retorno'];

                        $iu->save();

                        $respuesta['respuesta'] .= "La SALIDA fue registrada y enviada para su validación.";
                        $respuesta['sw']         = 1;
                    }
                    else
                    {
                        $consulta6 = RrhhSalida::where('id', '=', $id)
                            ->first();

                        if(date('Y', strtotime($consulta6['f_salida'])) == date('Y', strtotime($data1['f_salida'])))
                        {
                            if(($consulta6['validar_superior'] == '1') && ($consulta6['validar_rrhh'] == '1'))
                            {
                                $iu                       = RrhhSalida::find($id);
                                $iu->persona_id          = $data1['persona_id'];
                                $iu->tipo_salida_id      = $data1['tipo_salida_id'];
                                $iu->persona_id_superior = $data1['persona_id_superior'];

                                $iu->destino   = $data1['destino'];
                                $iu->motivo    = $data1['motivo'];
                                $iu->f_salida  = $data1['f_salida'];
                                $iu->f_retorno  = $data1['f_retorno'];

                                $iu->n_dias = $data1['n_dias'];

                                $iu->periodo_salida  = $data1['periodo_salida'];
                                $iu->periodo_retorno = $data1['periodo_retorno'];

                                $iu->save();

                                $respuesta['respuesta'] .= "La SALIDA se edito con éxito.";
                                $respuesta['sw']         = 1;
                                $respuesta['iu']         = 2;
                            }
                            else
                            {
                                $respuesta['respuesta'] .= "No se puede editar porque ya fue validado. Favor consulte con el personal de Recursos Humanos.";
                            }
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "No se puede cambiar el AÑO de la FECHA DE SALIDA.";
                        }
                    }
                return json_encode($respuesta);
                break;

            // === HABILITAR / ANULAR PAPELETA DE SALIDA ===
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
                        'titulo'     => '<div class="text-center"><strong>Papeleta de Salida</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );
                    $opcion      = 'n';
                    $anio_actual = date('Y');

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if($id != '')
                    {
                        $opcion = 'e';
                        if(!in_array(['codigo' => '1003'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '1002'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                //=== OPERACION ===
                    $data1['estado']   = trim($request->input('estado'));
                    $data1['dia_hora'] = trim($request->input('dia_hora'));

                // === MODIFICAR VALORES ===
                    $consulta1 = RrhhSalida::where('id', '=', $id)
                        ->where('estado', '=', $data1['estado'])
                        ->first();

                    $consulta2 = RrhhSalida::where('id', '=', $id)
                        ->first();

                    if(!(($consulta2['validar_superior'] == '2') || ($consulta2['validar_rrhh'] == '2')))
                    {
                        if(!(count($consulta1) > 0))
                        {
                            $iu         = RrhhSalida::find($id);
                            $iu->estado = $data1['estado'];

                            $iu->save();

                            if($data1['estado'] == '1')
                            {
                                $respuesta['respuesta'] .= "La PAPELETA DE SALIDA fue HABILITADA.";
                            }
                            else
                            {
                                $respuesta['respuesta'] .= "La PAPELETA DE SALIDA fue ANULO.";
                            }
                            $respuesta['sw']        = 1;
                        }
                        else
                        {
                            if($data1['estado'] == '1')
                            {
                                $respuesta['respuesta'] .= "La PAPELETA DE SALIDA ya fue HABILITADA.";
                            }
                            else
                            {
                                $respuesta['respuesta'] .= "La PAPELETA DE SALIDA ya fue ANULADA.";
                            }
                        }
                    }
                    else
                    {
                        if($data1['estado'] == '1')
                        {
                            $respuesta['respuesta'] .= "La PAPELETA DE SALIDA ya no se puede HABILITADA.<br>Porque se VALIDO.";
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "La PAPELETA DE SALIDA ya no se puede ANULADA.<br>Porque se VALIDO.";
                        }
                    }

                    $respuesta['dia_hora']  = $data1['dia_hora'];
                return json_encode($respuesta);
                break;

            // === UPLOAD IMAGE ===
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
                        'titulo'     => '<div class="text-center"><strong>SUBIR DOCUMENTO</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'error_sw'   => 1
                    );
                    $opcion = 'n';

                // === PERMISOS ===
                    $id       = trim($request->input('id'));
                    $dia_hora = trim($request->input('dia_hora'));
                    if($id != '')
                    {
                        $opcion = 'e';
                        if(!in_array(['codigo' => '1003'], $this->permisos))
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
                    $consulta1 = RrhhSalida::where('id', '=', $id)
                        ->select('papeleta_pdf')
                        ->first();

                    $dir_doc = "storage/rrhh/salidas/solicitud_salida";

                    if($consulta1['papeleta_pdf'] != '')
                    {
                        if(file_exists(public_path($dir_doc) . '/' . $consulta1['papeleta_pdf']))
                        {
                            unlink(public_path($dir_doc) . '/' . $consulta1['papeleta_pdf']);
                        }
                    }

                    if($request->hasFile('file'))
                    {
                        $archivo           = $request->file('file');
                        $nombre_archivo    = uniqid('solicitud_salida_', true) . '.' . $archivo->getClientOriginalExtension();
                        $direccion_archivo = public_path($dir_doc);

                        $archivo->move($direccion_archivo, $nombre_archivo);
                    }

                    $iu               = RrhhSalida::find($id);
                    $iu->pdf          = 2;
                    $iu->papeleta_pdf = $nombre_archivo;
                    $iu->save();

                    $respuesta['respuesta'] .= "El DOCUMENTO se subio con éxito.";
                    $respuesta['sw']        = 1;
                    $respuesta['dia_hora']  = $dia_hora;

                return json_encode($respuesta);
                break;

            // === SELECT2 PERSONA ===
            case '100':
                if($request->has('q'))
                {
                    $nombre                           = $request->input('q');
                    $estado                           = trim($request->input('estado'));
                    $page_limit                       = trim($request->input('page_limit'));
                    $lugar_dependencia_id_funcionario = trim($request->input('lugar_dependencia_id_funcionario'));
                    $persona_id                       = trim($request->input('persona_id'));

                    $tabla1 = "rrhh_funcionarios";
                    $tabla2 = "rrhh_personas";

                    $tabla3 = "inst_unidades_desconcentradas";

                    $query = RrhhFuncionario::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.persona_id")
                        ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.unidad_desconcentrada_id")
                        ->whereRaw("CONCAT_WS(' - ', a2.n_documento, CONCAT_WS(' ', a2.ap_paterno, a2.ap_materno, a2.nombre)) ilike '%$nombre%'")
                        ->where("$tabla1.estado", "=", $estado)
                        ->where("a3.lugar_dependencia_id", "=", $lugar_dependencia_id_funcionario)
                        ->where("$tabla1.persona_id", "<>", $persona_id)
                        ->select(DB::raw("$tabla1.persona_id AS id, CONCAT_WS(' - ', a2.n_documento, CONCAT_WS(' ', a2.ap_paterno, a2.ap_materno, a2.nombre)) AS text"))
                        ->orderByRaw("CONCAT_WS(' ', a2.ap_paterno, a2.ap_materno, a2.nombre) ASC")
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
                switch($valor['validar_superior'])
                {
                    case '1':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->no_si[$valor['validar_superior']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-primary font-sm">' . $this->no_si[$valor['validar_superior']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
                        return($respuesta);
                        break;
                }
                break;
            case '3':
                switch($valor['validar_rrhh'])
                {
                    case '1':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->no_si[$valor['validar_rrhh']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-primary font-sm">' . $this->no_si[$valor['validar_rrhh']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
                        return($respuesta);
                        break;
                }
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
                            'consulta1' => $consulta1,
                            'consulta2' => $consulta2
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

                            $pdf->SetFont('times', 'B', 11);
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

                                if($data3['consulta2']['lugar_dependencia_id_funcionario'] == '6')
                                {
                                    $fill = FALSE;
                                    $x1   = 49;
                                    $x2   = 49;
                                    $y1   = 4;

                                    $pdf->SetFont('times', 'B', 6);

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
                                        'x1'      => $x1,
                                        'y1'      => $y1,
                                        'txt'     => "AUTORIZADO POR FISCAL DEPARAMETAL",
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
                                }
                                else
                                {
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
                                }

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