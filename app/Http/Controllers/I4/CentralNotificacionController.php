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
use App\Models\I4\CasoFuncionario;
use App\Models\I4\Actividad;
use App\Models\I4\Persona;

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
                    $tabla1.notificacion_documento,
                    $tabla1.notificacion_testigo_nombre,
                    $tabla1.notificacion_testigo_n_documento,

                    UPPER(a2.EstadoNotificacion) AS EstadoNotificacion,
                    a2.UsoEntrega,

                    UPPER(a3.Persona) AS Persona,

                    UPPER(a4.Abogado) AS Abogado,

                    a5.Caso,

                    UPPER(a9.Dep) AS departamento,

                    UPPER(a11.Funcionario) AS funcionario_solicitante,

                    UPPER(a12.Funcionario) AS funcionario_notificador
                ";

                $array_where = '';

                if($request->has('anio_filter'))
                {
                    $array_where .= "YEAR(solicitud_fh)=" . $request->input('anio_filter');
                }
                else
                {
                    $array_where .= "TRUE";
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
                        'estado_notificacion_id' => $row["estado_notificacion_id"],
                        'estado'                 => $row["estado"],
                        'persona_estado'         => $row["persona_estado"],
                        'notificacion_estado'    => $row["notificacion_estado"],
                        'uso_entrega'            => $row["UsoEntrega"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',

                        // ($row["tipo_recinto"] =="") ? "" : $this->tipo_recinto[$row["tipo_recinto"]],

                        $this->utilitarios(array('tipo' => '1', 'valor' => $row["estado"])),
                        $this->utilitarios(array('tipo' => '2', 'valor' => $row["persona_estado"])),
                        $row["EstadoNotificacion"],
                        $this->utilitarios(array('tipo' => '3', 'valor' => $row["notificacion_estado"])),

                        $row["Caso"],

                        $row["codigo"],
                        $row["solicitud_fh"],
                        $row["notificacion_fh"],

                        $row["departamento"],

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
                        $respuesta = '<span class="label label-success font-sm">' . $this->si_no[$valor['valor']] . '</span>';
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
            default:
                break;
        }
    }
}