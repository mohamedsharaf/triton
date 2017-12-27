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
                'no_si_array'                 => $this->no_si,
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

                        $año = date('Y', strtotime($data1['f_salida']));

                        $iu->codigo = str_pad((RrhhSalida::whereRaw("date_part('year', f_salida)='" . $año . "'")->count())+1, 5, "0", STR_PAD_LEFT) . "/" . $año;

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
                        $respuesta = '<span class="label label-primary font-sm">' . $this->no_si[$valor['pdf']] . '</span>';
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