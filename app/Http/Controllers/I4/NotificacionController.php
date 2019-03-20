<?php

namespace App\Http\Controllers\I4;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\UtilClass;

use App\Models\Seguridad\SegPermisoRol;

use App\Models\I4\Caso;
use App\Models\I4\CasoFuncionario;
use App\Models\I4\Actividad;
use App\Models\I4\Persona;
use App\Models\I4\I4NotiNotificacion;

use Exception;

class NotificacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->estado = [
            '1' => 'HABILITADA',
            '2' => 'ANULADA'
        ];

        $this->persona_estado = [
            '1' => 'DENUNCIADO',
            '2' => 'DENUNCIANTE',
            '3' => 'VICTIMA'
        ];

        $this->notificacion_estado = [
            '1' => 'DENUNCIADO',
            '2' => 'DENUNCIANTE',
            '3' => 'VICTIMA'
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

        if(in_array(['codigo' => '2501'], $this->permisos))
        {
            $data = [
                'rol_id'            => $this->rol_id,
                'i4_funcionario_id' => $this->i4_funcionario_id,
                'permisos'          => $this->permisos,
                'title'             => 'Notificaciones',
                'home'              => 'Inicio',
                'sistema'           => 'i4',
                'modulo'            => 'Notificaciones',
                'title_table'       => 'Notificaciones'
            ];
            return view('i4.notificacion.notificacion')->with($data);
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
            // === INSERT UPDATE UPLOAD ===
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
                        'sw_1'       => 0,
                        'titulo'     => '<div class="text-center"><strong>NOTIFICAR</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'error_sw'   => 1
                    );
                    $opcion = 'n';

                // === PERMISOS ===
                    if(!in_array(['codigo' => '2502'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para NOTIFICAR.";
                        return json_encode($respuesta);
                    }

                // === VALIDATE ===
                    try
                    {
                        $validator = $this->validate($request,[
                            'caso_id'          => 'required',
                            'actividad_id'     => 'required',
                            'solicitud_asunto' => 'max: 500'
                        ],
                        [
                            'caso_id.required' => 'El CASO es obligatorio.',

                            'actividad_id.required' => 'La ACTIVIDAD es obligatorio.',

                            'solicitud_asunto.max' => 'El campo ASUNTO debe contener :max caracteres como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['caso_id']                  = trim($request->input('caso_id'));
                    $data1['actividad_solicitante_id'] = trim($request->input('actividad_id'));
                    $data1['persona_select']           = $request->input('persona_select');
                    $data1['solicitud_asunto']         = strtoupper($util->getNoAcentoNoComilla(trim($request->input('solicitud_asunto'))));

                    if( ! $request->has('persona_select'))
                    {
                        $respuesta['respuesta'] .= "Por lo menos a una persona se debe de NOTIFICAR.";
                        return json_encode($respuesta);
                    }

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    $fh_actual                  = date("Y-m-d H:i:s");
                    $g2_actual                  = date("y");
                    $g4_actual                  = date("Y");
                    $funcionario_solicitante_id = Auth::user()->i4_funcionario_id;
                    $estado_notificacion_id     = 1;
                    $persona_estado             = 0;

                    $c_enviadas          = 0;
                    $c_anterior_enviadas = 0;
                    $c_no_procesadas     = 0;

                    foreach($data1['persona_select'] as $persona_id)
                    {
                        $consulta1 = I4NotiNotificacion::where("actividad_solicitante_id", "=", $data1['actividad_solicitante_id'])
                                        ->where("persona_id", "=", $persona_id)
                                        ->select("id")
                                        ->first();

                        if($consulta1 === null)
                        {
                            // === CONSULTA 2 ===
                                $tabla1 = "Persona";
                                $tabla2 = "Abogado";
                                $tabla3 = "Muni";

                                $select2 = "
                                    $tabla1.id,
                                    UPPER($tabla1.Persona) AS Persona,
                                    UPPER($tabla1.DirDom) AS DirDom,
                                    UPPER($tabla1.ZonaDom) AS ZonaDom,
                                    UPPER($tabla1.NomMuniDom) AS NomMuniDom,
                                    $tabla1.TelDom,
                                    $tabla1.CelularDom,
                                    LOWER($tabla1.EMailPrivado) AS EMailPrivado,
                                    $tabla1.EsDenunciado,
                                    $tabla1.EsDenunciante,
                                    $tabla1.EsVictima,

                                    a2.id AS abogado_id,
                                    UPPER(a2.DirDom) AS abogado_DirDom,
                                    UPPER(a2.ZonaDom) AS abogado_ZonaDom,
                                    a2.TelDom AS abogado_TelDom,
                                    a2.CelularDom AS abogado_CelularDom,
                                    LOWER(a2.EMailPrivado) AS abogado_EMailPrivado,

                                    UPPER(a3.Muni) AS municipio
                                ";

                                $where2 = "$tabla1.id=" . $persona_id;

                                $consulta2 = Persona::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.Abogado")
                                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.MuniDom")
                                    ->whereRaw($where2)
                                    ->select(DB::raw($select2))
                                    ->first();

                            if(!($consulta2 === null))
                            {
                                if($consulta2["EsDenunciado"] == 1)
                                {
                                    $persona_estado = 1;
                                }
                                else if($consulta2["EsDenunciante"] == 1)
                                {
                                    $persona_estado = 2;
                                }
                                else if($consulta2["EsVictima"] == 1)
                                {
                                    $persona_estado = 3;
                                }

                                $iu = new I4NotiNotificacion;

                                $iu->caso_id                    = $data1['caso_id'];
                                $iu->persona_id                 = $persona_id;
                                $iu->abogado_id                 = $consulta2["abogado_id"];
                                $iu->actividad_solicitante_id   = $data1["actividad_solicitante_id"];
                                $iu->funcionario_solicitante_id = $funcionario_solicitante_id;
                                $iu->estado_notificacion_id     = $estado_notificacion_id;

                                $iu->codigo = $g2_actual . str_pad((I4NotiNotificacion::whereRaw('YEAR(solicitud_fh)=' . $g4_actual)->count())+1, 7, "0", STR_PAD_LEFT);

                                $iu->solicitud_fh     = $fh_actual;
                                $iu->solicitud_asunto = $data1['solicitud_asunto'];

                                $iu->persona_estado    = $persona_estado;
                                $iu->persona_direccion = $consulta2["DirDom"];
                                $iu->persona_zona      = $consulta2["ZonaDom"];
                                $iu->persona_municipio = $consulta2["NomMuniDom"];
                                $iu->persona_telefono  = $consulta2["TelDom"];
                                $iu->persona_celular   = $consulta2["CelularDom"];
                                $iu->persona_email     = $consulta2["EMailPrivado"];

                                $iu->abogado_direccion = $consulta2["abogado_DirDom"];
                                $iu->abogado_zona      = $consulta2["abogado_ZonaDom"];
                                $iu->abogado_municipio = $consulta2["municipio"];
                                $iu->abogado_telefono  = $consulta2["abogado_TelDom"];
                                $iu->abogado_celular   = $consulta2["abogado_CelularDom"];
                                $iu->abogado_email     = $consulta2["abogado_EMailPrivado"];

                                $iu->save();

                                $c_enviadas++;
                            }
                            else
                            {
                                $c_no_procesadas++;
                            }
                        }
                        else
                        {
                            $c_anterior_enviadas++;
                        }
                    }

                    if($c_enviadas > 0)
                    {
                        if($c_enviadas == 1)
                        {
                            $respuesta['respuesta'] .= "Se NOTIFICARA a " . $c_enviadas . ".";
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "Se NOTIFICARAN a " . $c_enviadas . ".";
                        }
                        $respuesta['sw']         = 1;
                    }

                    if($c_anterior_enviadas > 0)
                    {
                        $respuesta['respuesta'] .= "<br>Ya se ENVIO PARA NOTIFICAR a " . $c_anterior_enviadas . ".";
                    }

                    if($c_no_procesadas > 0)
                    {
                        $respuesta['respuesta'] .= "<br>No se ENVIO PARA NOTIFICAR a " . $c_no_procesadas . ".";
                    }

                return json_encode($respuesta);
                break;

            // === BUSCANDO CASO ===
            case '100':
                // === LIBRERIAS ===
                    $util = new UtilClass();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'        => 0,
                        'sw_1'      => 0,
                        'sw_2'      => 0,
                        'sw_3'      => 0,
                        'sw_4'      => 0,
                        'titulo'    => '<div class = "text-center"><strong>BUSQUEDA DEL CASO</strong></div>',
                        'respuesta' => '',
                        'tipo'      => $tipo
                    );

                //=== CAMPOS ENVIADOS ===
                    $data1['caso_id'] = trim($request->input('caso_id'));

                    if($data1['caso_id'] == "")
                    {
                        $respuesta['respuesta'] .= "El campo CASO está vacio.";
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    //=== CONSULTA 1 ===
                        $tabla1  = "Caso";
                        $tabla2  = "Delito";
                        $tabla3  = "EtapaCaso";
                        $tabla4  = "EstadoCaso";
                        $tabla5  = "OrigenCaso";

                        $select1 = "
                            $tabla1.id,
                            $tabla1.Caso,
                            $tabla1.CodCasoJuz,
                            $tabla1.FechaDenuncia,
                            $tabla1.DelitoPrincipal,
                            $tabla1.EtapaCaso,
                            $tabla1.EstadoCaso,
                            $tabla1.OrigenCaso,
                            $tabla1.triton_modificado,
                            $tabla1.n_detenidos,
                            $tabla1.DivisionFis AS division_id,

                            UPPER(a2.Delito) AS delito_principal,

                            UPPER(a3.EtapaCaso) AS etapa_caso,

                            UPPER(a4.EstadoCaso) AS estado_caso,

                            UPPER(a5.OrigenCaso) AS origen_caso
                        ";

                        $where1 = "$tabla1.id='" . $data1['caso_id'] . "'";

                        $cosulta1 = Caso::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.DelitoPrincipal")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.EtapaCaso")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "$tabla1.EstadoCaso")
                            ->leftJoin("$tabla5 AS a5", "a5.id", "=", "$tabla1.OrigenCaso")
                            ->whereRaw($where1)
                            ->select(DB::raw($select1))
                            ->first();

                        if($cosulta1 === null)
                        {
                            $respuesta['respuesta'] .= "No se encontró el CASO.";
                            return json_encode($respuesta);
                        }

                    //=== CONSULTA 3 ===
                        $tabla1 = "CasoFuncionario";
                        $tabla2 = "Funcionario";

                        $select2 = "
                            $tabla1.Caso AS caso_id,

                            UPPER(GROUP_CONCAT(DISTINCT a2.Funcionario ORDER BY a2.Funcionario ASC SEPARATOR ', ')) AS funcionario
                        ";

                        $group_by_2 = "
                            $tabla1.Caso
                        ";

                        $where2 = "$tabla1.Caso=" . $cosulta1['id'] . " AND $tabla1.FechaBaja IS NULL";

                        $cosulta2 = CasoFuncionario::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.Funcionario")
                            ->whereRaw($where2)
                            ->select(DB::raw($select2))
                            ->groupBy(DB::raw($group_by_2))
                            ->first();

                        if( ! ($cosulta2 === null))
                        {
                            $respuesta['sw_1']     = 1;
                            $respuesta['cosulta2'] = $cosulta2;
                        }

                    //=== CONSULTA 3 ===
                        $tabla1 = "Actividad";
                        $tabla2 = "TipoActividad";

                        $select3 = "
                            $tabla1.id,
                            $tabla1.Fecha,
                            UPPER($tabla1.Actividad) AS Actividad,
                            $tabla1._Documento,

                            UPPER(a2.TipoActividad) AS TipoActividad,
                            a2.Notificaciones,
                            a2.estado_notificacion
                        ";

                        $where3 = "$tabla1.Caso=" . $cosulta1['id'] . " AND $tabla1.EstadoDocumento=" . 2;

                        $cosulta3 = Actividad::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.TipoActividad")
                            ->whereRaw($where3)
                            ->select(DB::raw($select3))
                            ->orderBy("$tabla1.CreationDate", "DESC")
                            ->get();


                        if( ! $cosulta3->isEmpty())
                        {
                            $respuesta['sw_2']     = 1;
                            $respuesta['cosulta3'] = $cosulta3->toArray();
                        }

                    //=== CONSULTA 4 ===
                        $tabla1 = "Persona";

                        $select4 = "
                            $tabla1.Caso AS caso_id,

                            UPPER(GROUP_CONCAT(DISTINCT $tabla1.Persona ORDER BY $tabla1.Persona ASC SEPARATOR ', ')) AS denunciante
                        ";

                        $group_by_4 = "
                            $tabla1.Caso
                        ";

                        $where4 = "$tabla1.Caso=" . $cosulta1['id'] . " AND $tabla1.EsDenunciante=1";

                        $cosulta4 = Persona::whereRaw($where4)
                            ->select(DB::raw($select4))
                            ->groupBy(DB::raw($group_by_4))
                            ->first();

                        if( ! ($cosulta4 === null))
                        {
                            $respuesta['sw_3']     = 1;
                            $respuesta['cosulta4'] = $cosulta4;
                        }

                    //=== CONSULTA 5 ===
                        $tabla1 = "Persona";

                        $select5 = "
                            $tabla1.Caso AS caso_id,

                            UPPER(GROUP_CONCAT(DISTINCT $tabla1.Persona ORDER BY $tabla1.Persona ASC SEPARATOR ', ')) AS denunciado
                        ";

                        $group_by_5 = "
                            $tabla1.Caso
                        ";

                        $where5 = "$tabla1.Caso=" . $cosulta1['id'] . " AND $tabla1.EsDenunciado=1";

                        $cosulta5 = Persona::whereRaw($where5)
                            ->select(DB::raw($select5))
                            ->groupBy(DB::raw($group_by_5))
                            ->first();

                        if( ! ($cosulta5 === null))
                        {
                            $respuesta['sw_4']     = 1;
                            $respuesta['cosulta5'] = $cosulta5;
                        }

                    //=== CONSULTA 6 ===
                        $tabla1 = "Persona";
                        $tabla2 = "Abogado";

                        $select6 = "
                            $tabla1.id,
                            UPPER($tabla1.Persona) AS Persona,
                            UPPER($tabla1.DirDom) AS DirDom,
                            UPPER($tabla1.ZonaDom) AS ZonaDom,
                            $tabla1.TelDom,
                            $tabla1.CelularDom,
                            $tabla1.EsDenunciado,
                            $tabla1.EsDenunciante,
                            $tabla1.EsVictima,

                            a2.id AS abogado_id,
                            UPPER(a2.Abogado) AS abogado,
                            UPPER(a2.DirDom) AS abogado_DirDom,
                            UPPER(a2.ZonaDom) AS abogado_ZonaDom,
                            a2.TelDom AS abogado_TelDom,
                            a2.CelularDom AS abogado_CelularDom,
                            LOWER(a2.EMailPrivado) AS abogado_EMailPrivado
                        ";

                        $where6 = "$tabla1.Caso=" . $cosulta1['id'];

                        $cosulta6 = Persona::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.Abogado")
                            ->whereRaw($where6)
                            ->select(DB::raw($select6))
                            ->orderBy("$tabla1.Persona", "ASC")
                            ->get();

                        if( ! $cosulta6->isEmpty())
                        {
                            $respuesta['sw_6']     = 1;
                            $respuesta['cosulta6'] = $cosulta6->toArray();
                        }

                    $respuesta['cosulta1'] = $cosulta1;

                    $respuesta['respuesta'] .= "El CASO fue encontrado.";
                    $respuesta['sw']         = 1;

                return json_encode($respuesta);
                break;
            // === SELECT2 CASO ===
            case '101':
                if($request->has('q'))
                {
                    $i4_funcionario_id = Auth::user()->i4_funcionario_id;

                    if($i4_funcionario_id != '')
                    {
                        $nombre     = $request->input('q');
                        $page_limit = trim($request->input('page_limit'));

                        $query = Caso::join("CasoFuncionario", "CasoFuncionario.Caso", "=", "Caso.id")
                                    ->whereRaw("Caso.Caso LIKE '%$nombre%' AND CasoFuncionario.FechaBaja IS NULL AND CasoFuncionario.Funcionario=" . $i4_funcionario_id)
                                    ->select(DB::raw("Caso.id, Caso.Caso AS text"))
                                    ->orderByRaw("Caso.Caso ASC")
                                    ->limit($page_limit)
                                    ->get();

                        if( ! $query->isEmpty())
                        {
                            $respuesta = [
                                "results"  => $query->toArray(),
                                "paginate" => [
                                    "more" =>true
                                ]
                            ];
                            return json_encode($respuesta);
                        }
                    }
                }
                break;
        }
    }

    public function reportes(Request $request)
    {
        $tipo = $request->input('tipo');

        switch($tipo)
        {
            default:
                break;
        }
    }

    private function utilitarios($valor)
    {
        switch($valor['tipo'])
        {
            default:
                break;
        }
    }
}