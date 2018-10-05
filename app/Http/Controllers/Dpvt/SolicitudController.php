<?php

namespace App\Http\Controllers\Dpvt;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;
use App\Libraries\UtilClass;

use App\Models\Seguridad\SegPermisoRol;
use App\Models\Seguridad\SegLdUser;

use App\Models\Dpvt\PvtDelito;
use App\Models\Dpvt\PvtSolicitud;
use App\Models\Dpvt\PvtResolucion;
use App\Models\Dpvt\PvtSolicitudDelito;

use App\Models\UbicacionGeografica\UbgeMunicipio;
use App\Models\Rrhh\RrhhPersona;

use Maatwebsite\Excel\Facades\Excel;
use PDF;

use Exception;

class SolicitudController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

        $this->estado = [
            '1' => 'SIN ESTADO',
            '2' => 'PENDIENTE DE INFORME PSICOLOGICO',
            '3' => 'PENDIENTE DE INFORME SOCIAL',
            '4' => 'ARCHIVO DE OBRADOS',
            '5' => 'PENDIENTE DE INFORME DE SEGUIMIENTO',
            '6' => 'PENDIENTE DE RESOLUCION'
        ];

        $this->cerrado_abierto = [
            '1' => 'ABIERTA',
            '2' => 'CERRADA'
        ];

        $this->solicitante = [
            '1' => 'MINISTERIO DE TRABAJO EMPLEO Y PREVISION SOCIAL',
            '2' => 'MINISTERIO DE JUSTICIA',
            '3' => 'MINSITERIO DE GOBIERNO',
            '4' => 'FISCAL GENERAL DEL ESTADO',
            '5' => 'FISCAL DEPARTAMENTAL',
            '6' => 'FISCAL DE MATERIA',
            '7' => 'CUALQUIER PERSONA'
        ];

        $this->etapa_proceso = [
            '1' => 'ETAPA PRELIMINAR',
            '2' => 'ETAPA PREPARATORIA',
            '3' => 'ETAPA DE JUICIO'
        ];

        $this->estado_pdf = [
            '1' => 'NO',
            '2' => 'SI'
        ];

        $this->usuario_tipo = [
            '1' => 'VICTIMA DIRECTA',
            '2' => 'VICTIMA INDIRECTA',
            '3' => 'TESTIGO',
            '4' => 'MIEMBRO DEL MINISTERIO PUBLICO',
            '5' => 'SERVIDOR PUBLICO',
            '6' => 'EX SERVIDOR PUBLICO',
            '7' => 'DENUNCIANTE'
        ];

        $this->sexo = [
            '1' => 'HOMBRE',
            '2' => 'MUJER'
        ];

        $this->edad = [
            '1' => '0-11 AÑOS',
            '2' => '12-17 AÑOS',
            '3' => '18-59 AÑOS',
            '4' => 'MAS DE 60 AÑOS'
        ];

        $this->dirigido_a = [
            '1' => 'UPVT',
            '2' => 'SLIM',
            '3' => 'DNA',
            '4' => 'SIGPLU',
            '5' => 'SEPDAVI',
            '6' => 'ADULTO MAYOR',
            '7' => 'OTRO'
        ];

        $this->dirigido_psicologia = [
            '1' => 'EVALUACION PSICOLOGICA DEL ESTADO COGNITIVO CONDUCTUAL Y EMOCIONAL CON RELACION AL HECHO',
            '2' => 'IDENTIFICACION DE LOS FACTORES DE RIESGO',
            '3' => 'IDENTIFICACION DE LAS NECESIDADES DE PROTECCION',
            '4' => 'OTRO'
        ];

        $this->dirigido_trabajo_social = [
            '1' => 'EVALUACION SOCIAL CON RELACION AL HECHO',
            '2' => 'IDENTIFICACION DE LOS FACTORES DE RIESGO',
            '3' => 'IDENTIFICACION DE LAS NECESIDADES DE PROTECCION',
            '4' => 'OTRO'
        ];

        $this->resolucion_tipo_disposicion = [
            '1' => 'OTORGA',
            '2' => 'NIEGA',
            '3' => 'AMPLIA',
            '4' => 'RETIRA/SUSPENDE',
            '5' => 'PRORROGA',
            '6' => 'ELIMINA'
        ];

        $this->resolucion_mpd = [
            '1' => 'PRESERVACION DE LA IDENTIDAD Y LA CONFIDENCIALIDAD DE LOS DATOS PERSONALES',
            '2' => 'PRESERVACION DE SUS DERECHOS LABORALES',
            '3' => 'PROTECCION POLICIAL PARA EL TRASLADO A FIN DE CUMPLIR DILIGENCIAS ADMINISTRATIVAS Y/O JUDICIALES',
            '4' => 'CUSTODIA POLICIAL EN EL DOMICILIO DE LA PERSONA',
            '5' => 'USO DE SISTEMAS TECNOLOGICOS QUE IMPIDAN QUE LA IDENTIDAD DE LA PERSONA SEA CONOCIDA',
            '6' => 'METODOS DE DISTORSION DEL ASPECTO FISICO O DE LA VOZ',
            '7' => 'ALOJAMIENTO TEMPORAL EN ALBERGUES DESTINADOS A PROTECCION DE VICTIMAS Y TESTIGOS; CUYA UBICACION DEBE SER RESERVADA Y CON CUSTODIA POLICIAL',
            '8' => 'ATENCION PSICOLOGICA',
            '9' => 'SEPARACION DEL RESTO DE LA POBLACION CARCELARIA O SU TRASLADO, BAJO RESERVA, A OTRO RECINTO PENITENCIARIO, DONDE SE LE BRINDE MAYOR SEGURIDAD EN EL CASO DE PERSONA PROTEGIDA QUE SE ENCUENTRE PRIVADA DE LIBERTAD'
        ];

        $this->public_dir = '/storage/dpvt/solicitud/pdf/';
        $this->public_url = 'storage/dpvt/solicitud/pdf/';
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

        if(in_array(['codigo' => '1901'], $this->permisos))
        {
            $data = [
                'rol_id'                            => $this->rol_id,
                'permisos'                          => $this->permisos,
                'title'                             => 'Medidas de protección',
                'home'                              => 'Inicio',
                'sistema'                           => 'DPVTMMP',
                'modulo'                            => 'Medidas de protección',
                'title_table'                       => 'Medidas de protección',
                'gestion_i'                         => 2012,
                'gestion_f'                         => date('Y'),
                'public_dir'                        => $this->public_dir,
                'estado_array'                      => $this->estado,
                'cerrado_abierto_array'             => $this->cerrado_abierto,
                'solicitante_array'                 => $this->solicitante,
                'etapa_proceso_array'               => $this->etapa_proceso,
                'estado_pdf_array'                  => $this->estado_pdf,
                'usuario_tipo_array'                => $this->usuario_tipo,
                'sexo_array'                        => $this->sexo,
                'edad_array'                        => $this->edad,
                'dirigido_a_array'                  => $this->dirigido_a,
                'dirigido_psicologia_array'         => $this->dirigido_psicologia,
                'dirigido_trabajo_social_array'     => $this->dirigido_trabajo_social,
                'resolucion_tipo_disposicion_array' => $this->resolucion_tipo_disposicion,
                'resolucion_mpd_array'              => $this->resolucion_mpd
            ];
            return view('dpvt.solicitud.solicitud')->with($data);
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

                $tabla1 = "pvt_solicitudes";
                $tabla2 = "rrhh_personas";
                $tabla3 = "ubge_municipios";
                $tabla4 = "ubge_provincias";
                $tabla5 = "ubge_departamentos";

                $select = "
                    $tabla1.id,
                    $tabla1.persona_id_solicitante,
                    $tabla1.municipio_id,

                    $tabla1.estado,
                    $tabla1.cerrado_abierto,
                    $tabla1.gestion,
                    $tabla1.codigo,

                    $tabla1.solicitante,
                    $tabla1.delitos,
                    $tabla1.recalificacion_delitos,
                    $tabla1.n_caso,
                    $tabla1.denunciante,
                    $tabla1.denunciado,
                    $tabla1.victima,
                    $tabla1.persona_protegida,
                    $tabla1.etapa_proceso,
                    $tabla1.f_solicitud,
                    $tabla1.solicitud_estado_pdf,
                    $tabla1.solicitud_documento_pdf,

                    $tabla1.usuario_tipo,
                    $tabla1.usuario_tipo_descripcion,
                    $tabla1.usuario_nombre,
                    $tabla1.usuario_sexo,
                    $tabla1.usuario_edad,
                    $tabla1.usuario_celular,
                    $tabla1.usuario_domicilio,
                    $tabla1.usuario_otra_referencia,

                    $tabla1.dirigido_a_psicologia,
                    $tabla1.dirigido_a_psicologia_1,
                    $tabla1.dirigido_psicologia,
                    $tabla1.dirigido_psicologia_1,
                    $tabla1.dirigido_psicologia_estado_pdf,
                    $tabla1.dirigido_psicologia_archivo_pdf,

                    $tabla1.dirigido_a_trabajo_social,
                    $tabla1.dirigido_a_trabajo_social_1,
                    $tabla1.dirigido_trabajo_social,
                    $tabla1.dirigido_trabajo_social_1,
                    $tabla1.dirigido_trabajo_social_estado_pdf,
                    $tabla1.dirigido_trabajo_social_archivo_pdf,

                    $tabla1.dirigido_a_otro_trabajo,
                    $tabla1.dirigido_a_otro_trabajo_1,
                    $tabla1.dirigido_otro_trabajo,
                    $tabla1.dirigido_otro_trabajo_estado_pdf,
                    $tabla1.dirigido_otro_trabajo_archivo_pdf,

                    $tabla1.complementario_dirigido_a,
                    $tabla1.complementario_dirigido_a_1,
                    $tabla1.complementario_trabajo_solicitado,
                    $tabla1.complementario_trabajo_solicitado_estado_pdf,
                    $tabla1.complementario_trabajo_solicitado_archivo_pdf,

                    $tabla1.plazo_fecha_solicitud,
                    $tabla1.plazo_fecha_recepcion,

                    $tabla1.plazo_psicologico_fecha_entrega_digital,
                    $tabla1.plazo_psicologico_fecha_entrega_fisico,
                    $tabla1.plazo_psicologico_estado_pdf,
                    $tabla1.plazo_psicologico_archivo_pdf,

                    $tabla1.plazo_social_fecha_entrega_digital,
                    $tabla1.plazo_social_fecha_entrega_fisico,
                    $tabla1.plazo_social_estado_pdf,
                    $tabla1.plazo_social_archivo_pdf,

                    $tabla1.plazo_complementario_fecha,
                    $tabla1.plazo_complementario_estado_pdf,
                    $tabla1.plazo_complementario_archivo_pdf,

                    $tabla1.created_at,
                    $tabla1.updated_at,

                    a2.n_documento,
                    a2.nombre,
                    a2.ap_paterno,
                    a2.ap_materno,

                    a3.nombre AS municipio,
                    a3.provincia_id,

                    a4.nombre AS provincia,
                    a4.departamento_id,

                    a5.nombre AS departamento
                ";

                $array_where = "TRUE";
                $array_where .= $jqgrid->getWhere();

                $count = PvtSolicitud::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.persona_id_solicitante")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.municipio_id")
                    ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.provincia_id")
                    ->leftJoin("$tabla5 AS a5", "a5.id", "=", "a4.departamento_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = PvtSolicitud::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.persona_id_solicitante")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.municipio_id")
                    ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.provincia_id")
                    ->leftJoin("$tabla5 AS a5", "a5.id", "=", "a4.departamento_id")
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
                        'persona_id_solicitante' => $row["persona_id_solicitante"],
                        'municipio_id'           => $row["municipio_id"],

                        'estado'          => $row["estado"],
                        'cerrado_abierto' => $row["cerrado_abierto"],

                        'solicitante'             => $row["solicitante"],
                        'etapa_proceso'           => $row["etapa_proceso"],
                        'solicitud_estado_pdf'    => $row["solicitud_estado_pdf"],
                        'solicitud_documento_pdf' => $row["solicitud_documento_pdf"],

                        'usuario_tipo'             => $row["usuario_tipo"],
                        'usuario_tipo_descripcion' => $row["usuario_tipo_descripcion"],
                        'usuario_nombre'           => $row["usuario_nombre"],
                        'usuario_sexo'             => $row["usuario_sexo"],
                        'usuario_edad'             => $row["usuario_edad"],
                        'usuario_celular'          => $row["usuario_celular"],
                        'usuario_domicilio'        => $row["usuario_domicilio"],
                        'usuario_otra_referencia'  => $row["usuario_otra_referencia"],

                        'dirigido_a_psicologia'           => $row["dirigido_a_psicologia"],
                        'dirigido_a_psicologia_1'         => $row["dirigido_a_psicologia_1"],
                        'dirigido_psicologia'             => $row["dirigido_psicologia"],
                        'dirigido_psicologia_1'           => $row["dirigido_psicologia_1"],
                        'dirigido_psicologia_estado_pdf'  => $row["dirigido_psicologia_estado_pdf"],
                        'dirigido_psicologia_archivo_pdf' => $row["dirigido_psicologia_archivo_pdf"],

                        'dirigido_a_trabajo_social'           => $row["dirigido_a_trabajo_social"],
                        'dirigido_a_trabajo_social_1'         => $row["dirigido_a_trabajo_social_1"],
                        'dirigido_trabajo_social'             => $row["dirigido_trabajo_social"],
                        'dirigido_trabajo_social_1'           => $row["dirigido_trabajo_social_1"],
                        'dirigido_trabajo_social_estado_pdf'  => $row["dirigido_trabajo_social_estado_pdf"],
                        'dirigido_trabajo_social_archivo_pdf' => $row["dirigido_trabajo_social_archivo_pdf"],

                        'dirigido_a_otro_trabajo'           => $row["dirigido_a_otro_trabajo"],
                        'dirigido_a_otro_trabajo_1'         => $row["dirigido_a_otro_trabajo_1"],
                        'dirigido_otro_trabajo'             => $row["dirigido_otro_trabajo"],
                        'dirigido_otro_trabajo_estado_pdf'  => $row["dirigido_otro_trabajo_estado_pdf"],
                        'dirigido_otro_trabajo_archivo_pdf' => $row["dirigido_otro_trabajo_archivo_pdf"],

                        'complementario_dirigido_a'                     => $row["complementario_dirigido_a"],
                        'complementario_dirigido_a_1'                   => $row["complementario_dirigido_a_1"],
                        'complementario_trabajo_solicitado'             => $row["complementario_trabajo_solicitado"],
                        'complementario_trabajo_solicitado_estado_pdf'  => $row["complementario_trabajo_solicitado_estado_pdf"],
                        'complementario_trabajo_solicitado_archivo_pdf' => $row["complementario_trabajo_solicitado_archivo_pdf"],

                        'plazo_fecha_solicitud' => $row["plazo_fecha_solicitud"],
                        'plazo_fecha_recepcion' => $row["plazo_fecha_recepcion"],

                        'plazo_psicologico_fecha_entrega_digital' => $row["plazo_psicologico_fecha_entrega_digital"],
                        'plazo_psicologico_fecha_entrega_fisico'  => $row["plazo_psicologico_fecha_entrega_fisico"],
                        'plazo_psicologico_estado_pdf'            => $row["plazo_psicologico_estado_pdf"],
                        'plazo_psicologico_archivo_pdf'           => $row["plazo_psicologico_archivo_pdf"],

                        'plazo_social_fecha_entrega_digital' => $row["plazo_social_fecha_entrega_digital"],
                        'plazo_social_fecha_entrega_fisico'  => $row["plazo_social_fecha_entrega_fisico"],
                        'plazo_social_estado_pdf'            => $row["plazo_social_estado_pdf"],
                        'plazo_social_archivo_pdf'           => $row["plazo_social_archivo_pdf"],

                        'plazo_complementario_fecha'       => $row["plazo_complementario_fecha"],
                        'plazo_complementario_estado_pdf'  => $row["plazo_complementario_estado_pdf"],
                        'plazo_complementario_archivo_pdf' => $row["plazo_complementario_archivo_pdf"],

                        'provincia_id'    => $row["provincia_id"],
                        'departamento_id' => $row["departamento_id"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',

                        ($row["estado"] =="") ? "" : $this->estado[$row["estado"]],
                        ($row["cerrado_abierto"] =="") ? "" : $this->cerrado_abierto[$row["cerrado_abierto"]],
                        $row["gestion"],
                        $row["codigo"],


                        ($row["solicitante"] =="") ? "" : $this->solicitante[$row["solicitante"]],
                        $row["n_documento"],
                        $row["nombre"],
                        $row["ap_paterno"],
                        $row["ap_materno"],
                        $row["municipio"],
                        $row["provincia"],
                        $row["departamento"],
                        $row["f_solicitud"],
                        ($row["solicitud_estado_pdf"] =="") ? "" : $this->estado_pdf[$row["solicitud_estado_pdf"]],

                        $row["n_caso"],
                        ($row["etapa_proceso"] =="") ? "" : $this->etapa_proceso[$row["etapa_proceso"]],
                        $row["denunciante"],
                        $row["denunciado"],
                        $row["victima"],
                        $row["persona_protegida"],

                        $row["delitos"],
                        $row["recalificacion_delitos"],

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
                        'titulo'     => '<div class="text-center"><strong>SOLICITUD</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );

                    $opcion      = 'n';
                    $anio_actual = date("Y");

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if($id != '')
                    {
                        $opcion = 'e';
                        if(!in_array(['codigo' => '1903'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '1902'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                // === VALIDATE ===
                    try
                    {
                        $validator = $this->validate($request,[
                            'gestion'           => 'required|integer',
                            // 'f_solicitud'       => 'date',
                            'n_caso'            => 'max:50',
                            'denunciante'       => 'max:500',
                            'denunciado'        => 'max:500',
                            'victima'           => 'max:500',
                            'persona_protegida' => 'max:500'
                        ],
                        [
                            'gestion.required' => 'El campo GESTION es obligatorio.',
                            'gestion.integer'  => 'El campo GESTION debe ser un número entero.',

                            // 'f_solicitud.date' => 'El campo FECHA DE SOLICITUD no corresponde a una fecha válida.',

                            'n_caso.max' => 'El campo NUMERO DE CASO debe contener :max caracteres como máximo.',

                            'denunciante.max' => 'El campo DENUNCIANTE debe contener :max caracteres como máximo.',

                            'denunciado.max' => 'El campo DENUNCIADO debe contener :max caracteres como máximo.',

                            'victima.max' => 'El campo VICTIMA debe contener :max caracteres como máximo.',

                            'persona_protegida.max' => 'El campo PERSONA PROTEGIDA debe contener :max caracteres como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['gestion']                = trim($request->input('gestion'));
                    $data1['solicitante']            = trim($request->input('solicitante'));
                    $data1['persona_id_solicitante'] = trim($request->input('persona_id_solicitante'));
                    $data1['municipio_id']           = trim($request->input('municipio_id'));
                    $data1['f_solicitud']            = trim($request->input('f_solicitud'));
                    $data1['n_caso']                 = strtoupper($util->getNoAcentoNoComilla(trim($request->input('n_caso'))));
                    $data1['etapa_proceso']          = trim($request->input('etapa_proceso'));
                    $data1['denunciante']            = strtoupper($util->getNoAcentoNoComilla(trim($request->input('denunciante'))));
                    $data1['denunciado']             = strtoupper($util->getNoAcentoNoComilla(trim($request->input('denunciado'))));
                    $data1['victima']                = strtoupper($util->getNoAcentoNoComilla(trim($request->input('victima'))));
                    $data1['persona_protegida']      = strtoupper($util->getNoAcentoNoComilla(trim($request->input('persona_protegida'))));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $iu                         = new PvtSolicitud;
                        $iu->gestion                = $data1['gestion'];
                        $iu->solicitante            = $data1['solicitante'];
                        $iu->persona_id_solicitante = $data1['persona_id_solicitante'];
                        $iu->municipio_id           = $data1['municipio_id'];
                        $iu->f_solicitud            = $data1['f_solicitud'];
                        $iu->n_caso                 = $data1['n_caso'];
                        $iu->etapa_proceso          = $data1['etapa_proceso'];
                        $iu->denunciante            = $data1['denunciante'];
                        $iu->denunciado             = $data1['denunciado'];
                        $iu->victima                = $data1['victima'];
                        $iu->persona_protegida      = $data1['persona_protegida'];
                        $iu->codigo                 = str_pad((PvtSolicitud::where('gestion', '=', $data1['gestion'])->count())+1, 4, "0", STR_PAD_LEFT) . "/" . $data1['gestion'];
                        $iu->save();

                        $id = $iu->id;

                        $respuesta['respuesta'] .= "La SOLICITUD fue registrada con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['id']         = $id;
                        $respuesta['codigo']     = $iu->codigo;
                    }
                    else
                    {
                        $iu                         = PvtSolicitud::find($id);
                        $iu->gestion                = $data1['gestion'];
                        $iu->solicitante            = $data1['solicitante'];
                        $iu->persona_id_solicitante = $data1['persona_id_solicitante'];
                        $iu->municipio_id           = $data1['municipio_id'];
                        $iu->f_solicitud            = $data1['f_solicitud'];
                        $iu->n_caso                 = $data1['n_caso'];
                        $iu->etapa_proceso          = $data1['etapa_proceso'];
                        $iu->denunciante            = $data1['denunciante'];
                        $iu->denunciado             = $data1['denunciado'];
                        $iu->victima                = $data1['victima'];
                        $iu->persona_protegida      = $data1['persona_protegida'];
                        $iu->save();

                        $respuesta['respuesta'] .= "La SOLICITUD se edito con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['iu']         = 2;
                    }
                return json_encode($respuesta);
                break;




            // === UPLOAD PDF ===
            case '11':
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
                    $id = trim($request->input('solicitud_id'));
                    if($id != '')
                    {
                        $opcion = 'e';
                        if(!in_array(['codigo' => '1903'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "La ID de la MEDIDA DE PROTECCION es obligatorio.";
                        return json_encode($respuesta);
                    }

                // === VALIDATE ===
                    $file_name = trim($request->input('file_name'));

                    try
                    {
                       $validator = $this->validate($request,[
                            $file_name => 'mimes:pdf|max:5120'
                        ],
                        [
                            $file_name . '.mimes' => 'El archivo subido debe de ser de tipo :values.',
                            $file_name . '.max'   => 'El archivo debe pesar 5120 kilobytes como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $col_name = trim($request->input('col_name'));

                    $consulta1 = PvtSolicitud::where('id', '=', $id)
                        ->select($col_name)
                        ->first()
                        ->toArray();
                    if($consulta1[$col_name] != '')
                    {
                        if(file_exists(public_path($this->public_dir) . '/' . $consulta1[$col_name]))
                        {
                            unlink(public_path($this->public_dir) . '/' . $consulta1[$col_name]);
                        }
                    }

                    if($request->hasFile($file_name))
                    {
                        $archivo = $request->file($file_name);

                        switch($request->input('tipo_file'))
                        {
                            case 1:
                                $nombre_archivo = uniqid('solicitud_', true) . '.' . $archivo->getClientOriginalExtension();
                                break;
                            default:
                                # code...
                                break;
                        }

                        $direccion_archivo = public_path($this->public_dir);

                        $archivo->move($direccion_archivo, $nombre_archivo);

                        $iu = PvtSolicitud::find($id);
                        switch($request->input('tipo_file'))
                        {
                            case 1:
                                $iu->solicitud_estado_pdf    = 2;
                                $iu->solicitud_documento_pdf = $nombre_archivo;
                                break;
                            default:
                                # code...
                                break;
                        }
                        $iu->save();

                        $respuesta['respuesta'] .= "El DOCUMENTO se subio con éxito.";
                        $respuesta['sw']         = 1;
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
            // === SELECT2 DEPARTAMENTO, PROVINCIA Y MUNICIPIO ===
            case '101':

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
                    // else
                    // {
                    //     return json_encode(array("id"=>"0","text"=>"No se encontraron resultados"));
                    // }
                }
                break;
            default:
                break;
        }
    }
}