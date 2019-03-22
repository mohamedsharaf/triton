<?php

namespace App\Http\Controllers\I4;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridMysqlClass;
use App\Libraries\UtilClass;

use App\Models\Seguridad\SegPermisoRol;

use App\Models\I4\Dep;
use App\Models\I4\EstadoNotificacion;
use App\Models\I4\I4NotiNotificacion;
use App\Models\I4\Actividad;

use PDF;

use Exception;

class CentralNotificacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->estado = [
            '1' => 'HABILITADA',
            '2' => 'ANULADA',
            '3' => 'CERRADA'
        ];

        $this->persona_estado = [
            '1' => 'DENUNCIADO',
            '2' => 'DENUNCIANTE',
            '3' => 'VICTIMA'
        ];

        $this->si_no = [
            '1' => 'NO',
            '2' => 'SI'
        ];

        $this->public_dir = '/image/logo';
    }

    public function index()
    {
        $this->rol_id            = Auth::user()->rol_id;
        $this->i4_funcionario_id = Auth::user()->i4_funcionario_id;

        $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
            ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
            ->select("seg_permisos.codigo")
            ->get()
            ->toArray();

        if(in_array(['codigo' => '2701'], $this->permisos))
        {
            $data = [
                'rol_id'                    => $this->rol_id,
                'i4_funcionario_id'         => $this->i4_funcionario_id,
                'permisos'                  => $this->permisos,
                'title'                     => 'Central de notificaciones',
                'home'                      => 'Inicio',
                'sistema'                   => 'i4',
                'modulo'                    => 'Central de notificaciones',
                'title_table'               => 'Central de notificaciones',
                'estado_array'              => $this->estado,
                'persona_estado_array'      => $this->persona_estado,
                'si_no_array'               => $this->si_no,
                'departamento_array'        => Dep::select(DB::raw("id, UPPER(Dep) AS nombre"))
                                                ->orderBy("Dep")
                                                ->get()
                                                ->toArray(),
                'estado_notificacion_array' => EstadoNotificacion::select(DB::raw("id, UPPER(EstadoNotificacion) AS nombre, UsoEntrega AS estado"))
                                                ->where("UsoEntrega", ">", 0)
                                                ->orderBy("EstadoNotificacion")
                                                ->get()
                                                ->toArray()
            ];
            return view('i4.central_notificacion.central_notificacion')->with($data);
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
                $jqgrid = new JqgridMysqlClass($request);

                $tabla1  = "i4_noti_notificaciones";
                $tabla2  = "EstadoNotificacion";
                $tabla3  = "Persona";
                $tabla4  = "Abogado";
                $tabla5  = "Caso";
                $tabla6  = "Division";
                $tabla7  = "Oficina";
                $tabla8  = "Muni";
                $tabla9  = "Dep";
                $tabla10 = "Actividad";
                $tabla11 = "Funcionario";
                $tabla12 = "TipoActividad";

                $select = "
                    $tabla1.id,
                    $tabla1.caso_id,
                    $tabla1.persona_id,
                    $tabla1.abogado_id,
                    $tabla1.actividad_solicitante_id,
                    $tabla1.actividad_notificacion_id,
                    $tabla1.funcionario_solicitante_id,
                    $tabla1.funcionario_notificador_id,
                    $tabla1.funcionario_entrega_id,
                    $tabla1.estado_notificacion_id,

                    $tabla1.estado,
                    $tabla1.codigo,

                    $tabla1.solicitud_fh,
                    $tabla1.solicitud_asunto,

                    $tabla1.persona_estado,

                    $tabla1.persona_municipio,
                    $tabla1.persona_zona,
                    $tabla1.persona_direccion,
                    $tabla1.persona_telefono,
                    $tabla1.persona_celular,
                    $tabla1.persona_email,

                    $tabla1.abogado_municipio,
                    $tabla1.abogado_zona,
                    $tabla1.abogado_direccion,
                    $tabla1.abogado_telefono,
                    $tabla1.abogado_celular,
                    $tabla1.abogado_email,

                    $tabla1.notificacion_estado,
                    $tabla1.notificacion_fh,
                    $tabla1.notificacion_observacion,
                    $tabla1.notificacion_testigo_nombre,
                    $tabla1.notificacion_testigo_n_documento,

                    UPPER(a2.EstadoNotificacion) AS EstadoNotificacion,
                    a2.UsoEntrega,

                    UPPER(a3.Persona) AS Persona,

                    UPPER(a4.Abogado) AS Abogado,

                    a5.Caso,

                    UPPER(a9.Dep) AS departamento,

                    UPPER(a11.Funcionario) AS funcionario_solicitante,

                    UPPER(a12.Funcionario) AS funcionario_notificador,

                    UPPER(a13.TipoActividad) AS tipo_actividad
                ";

                $array_where = 'TRUE';

                $this->rol_id = Auth::user()->rol_id;

                $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                    ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                    ->select("seg_permisos.codigo")
                    ->get()
                    ->toArray();

                if(in_array(['codigo' => '2709'], $this->permisos))
                {
                    $i4_funcionario_id = Auth::user()->i4_funcionario_id;
                    $array_where      .= " AND $tabla1.funcionario_solicitante_id=" . $i4_funcionario_id;
                }
                else
                {
                    $departamentos_sw   = FALSE;
                    $departamentos_sw_1 = FALSE;
                    $departamentos      = " AND (";

                    if(in_array(['codigo' => '2710'], $this->permisos))
                    {
                        $departamentos     .= "a9.id=1";
                        $departamentos_sw   = TRUE;
                        $departamentos_sw_1 = TRUE;
                    }

                    if(in_array(['codigo' => '2711'], $this->permisos))
                    {
                        if($departamentos_sw_1)
                        {
                            $departamentos .= " OR a9.id=2";
                        }
                        else
                        {
                            $departamentos     .= "a9.id=2";
                            $departamentos_sw_1 = TRUE;
                        }

                        $departamentos_sw = TRUE;
                    }

                    if(in_array(['codigo' => '2712'], $this->permisos))
                    {
                        if($departamentos_sw_1)
                        {
                            $departamentos .= " OR a9.id=3";
                        }
                        else
                        {
                            $departamentos     .= "a9.id=3";
                            $departamentos_sw_1 = TRUE;
                        }

                        $departamentos_sw = TRUE;
                    }                    

                    if(in_array(['codigo' => '2713'], $this->permisos))
                    {
                        if($departamentos_sw_1)
                        {
                            $departamentos .= " OR a9.id=4";
                        }
                        else
                        {
                            $departamentos     .= "a9.id=4";
                            $departamentos_sw_1 = TRUE;
                        }

                        $departamentos_sw = TRUE;
                    }

                    if(in_array(['codigo' => '2714'], $this->permisos))
                    {
                        if($departamentos_sw_1)
                        {
                            $departamentos .= " OR a9.id=5";
                        }
                        else
                        {
                            $departamentos     .= "a9.id=5";
                            $departamentos_sw_1 = TRUE;
                        }

                        $departamentos_sw = TRUE;
                    }

                    if(in_array(['codigo' => '2715'], $this->permisos))
                    {
                        if($departamentos_sw_1)
                        {
                            $departamentos .= " OR a9.id=6";
                        }
                        else
                        {
                            $departamentos     .= "a9.id=6";
                            $departamentos_sw_1 = TRUE;
                        }

                        $departamentos_sw = TRUE;
                    }

                    if(in_array(['codigo' => '2716'], $this->permisos))
                    {
                        if($departamentos_sw_1)
                        {
                            $departamentos .= " OR a9.id=7";
                        }
                        else
                        {
                            $departamentos     .= "a9.id=7";
                            $departamentos_sw_1 = TRUE;
                        }

                        $departamentos_sw = TRUE;
                    }

                    if(in_array(['codigo' => '2717'], $this->permisos))
                    {
                        if($departamentos_sw_1)
                        {
                            $departamentos .= " OR a9.id=8";
                        }
                        else
                        {
                            $departamentos     .= "a9.id=8";
                            $departamentos_sw_1 = TRUE;
                        }

                        $departamentos_sw = TRUE;
                    }

                    if(in_array(['codigo' => '2718'], $this->permisos))
                    {
                        if($departamentos_sw_1)
                        {
                            $departamentos .= " OR a9.id=9";
                        }
                        else
                        {
                            $departamentos     .= "a9.id=9";
                            $departamentos_sw_1 = TRUE;
                        }

                        $departamentos_sw = TRUE;
                    }

                    $departamentos .= ")";

                    if($departamentos_sw)
                    {
                        $array_where .= $departamentos;
                    }
                }

                if($request->has('anio_filter'))
                {
                    $array_where .= " AND YEAR(solicitud_fh)=" . $request->input('anio_filter');
                }

                $array_where .= $jqgrid->getWhere();

                $count = I4NotiNotificacion::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.estado_notificacion_id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.persona_id")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "$tabla1.abogado_id")
                            ->leftJoin("$tabla5 AS a5", "a5.id", "=", "$tabla1.caso_id")
                            ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.DivisionFis")
                            ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a6.Oficina")
                            ->leftJoin("$tabla8 AS a8", "a8.id", "=", "a7.Muni")
                            ->leftJoin("$tabla9 AS a9", "a9.id", "=", "a8.Dep")
                            ->leftJoin("$tabla10 AS a10", "a10.id", "=", "$tabla1.actividad_solicitante_id")
                            ->leftJoin("$tabla11 AS a11", "a11.id", "=", "$tabla1.funcionario_solicitante_id")
                            ->leftJoin("$tabla11 AS a12", "a12.id", "=", "$tabla1.funcionario_notificador_id")
                            ->leftJoin("$tabla12 AS a13", "a13.id", "=", "a10.TipoActividad")
                            ->whereRaw($array_where)
                            ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = I4NotiNotificacion::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.estado_notificacion_id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.persona_id")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "$tabla1.abogado_id")
                            ->leftJoin("$tabla5 AS a5", "a5.id", "=", "$tabla1.caso_id")
                            ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.DivisionFis")
                            ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a6.Oficina")
                            ->leftJoin("$tabla8 AS a8", "a8.id", "=", "a7.Muni")
                            ->leftJoin("$tabla9 AS a9", "a9.id", "=", "a8.Dep")
                            ->leftJoin("$tabla10 AS a10", "a10.id", "=", "$tabla1.actividad_solicitante_id")
                            ->leftJoin("$tabla11 AS a11", "a11.id", "=", "$tabla1.funcionario_solicitante_id")
                            ->leftJoin("$tabla11 AS a12", "a12.id", "=", "$tabla1.funcionario_notificador_id")
                            ->leftJoin("$tabla12 AS a13", "a13.id", "=", "a10.TipoActividad")
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
                        'estado_notificacion_id'           => $row["estado_notificacion_id"],
                        'estado'                           => $row["estado"],
                        'persona_estado'                   => $row["persona_estado"],
                        'notificacion_estado'              => $row["notificacion_estado"],
                        'notificacion_testigo_n_documento' => $row["notificacion_testigo_n_documento"],
                        'uso_entrega'                      => $row["UsoEntrega"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',

                        // ($row["tipo_recinto"] =="") ? "" : $this->tipo_recinto[$row["tipo_recinto"]],

                        $this->utilitarios(array('tipo' => '1', 'valor' => $row["estado"])),
                        $this->utilitarios(array('tipo' => '2', 'valor' => $row["persona_estado"])),
                        $row["EstadoNotificacion"],
                        $this->utilitarios(array('tipo' => '3', 'id' => $row["id"], 'valor' => $row["notificacion_estado"])),

                        $row["Caso"],

                        $row["codigo"],
                        $row["solicitud_fh"],
                        $row["notificacion_fh"],

                        $row["departamento"],

                        $this->utilitarios(array(
                            'tipo'  => '5',
                            'id'    => $row["id"],
                            'valor' => $row["tipo_actividad"]
                        )),

                        $row["Persona"],
                        $this->utilitarios(array(
                            'tipo'      => '4',
                            'municipio' => $row["persona_municipio"],
                            'zona'      => $row["persona_zona"],
                            'direccion' => $row["persona_direccion"],
                            'telefono'  => $row["persona_telefono"],
                            'celular'   => $row["persona_celular"],
                            'email'     => $row["persona_email"]
                        )),

                        $row["Abogado"],
                        $this->utilitarios(array(
                            'tipo'      => '4',
                            'municipio' => $row["abogado_municipio"],
                            'zona'      => $row["abogado_zona"],
                            'direccion' => $row["abogado_direccion"],
                            'telefono'  => $row["abogado_telefono"],
                            'celular'   => $row["abogado_celular"],
                            'email'     => $row["abogado_email"]
                        )),

                        $row["solicitud_asunto"],

                        $row["notificacion_observacion"],

                        $row["notificacion_testigo_nombre"],

                        $row["funcionario_solicitante"],

                        $row["funcionario_notificador"],

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
                'titulo'    => 'ERROR 500',
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
                        'titulo'     => '<div class="text-center"><strong>Notificación</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'error_sw'   => 1
                    );

                // === PERMISOS ===
                    if(!in_array(['codigo' => '2702'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para NOTIFICAR.";
                        return json_encode($respuesta);
                    }

                // === VALIDATE ===
                    try
                    {
                        $validator = $this->validate($request,[
                            'estado_notificacion_id'           => 'required',
                            'notificacion_f'                   => 'required|date',
                            'notificacion_observacion'         => 'max: 200',
                            'notificacion_testigo_n_documento' => 'max: 20',
                            'notificacion_testigo_nombre'      => 'max: 200'
                        ],
                        [
                            'estado_notificacion_id.required' => 'El campo ESTADO DE LA NOTIFICACION es obligatorio.',

                            'notificacion_f.required' => 'El campo FECHA DE NOTIFICACION es obligatorio.',
                            'notificacion_f.date'     => 'El campo FECHA DE NOTIFICACION no corresponde con una fecha válida.',

                            'notificacion_observacion.max' => 'El campo OBSERVACION debe contener :max caracteres como máximo.',

                            'notificacion_testigo_n_documento.max' => 'El campo NUMERO DE DOCUMENTO DEL TESTIGO debe contener :max caracteres como máximo.',

                            'notificacion_testigo_nombre.max' => 'El campo NOMBRE DEL TESTIGO debe contener :max caracteres como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== REQUEST ===
                    if( ! $request->has('id'))
                    {
                        $respuesta['respuesta'] .= "No existe NOTIFICACION.";
                        return json_encode($respuesta);
                    }

                    $id = trim($request->input('id'));

                    $consulta1 = I4NotiNotificacion::where('id', '=', $id)
                                    ->select("id", "estado")
                                    ->first();

                    if($consulta1['estado'] == 2)
                    {
                        $respuesta['respuesta'] .= "La NOTIFICACION está ANULADA.";
                        return json_encode($respuesta);
                    }

                    if($consulta1['estado'] == 3)
                    {
                        $respuesta['respuesta'] .= "La NOTIFICACION está CERRADA.";
                        return json_encode($respuesta);
                    }

                    $data1['estado_notificacion_id']           = trim($request->input('estado_notificacion_id'));
                    $data1['notificacion_f']                   = trim($request->input('notificacion_f'));
                    $data1['notificacion_h']                   = trim($request->input('notificacion_h'));
                    $data1['notificacion_observacion']         = strtoupper($util->getNoAcentoNoComilla(trim($request->input('notificacion_observacion'))));
                    $data1['notificacion_testigo_n_documento'] = strtoupper($util->getNoAcentoNoComilla(trim($request->input('notificacion_testigo_n_documento'))));
                    $data1['notificacion_testigo_nombre']      = strtoupper($util->getNoAcentoNoComilla(trim($request->input('notificacion_testigo_nombre'))));

                    if($data1['notificacion_h'] != '')
                    {
                        $data1['notificacion_fh'] = $data1['notificacion_f'] . ' ' . $data1['notificacion_h'];
                    }
                    else
                    {
                        $data1['notificacion_fh'] = $data1['notificacion_f'] . ' ' . '00:00:00';
                    }

                    $data1['funcionario_notificador_id'] = Auth::user()->i4_funcionario_id;
                    if($data1['funcionario_notificador_id'] == "")
                    {
                        $respuesta['respuesta'] .= "Debe de registrar su cuenta del i4 en el Sistema TRITON.";
                        return json_encode($respuesta);
                    }

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === MODIFICAR NOTIFICACIONES ===
                    $iu                             = I4NotiNotificacion::find($id);
                    $iu->funcionario_notificador_id = $data1['funcionario_notificador_id'];
                    $iu->estado_notificacion_id     = $data1['estado_notificacion_id'];
                    $iu->notificacion_fh            = $data1['notificacion_fh'];
                    $iu->notificacion_observacion   = $data1['notificacion_observacion'];
                    if($data1['estado_notificacion_id'] == 4)
                    {
                        // if($data1['notificacion_testigo_n_documento'] == "" || $data1['notificacion_testigo_nombre'] == "")
                        // {
                        //     $respuesta['respuesta'] .= "Los campos número de documento y nombre del testigo son obligatorios.";
                        //     return json_encode($respuesta);
                        // }

                        $iu->notificacion_testigo_n_documento = $data1['notificacion_testigo_n_documento'];
                        $iu->notificacion_testigo_nombre      = $data1['notificacion_testigo_nombre'];
                    }
                    else
                    {
                        $iu->notificacion_testigo_n_documento = NULL;
                        $iu->notificacion_testigo_nombre      = NULL;
                    }

                    $iu->save();

                    $respuesta['respuesta'] .= "El RECINTO CARCELARIO se edito con éxito.";
                    $respuesta['sw']         = 1;
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
                        'titulo'     => '<div class="text-center"><strong>SUBIR DOCUMENTO PDF</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'error_sw'   => 1
                    );

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if($id != '')
                    {
                        if(!in_array(['codigo' => '2707'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para SUBIR ARCHIVO PDF.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "La ID de la NOTIFICACION es obligatoria.";
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
                    if($request->hasFile('file'))
                    {
                        $documento        = $request->file('file');
                        $documento_base64 = file_get_contents($documento->getRealPath());
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "El ARCHIVO PDF no existe.";
                        return json_encode($respuesta);
                    }

                    $iu                         = I4NotiNotificacion::find($id);
                    $iu->notificacion_estado    = 2;
                    $iu->notificacion_documento = $documento_base64;
                    $iu->save();

                    $respuesta['respuesta'] .= "El DOCUMENTO PDF se subio con éxito.";
                    $respuesta['sw']         = 1;

                return json_encode($respuesta);
                break;
            // === DOCUMENTO DE LA NOTIFICACION - BINARIO 64 ===
            case '3':
                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'        => 0,
                        'titulo'    => '<div class="text-center"><strong>DOCUMENTO PDF</strong></div>',
                        'respuesta' => '',
                        'tipo'      => $tipo,
                        'pdf'       => ""
                    );
                    $error  = FALSE;

                // === VALIDAR ===
                    $id = trim($request->input('id'));
                    if($id == '')
                    {
                        $respuesta['respuesta'] .= "Seleccione una NOTIFICACION.";
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $consulta1 = I4NotiNotificacion::where('id', '=', $id)->first();
                    if( ! ($consulta1 === null))
                    {
                        // $my_bytea  = stream_get_contents($consulta1->notificacion_documento);
                        // $my_string = pg_unescape_bytea($my_bytea);
                        // $html_data = htmlspecialchars($my_string);

                        $respuesta['pdf'] .= base64_encode($consulta1->notificacion_documento);

                        header('Content-type: application/pdf');
                        header("Cache-Control: no-cache");
                        header("Pragma: no-cache");
                        header("Content-Disposition: inline;filename=myfile.pdf'");

                        $respuesta['respuesta'] .= "Se encontro el DOCUMENTO PDF.";
                        $respuesta['sw']         = 1;
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "No se logró encontrar la NOTIFICACION.";
                    }

                //=== RESPUESTA ===
                    return json_encode($respuesta);
                break;
            // === CERRAR NOTIFICACION ===
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
                        'titulo'     => '<div class="text-center"><strong>CERRAR NOTIFICACION</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'error_sw'   => 1
                    );

                // === PERMISOS ===
                    if(!in_array(['codigo' => '2708'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para CERRAR NOTIFICACION.";
                        return json_encode($respuesta);
                    }

                // === VALIDATE ===
                    $id = trim($request->input('id'));
                    if($id == '')
                    {
                        $respuesta['respuesta'] .= "Seleccione una NOTIFICACION.";
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $iu         = I4NotiNotificacion::find($id);
                    $iu->estado = 3;

                    $iu->save();

                    $respuesta['respuesta'] .= "La NOTIFICACION se cerro.";
                    $respuesta['sw']         = 1;

                return json_encode($respuesta);
                break;
            // === DOCUMENTO DE LA ACTIVIDAD - BINARIO 64 ===
            case '5':
                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'        => 0,
                        'titulo'    => '<div class="text-center"><strong>DOCUMENTO PDF</strong></div>',
                        'respuesta' => '',
                        'tipo'      => $tipo,
                        'pdf'       => ""
                    );
                    $error  = FALSE;

                // === VALIDAR ===
                    $id = trim($request->input('id'));
                    if($id == '')
                    {
                        $respuesta['respuesta'] .= "Seleccione una ACTIVIDAD.";
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $consulta1 = I4NotiNotificacion::select('actividad_solicitante_id')->where('id', '=', $id)->first();
                    if( ! ($consulta1 === null))
                    {                        
                        $consulta2 = Actividad::select('Documento', '_Documento')->where('id', '=', $consulta1->actividad_solicitante_id)->first();

                        if( ! ($consulta2 === null))
                        {
                            $ultimos_tres = substr($consulta2['_Documento'], -3);
                            if(strtoupper($ultimos_tres) == 'PDF')
                            {
                                $respuesta['pdf'] .= base64_encode($consulta2->Documento);

                                header('Content-type: application/pdf');
                                header("Cache-Control: no-cache");
                                header("Pragma: no-cache");
                                header("Content-Disposition: inline;filename=myfile.pdf'");

                                $respuesta['respuesta'] .= "Se encontro el DOCUMENTO PDF." . $ultimos_tres;
                                $respuesta['sw']         = 1;
                            }
                            else
                            {
                                $respuesta['respuesta'] .= "No es DOCUMENTO PDF." . $ultimos_tres;
                            }
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "No se logró encontrar la ACTIVIDAD.";
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "No se logró encontrar la NOTIFICACION.";
                    }

                //=== RESPUESTA ===
                    return json_encode($respuesta);
                break;
        }
    }

    public function reportes(Request $request)
    {
        $tipo = $request->input('tipo');

        switch($tipo)
        {
            case '1':
                if($request->has('notificacion_id'))
                {
                    $notificacion_id = trim($request->input('notificacion_id'));

                    $dir_logo_institucion = public_path($this->public_dir) . '/' . 'logo_fge_256_2018_3.png';

                    // === VALIDAR IMAGENES ===
                        if( ! file_exists($dir_logo_institucion))
                        {
                            return "No existe el logo de la institución " . $dir_logo_institucion;
                        }

                    // === CONSULTA A LA BASE DE DATOS ===
                        $consulta1 = I4NotiNotificacion::where('id', '=', $notificacion_id)
                                        ->first();

                        if($consulta1 === null)
                        {
                            return "No existe la NOTIFICACION";
                        }

                        if($consulta1['estado'] == '2')
                        {
                            return "La NOTIFICACION fue ANULADA.";
                        }

                        $tabla1  = "i4_noti_notificaciones";
                        $tabla2  = "EstadoNotificacion";
                        $tabla3  = "Persona";
                        $tabla4  = "Abogado";
                        $tabla5  = "Caso";
                        $tabla6  = "Division";
                        $tabla7  = "Oficina";
                        $tabla8  = "Muni";
                        $tabla9  = "Dep";
                        $tabla10 = "Actividad";
                        $tabla11 = "Funcionario";
                        $tabla12 = "TipoActividad";

                        $select2 = "
                            $tabla1.id,
                            $tabla1.caso_id,
                            $tabla1.persona_id,
                            $tabla1.abogado_id,
                            $tabla1.actividad_solicitante_id,
                            $tabla1.actividad_notificacion_id,
                            $tabla1.funcionario_solicitante_id,
                            $tabla1.funcionario_notificador_id,
                            $tabla1.funcionario_entrega_id,
                            $tabla1.estado_notificacion_id,

                            $tabla1.estado,
                            $tabla1.codigo,

                            $tabla1.solicitud_fh,
                            $tabla1.solicitud_asunto,

                            $tabla1.persona_estado,

                            $tabla1.persona_municipio,
                            $tabla1.persona_zona,
                            $tabla1.persona_direccion,
                            $tabla1.persona_telefono,
                            $tabla1.persona_celular,
                            $tabla1.persona_email,

                            $tabla1.abogado_municipio,
                            $tabla1.abogado_zona,
                            $tabla1.abogado_direccion,
                            $tabla1.abogado_telefono,
                            $tabla1.abogado_celular,
                            $tabla1.abogado_email,

                            $tabla1.notificacion_estado,
                            $tabla1.notificacion_fh,
                            $tabla1.notificacion_observacion,
                            $tabla1.notificacion_documento,
                            $tabla1.notificacion_testigo_nombre,
                            $tabla1.notificacion_testigo_n_documento,

                            UPPER(a2.EstadoNotificacion) AS EstadoNotificacion,
                            a2.UsoEntrega,

                            UPPER(a3.Persona) AS Persona,

                            UPPER(a4.Abogado) AS Abogado,

                            a5.Caso,

                            UPPER(a8.Muni) AS municipio,

                            UPPER(a9.Dep) AS departamento,

                            a10.Fecha AS fecha_actividad,

                            UPPER(a11.Funcionario) AS funcionario_solicitante,

                            UPPER(a12.Funcionario) AS funcionario_notificador,

                            UPPER(a13.TipoActividad) AS tipo_actividad
                        ";

                        $consulta2 = I4NotiNotificacion::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.estado_notificacion_id")
                                        ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.persona_id")
                                        ->leftJoin("$tabla4 AS a4", "a4.id", "=", "$tabla1.abogado_id")
                                        ->leftJoin("$tabla5 AS a5", "a5.id", "=", "$tabla1.caso_id")
                                        ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.DivisionFis")
                                        ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a6.Oficina")
                                        ->leftJoin("$tabla8 AS a8", "a8.id", "=", "a7.Muni")
                                        ->leftJoin("$tabla9 AS a9", "a9.id", "=", "a8.Dep")
                                        ->leftJoin("$tabla10 AS a10", "a10.id", "=", "$tabla1.actividad_solicitante_id")
                                        ->leftJoin("$tabla11 AS a11", "a11.id", "=", "$tabla1.funcionario_solicitante_id")
                                        ->leftJoin("$tabla11 AS a12", "a12.id", "=", "$tabla1.funcionario_notificador_id")
                                        ->leftJoin("$tabla12 AS a13", "a13.id", "=", "a10.TipoActividad")
                                        ->where("$tabla1.id", '=', $notificacion_id)
                                        ->select(DB::raw($select2))
                                        ->first();

                    // === CARGAR VALORES ===
                        $style_qrcode = array(
                            'border'        => 0,
                            'vpadding'      => 'auto',
                            'hpadding'      => 'auto',
                            'fgcolor'       => array(0, 0, 0),
                            'bgcolor'       => false, //array(255,255,255)
                            'module_width'  => 1, // width of a single module in points
                            'module_height' => 1 // height of a single module in points
                        );

                        $meses = array(
                            '01' => 'enero',
                            '02' => 'febrero',
                            '03' => 'marzo',
                            '04' => 'abril',
                            '05' => 'mayo',
                            '06' => 'junio',
                            '07' => 'julio',
                            '08' => 'agosto',
                            '09' => 'septiembre',
                            '10' => 'octubre',
                            '11' => 'noviembre',
                            '12' => 'diciembre'
                        );

                    // == FOOTER ==
                        PDF::setFooterCallback(function($pdf) use($consulta2){
                            $style1 = array(
                                'width' => 0.5,
                                'cap'   => 'butt',
                                'join'  => 'miter',
                                'dash'  => '0',
                                'phase' => 10,
                                'color' => array(0, 0, 0)
                            );

                            if($consulta2['estado_notificacion_id'] <> 4)
                            {
                                $texto_notificacion = "NOTIFICADO";
                                if($consulta2['estado_notificacion_id'] == 3)
                                {
                                    $texto_persona = $consulta2['Persona'];
                                }
                                else
                                {
                                    $texto_persona = $consulta2['Abogado'];
                                }
                            }
                            else
                            {
                                $texto_notificacion = "TESTIGO";

                                if($consulta2['notificacion_testigo_nombre'] == "")
                                {
                                    $texto_persona = "";
                                }
                                else
                                {
                                    $texto_persona = $consulta2['notificacion_testigo_nombre'];
                                }
                            }

                            $y_n = 139.5;

                            $pdf->SetFont("helvetica", "", 8);
                            $pdf->SetY($y_n-25);
                            $pdf->Cell(98, 4, $consulta2['funcionario_notificador'], 0, 0, "C");
                            $pdf->Cell(98, 4, $texto_persona, 0, 0, "C");

                            $pdf->Ln();
                            $pdf->SetFont("helvetica", "B", 10);
                            $pdf->Cell(98, 4, 'FUNCIONARIO', 0, 0, "C");
                            $pdf->Cell(98, 4, $texto_notificacion, 0, 0, "C");

                            $pdf->Line(10, $y_n-10, 206, $y_n-10, $style1);
                            $pdf->SetY($y_n-9);
                            $pdf->SetFont("times", "I", 7);
                            $pdf->Cell(98, 4, 'Fecha de emisión: ' . date("d/m/Y H:i:s"), 0, 0, "L");
                            $pdf->Cell(98, 4, "Página " . $pdf->getAliasNumPage() . "/" . $pdf->getAliasNbPages(), 0, 0, "R");
                        });

                    PDF::setPageUnit('mm');

                    PDF::SetMargins(10, 6, 10);
                    PDF::getAliasNbPages();
                    PDF::SetCreator('MINISTERIO PUBLICO');
                    PDF::SetAuthor('TRITON');
                    PDF::SetTitle('NOTIFICACION');
                    PDF::SetSubject('DOCUMENTO');
                    PDF::SetKeywords('NOTIFICACION');

                    // PDF::SetFontSubsetting(false);

                    PDF::SetAutoPageBreak(TRUE, 10);
                    // PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                    // === BODY ===
                        // PDF::AddPage('L', 'MEMO');
                        PDF::AddPage('P', 'LETTER');

                        // === TITULO Y LOGO ===
                            PDF::Image($dir_logo_institucion, 180, 6, 0, 23, 'PNG');

                            PDF::SetFont('times', 'B', 20);
                            PDF::Write(0, 'MINISTERIO PÚBLICO', '', 0, 'C', true, 0, false, false, 0);

                            PDF::SetFont('times', 'B', 18);
                            PDF::Write(0, $consulta2['EstadoNotificacion'], '', 0, 'C', true, 0, false, false, 0);

                            PDF::SetFont('times', 'B', 13);
                            PDF::Write(0, "CASO: " . $consulta2['Caso'], '', 0, 'C', true, 0, false, false, 0);

                            PDF::write2DBarcode(
                                url()->full(),  // Código para imprimir
                                'QRCODE,L',     // Tipo de código de barras
                                10,             // x posición
                                6,              // y posición
                                25,             // Ancho
                                25,             // Altura
                                $style_qrcode,  // conjunto de opciones:
                                '',             // Indica la alineación del puntero al lado de la inserción del código de barras con respecto a la altura del código de barras. El valor puede ser:
                                                    // T: arriba a la derecha para LTR o arriba a la izquierda para RTL
                                                    // M: medio-derecha para LTR o middle-left para RTL
                                                    // B: abajo a la derecha para LTR o abajo a la izquierda para RTL
                                                    // N: siguiente línea
                                FALSE           // FALSE
                            );

                            PDF::SetFont('times', 'B', 10);
                            PDF::Write(0, "    " . $consulta2['codigo'], '', 0, 'L', true, 0, false, false, 0);

                        // === CONTENIDO DE LA NOTIFICACION ===
                            $anio = date("Y", strtotime($consulta2['notificacion_fh']));
                            $mes  = date("m", strtotime($consulta2['notificacion_fh']));
                            $dia  = date("d", strtotime($consulta2['notificacion_fh']));
                            $hora = date("H:i", strtotime($consulta2['notificacion_fh']));

                            if($hora == '00:00')
                            {
                                $hora1 = '..........:...........';
                            }
                            else
                            {
                                $hora1 = $hora;
                            }

                            if($consulta2['UsoEntrega'] > 3)
                            {
                                $persona_abogado = $consulta2['Abogado'];
                            }
                            else
                            {
                                $persona_abogado = $consulta2['Persona'];
                            }

                            if($consulta2['estado_notificacion_id'] == 3)
                            {
                                $en_texto = ", haciendole entrega de la copia de acuerdo al Art. 163 y 164 del Código de Procedimiento Penal";
                            }
                            elseif($consulta2['estado_notificacion_id'] == 4)
                            {
                                if($consulta2['notificacion_testigo_nombre'] == '')
                                {
                                    $testigo_nombre = "....................................................................................................";
                                }
                                else
                                {
                                    $testigo_nombre = $consulta2['notificacion_testigo_nombre'];
                                }

                                if($consulta2['notificacion_testigo_n_documento'] == '')
                                {
                                    $testigo_n_documento = "..................................................";
                                }
                                else
                                {
                                    $testigo_n_documento = $consulta2['notificacion_testigo_n_documento'];
                                }

                                $en_texto = ", en presencia del testigo " . $testigo_nombre . " con Cédula de Identidad " . $testigo_n_documento . ", quien firma en constancia al pie del presente";
                            }
                            else
                            {
                                $en_texto = "";
                            }

                            $anio1 = date("Y", strtotime($consulta2['fecha_actividad']));
                            $mes1  = date("m", strtotime($consulta2['fecha_actividad']));
                            $dia1  = date("d", strtotime($consulta2['fecha_actividad']));

                            PDF::Ln(2);
                            PDF::SetFont("helvetica", "", 10);
                            $html = '
                            <p style="text-align:justify;"><i>En la ciudad de <b>' . $consulta2['municipio'] . '</b> a horas ' . $hora1 . ' del ' . $dia . ' de ' . $meses[$mes] . ' de ' . $anio . ', se notifica a <b>' . $persona_abogado . '</b> con <b>' . $consulta2['tipo_actividad'] . '</b> de fecha ' . $dia1 . ' de ' . $meses[$mes1] . ' de ' . $anio1 . $en_texto . '.</i></p>
                            <p style="text-align:justify;"><i><b>Observaciones: </b></i></p>
                            <p style="text-align:justify;"><i>...............................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................</i></p>
                            ';
                            PDF::writeHTML($html, true, false, true, true, '');

                    PDF::Output('notificacion_' . date("YmdHis") . '.pdf', 'I');
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

    private function utilitarios($valor)
    {
        switch($valor['tipo'])
        {
            case '1':
                switch($valor['valor'])
                {
                    case '1':
                        $respuesta = '<span class="label label-success font-sm">' . $this->estado[$valor['valor']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->estado[$valor['valor']] . '</span>';
                        return($respuesta);
                        break;
                    case '3':
                        $respuesta = '<span class="label label-warning font-sm">' . $this->estado[$valor['valor']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
                        return($respuesta);
                        break;
                }
                break;
            case '2':
                switch($valor['valor'])
                {
                    case '1':
                        $respuesta = '<span class="label label-warning font-sm">' . $this->persona_estado[$valor['valor']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-primary font-sm">' . $this->persona_estado[$valor['valor']] . '</span>';
                        return($respuesta);
                        break;
                    case '3':
                        $respuesta = '<span class="label label-info font-sm">' . $this->persona_estado[$valor['valor']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN SITUACION</span>';
                        return($respuesta);
                        break;
                }
                break;
            case '3':
                switch($valor['valor'])
                {
                    case '1':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->si_no[$valor['valor']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<button type="button" class="btn btn-xs btn-success" title="Mostrar documento PDF" onclick="utilitarios([31, ' . $valor['id'] . ']);"><strong>' . $this->si_no[$valor['valor']] . '</strong></button>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN SITUACION</span>';
                        return($respuesta);
                        break;
                }
                break;
            case '4':
                $respuesta = "";
                $sw        = TRUE;
                if($valor['municipio'] != '')
                {
                    if($sw)
                    {
                        $respuesta .= $valor['municipio'];
                        $sw = FALSE;
                    }
                }

                if($valor['zona'] != '')
                {
                    if($sw)
                    {
                        $respuesta .= $valor['zona'];
                        $sw = FALSE;
                    }
                    else
                    {
                        $respuesta .= ", " . $valor['zona'];
                    }
                }

                if($valor['direccion'] != '')
                {
                    if($sw)
                    {
                        $respuesta .= $valor['direccion'];
                        $sw = FALSE;
                    }
                    else
                    {
                        $respuesta .= ", " . $valor['direccion'];
                    }
                }

                if($valor['telefono'] != '')
                {
                    if($sw)
                    {
                        $respuesta .= $valor['telefono'];
                        $sw = FALSE;
                    }
                    else
                    {
                        $respuesta .= ", " . $valor['telefono'];
                    }
                }

                if($valor['celular'] != '')
                {
                    if($sw)
                    {
                        $respuesta .= $valor['celular'];
                        $sw = FALSE;
                    }
                    else
                    {
                        $respuesta .= ", " . $valor['celular'];
                    }
                }

                if($valor['email'] != '')
                {
                    if($sw)
                    {
                        $respuesta .= $valor['email'];
                        $sw = FALSE;
                    }
                    else
                    {
                        $respuesta .= ", " . $valor['email'];
                    }
                }

                return($respuesta);
                break;            
            case '5':
                switch($valor['valor'])
                {
                    case '':
                        $respuesta = '';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<button type="button" class="btn btn-xs btn-success" title="Mostrar actividad" onclick="utilitarios([33, ' . $valor['id'] . ']);"><strong>' . $valor['valor'] . '</strong></button>';
                        return($respuesta);
                        break;
                }
                break;
            default:
                break;
        }
    }
}