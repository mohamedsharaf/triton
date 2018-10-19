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
use App\Models\Dpvt\PvtSolicitudComplementaria;

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
            '3' => 'MINISTERIO DE GOBIERNO',
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
            '4' => 'MAS DE 60 AÑOS',
            '5' => 'SIN DATOS'
        ];

        $this->dirigido_a = [
            '1'  => 'UPAVT',
            '2'  => 'SLIM',
            '3'  => 'DNA',
            '4'  => 'SIJPLU',
            '5'  => 'SEPDAVI',
            '6'  => 'IDIF',
            '7'  => 'CONALPEDIS',
            '8'  => 'CENTROS HOSPITALARIOS',
            '9'  => 'ONG',
            '10' => 'REGIMEN PENITENCIARIO',
            '11' => 'REGIMIENTO MILITAR',
            '12' => 'DIRECCION FGE',
            '13' => 'JEFATURA FGE',
            '14' => 'MINISTERIO',
            '15' => 'JEFATURA DE TRABAJO',
            '16' => 'DPVTMMP',
            '17' => 'OTRO'
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
                'public_url'                        => $this->public_url,
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
                $where_concatenar = "";
                if($request->has('anio_filter'))
                {
                    $where_concatenar = " AND pvt_solicitudes.gestion='" . $request->input('anio_filter') . "'";
                }

                $jqgrid = new JqgridClass($request);

                $tabla1 = "pvt_solicitudes";
                $tabla3 = "ubge_municipios";
                $tabla4 = "ubge_provincias";
                $tabla5 = "ubge_departamentos";

                $select = "
                    $tabla1.id,
                    $tabla1.municipio_id,

                    $tabla1.estado,
                    $tabla1.cerrado_abierto,
                    $tabla1.gestion,
                    $tabla1.codigo,

                    $tabla1.solicitante,
                    $tabla1.nombre_solicitante,
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
                    $tabla1.dirigido_psicologia,
                    $tabla1.dirigido_psicologia_estado_pdf,
                    $tabla1.dirigido_psicologia_archivo_pdf,

                    $tabla1.dirigido_a_trabajo_social,
                    $tabla1.dirigido_trabajo_social,
                    $tabla1.dirigido_trabajo_social_estado_pdf,
                    $tabla1.dirigido_trabajo_social_archivo_pdf,

                    $tabla1.dirigido_a_otro_trabajo,
                    $tabla1.dirigido_otro_trabajo,
                    $tabla1.dirigido_otro_trabajo_estado_pdf,
                    $tabla1.dirigido_otro_trabajo_archivo_pdf,

                    $tabla1.plazo_fecha_solicitud,

                    $tabla1.plazo_psicologico_fecha_entrega_digital,
                    $tabla1.plazo_psicologico_estado_pdf,
                    $tabla1.plazo_psicologico_archivo_pdf,

                    $tabla1.plazo_social_fecha_entrega_digital,
                    $tabla1.plazo_social_estado_pdf,
                    $tabla1.plazo_social_archivo_pdf,

                    $tabla1.plazo_complementario_fecha,
                    $tabla1.plazo_complementario_estado_pdf,
                    $tabla1.plazo_complementario_archivo_pdf,

                    $tabla1.created_at,
                    $tabla1.updated_at,

                    a3.nombre AS municipio,
                    a3.provincia_id,

                    a4.nombre AS provincia,
                    a4.departamento_id,

                    a5.nombre AS departamento
                ";

                $array_where = "TRUE" . $where_concatenar;
                $array_where .= $jqgrid->getWhere();

                $count = PvtSolicitud::leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.municipio_id")
                    ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.provincia_id")
                    ->leftJoin("$tabla5 AS a5", "a5.id", "=", "a4.departamento_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = PvtSolicitud::leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.municipio_id")
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
                        'municipio_id'       => $row["municipio_id"],

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
                        'dirigido_psicologia'             => $row["dirigido_psicologia"],
                        'dirigido_psicologia_estado_pdf'  => $row["dirigido_psicologia_estado_pdf"],
                        'dirigido_psicologia_archivo_pdf' => $row["dirigido_psicologia_archivo_pdf"],

                        'dirigido_a_trabajo_social'           => $row["dirigido_a_trabajo_social"],
                        'dirigido_trabajo_social'             => $row["dirigido_trabajo_social"],
                        'dirigido_trabajo_social_estado_pdf'  => $row["dirigido_trabajo_social_estado_pdf"],
                        'dirigido_trabajo_social_archivo_pdf' => $row["dirigido_trabajo_social_archivo_pdf"],

                        'dirigido_a_otro_trabajo'           => $row["dirigido_a_otro_trabajo"],
                        'dirigido_otro_trabajo'             => $row["dirigido_otro_trabajo"],
                        'dirigido_otro_trabajo_estado_pdf'  => $row["dirigido_otro_trabajo_estado_pdf"],
                        'dirigido_otro_trabajo_archivo_pdf' => $row["dirigido_otro_trabajo_archivo_pdf"],

                        'plazo_fecha_solicitud' => $row["plazo_fecha_solicitud"],

                        'plazo_psicologico_fecha_entrega_digital' => $row["plazo_psicologico_fecha_entrega_digital"],
                        'plazo_psicologico_estado_pdf'            => $row["plazo_psicologico_estado_pdf"],
                        'plazo_psicologico_archivo_pdf'           => $row["plazo_psicologico_archivo_pdf"],

                        'plazo_social_fecha_entrega_digital' => $row["plazo_social_fecha_entrega_digital"],
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
                        $this->utilitarios(array('tipo' => '2', 'cerrado_abierto' => $row["cerrado_abierto"])),
                        $row["gestion"],
                        $row["codigo"],


                        ($row["solicitante"] =="") ? "" : $this->solicitante[$row["solicitante"]],
                        $row["nombre_solicitante"],
                        $row["municipio"],
                        $row["provincia"],
                        $row["departamento"],
                        $row["f_solicitud"],
                        $this->utilitarios(array('tipo' => '1', 'estado_pdf' => $row["solicitud_estado_pdf"], 'id' => $row["id"], 'tipo_pdf' => 1)),

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
            case '2':
                $where_concatenar = "";
                if($request->has('solicitud_id'))
                {
                    $where_concatenar = " AND pvt_solicitudes_delitos.solicitud_id=" . $request->input('solicitud_id') . "";
                }

                $jqgrid = new JqgridClass($request);

                $tabla1 = "pvt_solicitudes_delitos";
                $tabla2 = "pvt_delitos";

                $select = "
                    $tabla1.id,

                    $tabla1.solicitud_id,
                    $tabla1.delito_id,

                    $tabla1.estado,
                    $tabla1.tentativa,

                    a2.nombre
                ";

                $array_where = "TRUE AND pvt_solicitudes_delitos.estado=1" . $where_concatenar;
                $array_where .= $jqgrid->getWhere();

                $count = PvtSolicitudDelito::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.delito_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = PvtSolicitudDelito::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.delito_id")
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
                        'solicitud_id' => $row["solicitud_id"],
                        'delito_id'    => $row["delito_id"],

                        'estado'    => $row["estado"],
                        'tentativa' => $row["tentativa"]
                    );

                    $respuesta['rows'][$i]['id']   = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',

                        $row["nombre"],
                        ($row["tentativa"] =="") ? "" : $this->estado_pdf[$row["tentativa"]],

                        //=== VARIABLES OCULTOS ===
                            json_encode($val_array)
                    );
                    $i++;
                }
                return json_encode($respuesta);
                break;
            case '3':
                $where_concatenar = "";
                if($request->has('solicitud_id'))
                {
                    $where_concatenar = " AND pvt_solicitudes_delitos.solicitud_id=" . $request->input('solicitud_id') . "";
                }

                $jqgrid = new JqgridClass($request);

                $tabla1 = "pvt_solicitudes_delitos";
                $tabla2 = "pvt_delitos";

                $select = "
                    $tabla1.id,

                    $tabla1.solicitud_id,
                    $tabla1.delito_id,

                    $tabla1.estado,
                    $tabla1.tentativa,

                    a2.nombre
                ";

                $array_where = "TRUE AND pvt_solicitudes_delitos.estado=2" . $where_concatenar;
                $array_where .= $jqgrid->getWhere();

                $count = PvtSolicitudDelito::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.delito_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = PvtSolicitudDelito::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.delito_id")
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
                        'solicitud_id' => $row["solicitud_id"],
                        'delito_id'    => $row["delito_id"],

                        'estado'    => $row["estado"],
                        'tentativa' => $row["tentativa"]
                    );

                    $respuesta['rows'][$i]['id']   = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',

                        $row["nombre"],
                        ($row["tentativa"] =="") ? "" : $this->estado_pdf[$row["tentativa"]],

                        //=== VARIABLES OCULTOS ===
                            json_encode($val_array)
                    );
                    $i++;
                }
                return json_encode($respuesta);
                break;
            case '4':
                $where_concatenar = "";
                if($request->has('solicitud_id'))
                {
                    $where_concatenar = " AND pvt_resoluciones.solicitud_id=" . $request->input('solicitud_id') . "";
                }

                $jqgrid = new JqgridClass($request);

                $tabla1 = "pvt_resoluciones";

                $select = "
                    $tabla1.id,

                    $tabla1.solicitud_id,

                    $tabla1.estado,

                    $tabla1.resolucion_descripcion,
                    $tabla1.resolucion_fecha_emision,
                    $tabla1.resolucion_estado_pdf,
                    $tabla1.resolucion_archivo_pdf,
                    $tabla1.resolucion_tipo_disposicion,
                    $tabla1.resolucion_medidas_proteccion,
                    $tabla1.resolucion_otra_medidas_proteccion,
                    $tabla1.resolucion_instituciones_coadyuvantes,
                    $tabla1.resolucion_estado_pdf_2,
                    $tabla1.resolucion_archivo_pdf_2,

                    $tabla1.fecha_inicio,
                    $tabla1.fecha_entrega_digital,
                    $tabla1.informe_seguimiento_fecha,
                    $tabla1.informe_seguimiento_estado_pdf,
                    $tabla1.informe_seguimiento_archivo_pdf,
                    $tabla1.complementario_fecha,
                    $tabla1.complementario_estado_pdf,
                    $tabla1.complementario_archivo_pdf
                ";

                $array_where = "TRUE" . $where_concatenar;
                $array_where .= $jqgrid->getWhere();

                $count = PvtResolucion::whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = PvtResolucion::whereRaw($array_where)
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
                        'solicitud_id'                    => $row["solicitud_id"],
                        'estado'                          => $row["estado"],
                        'resolucion_estado_pdf'           => $row["resolucion_estado_pdf"],
                        'resolucion_archivo_pdf'          => $row["resolucion_archivo_pdf"],
                        'resolucion_tipo_disposicion'     => $row["resolucion_tipo_disposicion"],
                        'resolucion_medidas_proteccion'   => $row["resolucion_medidas_proteccion"],
                        'resolucion_estado_pdf_2'         => $row["resolucion_estado_pdf_2"],
                        'resolucion_archivo_pdf_2'        => $row["resolucion_archivo_pdf_2"],
                        'informe_seguimiento_estado_pdf'  => $row["informe_seguimiento_estado_pdf"],
                        'informe_seguimiento_archivo_pdf' => $row["informe_seguimiento_archivo_pdf"],
                        'complementario_estado_pdf'       => $row["complementario_estado_pdf"],
                        'complementario_archivo_pdf'      => $row["complementario_archivo_pdf"]
                    );

                    $respuesta['rows'][$i]['id']   = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',

                        $row["resolucion_descripcion"],
                        $row["resolucion_fecha_emision"],
                        $this->utilitarios(array('tipo' => '7', 'estado_pdf' => $row["resolucion_estado_pdf"], 'id' => $row["id"], 'tipo_pdf' => 1)),
                        $this->utilitarios(array('tipo' => '8', 'tipo1' => '1', 'valor' => $row["resolucion_tipo_disposicion"])),
                        $this->utilitarios(array('tipo' => '8', 'tipo1' => '2', 'valor' => $row["resolucion_medidas_proteccion"])),
                        $row["resolucion_otra_medidas_proteccion"],
                        $row["resolucion_instituciones_coadyuvantes"],
                        $this->utilitarios(array('tipo' => '7', 'estado_pdf' => $row["resolucion_estado_pdf_2"], 'id' => $row["id"], 'tipo_pdf' => 2)),

                        $row["fecha_inicio"],
                        $row["fecha_entrega_digital"],
                        $row["informe_seguimiento_fecha"],
                        $this->utilitarios(array('tipo' => '7', 'estado_pdf' => $row["informe_seguimiento_estado_pdf"], 'id' => $row["id"], 'tipo_pdf' => 3)),
                        $row["complementario_fecha"],
                        $this->utilitarios(array('tipo' => '7', 'estado_pdf' => $row["complementario_estado_pdf"], 'id' => $row["id"], 'tipo_pdf' => 4)),

                        //=== VARIABLES OCULTOS ===
                            json_encode($val_array)
                    );
                    $i++;
                }
                return json_encode($respuesta);
                break;
            case '5':
                $where_concatenar = "";
                if($request->has('solicitud_id'))
                {
                    $where_concatenar = " AND pvt_solicitudes_complementarias.solicitud_id=" . $request->input('solicitud_id') . "";
                }

                $jqgrid = new JqgridClass($request);

                $tabla1 = "pvt_solicitudes_complementarias";

                $select = "
                    $tabla1.id,

                    $tabla1.solicitud_id,

                    $tabla1.estado,

                    $tabla1.complementario_dirigido_a,
                    $tabla1.complementario_trabajo_solicitado,
                    $tabla1.complementario_estado_pdf,
                    $tabla1.complementario_archivo_pdf
                ";

                $array_where = "TRUE" . $where_concatenar;
                $array_where .= $jqgrid->getWhere();

                $count = PvtSolicitudComplementaria::whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = PvtSolicitudComplementaria::whereRaw($array_where)
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
                        'solicitud_id'               => $row["solicitud_id"],
                        'estado'                     => $row["estado"],
                        'complementario_estado_pdf'  => $row["complementario_estado_pdf"],
                        'complementario_archivo_pdf' => $row["complementario_archivo_pdf"]
                    );

                    $respuesta['rows'][$i]['id']   = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',

                        $this->utilitarios(array('tipo' => '6', 'estado_pdf' => $row["complementario_estado_pdf"], 'id' => $row["id"], 'tipo_pdf' => 1)),
                        $row["complementario_dirigido_a"],
                        $row["complementario_trabajo_solicitado"],

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
            // === PASO 1 - INSERT UPDATE ===
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

                            'persona_protegida.max' => 'El campo USUARIO debe contener :max caracteres como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['gestion']            = trim($request->input('gestion'));
                    $data1['solicitante']        = trim($request->input('solicitante'));
                    $data1['nombre_solicitante'] = strtoupper($util->getNoAcentoNoComilla(trim($request->input('nombre_solicitante'))));
                    $data1['municipio_id']       = trim($request->input('municipio_id'));
                    $data1['f_solicitud']        = trim($request->input('f_solicitud'));
                    $data1['n_caso']             = strtoupper($util->getNoAcentoNoComilla(trim($request->input('n_caso'))));
                    $data1['etapa_proceso']      = trim($request->input('etapa_proceso'));
                    $data1['denunciante']        = strtoupper($util->getNoAcentoNoComilla(trim($request->input('denunciante'))));
                    $data1['denunciado']         = strtoupper($util->getNoAcentoNoComilla(trim($request->input('denunciado'))));
                    $data1['victima']            = strtoupper($util->getNoAcentoNoComilla(trim($request->input('victima'))));
                    $data1['persona_protegida']  = strtoupper($util->getNoAcentoNoComilla(trim($request->input('persona_protegida'))));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $iu                     = new PvtSolicitud;
                        $iu->gestion            = $data1['gestion'];
                        $iu->solicitante        = $data1['solicitante'];
                        $iu->nombre_solicitante = $data1['nombre_solicitante'];
                        $iu->municipio_id       = $data1['municipio_id'];
                        $iu->f_solicitud        = $data1['f_solicitud'];
                        $iu->n_caso             = $data1['n_caso'];
                        $iu->etapa_proceso      = $data1['etapa_proceso'];
                        $iu->denunciante        = $data1['denunciante'];
                        $iu->denunciado         = $data1['denunciado'];
                        $iu->victima            = $data1['victima'];
                        $iu->persona_protegida  = $data1['persona_protegida'];
                        $iu->codigo             = str_pad((PvtSolicitud::where('gestion', '=', $data1['gestion'])->count())+1, 4, "0", STR_PAD_LEFT) . "/" . $data1['gestion'];
                        $iu->save();

                        $id = $iu->id;

                        $respuesta['respuesta'] .= "La SOLICITUD fue registrada con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['id']         = $id;
                        $respuesta['codigo']     = $iu->codigo;
                    }
                    else
                    {
                        $iu                     = PvtSolicitud::find($id);
                        $iu->gestion            = $data1['gestion'];
                        $iu->solicitante        = $data1['solicitante'];
                        $iu->nombre_solicitante = $data1['nombre_solicitante'];
                        $iu->municipio_id       = $data1['municipio_id'];
                        $iu->f_solicitud        = $data1['f_solicitud'];
                        $iu->n_caso             = $data1['n_caso'];
                        $iu->etapa_proceso      = $data1['etapa_proceso'];
                        $iu->denunciante        = $data1['denunciante'];
                        $iu->denunciado         = $data1['denunciado'];
                        $iu->victima            = $data1['victima'];
                        $iu->persona_protegida  = $data1['persona_protegida'];
                        $iu->save();

                        $respuesta['respuesta'] .= "La SOLICITUD se edito con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['iu']         = 2;
                    }
                return json_encode($respuesta);
                break;
            // === PASO 2 - UPDATE ===
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
                        'titulo'     => '<div class="text-center"><strong>USUARIO</strong></div>',
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
                            'usuario_tipo_descripcion' => 'max:1000',
                            'usuario_nombre'           => 'max:1000',
                            'usuario_celular'          => 'max:100',
                            'usuario_domicilio'        => 'max:500',
                            'usuario_otra_referencia'  => 'max:500'
                        ],
                        [
                            'usuario_tipo_descripcion.max' => 'El campo TIPO DESCRIPCION debe contener :max caracteres como máximo.',

                            'usuario_nombre.max' => 'El campo NOMBRE DE USUARIO debe contener :max caracteres como máximo.',

                            'usuario_celular.max' => 'El campo TELEFONO Y/O CELULAR debe contener :max caracteres como máximo.',

                            'usuario_domicilio.max' => 'El campo DOMICILIO USUARIO debe contener :max caracteres como máximo.',

                            'usuario_otra_referencia.max' => 'El campo OTRAS REFERENCIAS debe contener :max caracteres como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['usuario_tipo']             = $request->input('usuario_tipo');
                    $data1['usuario_tipo_descripcion'] = strtoupper($util->getNoAcentoNoComilla(trim($request->input('usuario_tipo_descripcion'))));
                    $data1['usuario_nombre']           = strtoupper($util->getNoAcentoNoComilla(trim($request->input('usuario_nombre'))));
                    $data1['usuario_sexo']             = trim($request->input('usuario_sexo'));
                    $data1['usuario_celular']          = strtoupper($util->getNoAcentoNoComilla(trim($request->input('usuario_celular'))));
                    $data1['usuario_domicilio']        = strtoupper($util->getNoAcentoNoComilla(trim($request->input('usuario_domicilio'))));
                    $data1['usuario_otra_referencia']  = strtoupper($util->getNoAcentoNoComilla(trim($request->input('usuario_otra_referencia'))));
                    $data1['usuario_edad']             = trim($request->input('usuario_edad'));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $iu                           = new PvtSolicitud;
                        $iu->usuario_tipo             = $data1['usuario_tipo'];
                        $iu->usuario_tipo_descripcion = $data1['usuario_tipo_descripcion'];
                        $iu->usuario_nombre           = $data1['usuario_nombre'];
                        $iu->usuario_sexo             = $data1['usuario_sexo'];
                        $iu->usuario_celular          = $data1['usuario_celular'];
                        $iu->usuario_domicilio        = $data1['usuario_domicilio'];
                        $iu->usuario_otra_referencia  = $data1['usuario_otra_referencia'];
                        $iu->usuario_edad             = $data1['usuario_edad'];
                        $iu->save();

                        $id = $iu->id;

                        $respuesta['respuesta'] .= "El USUARIO fue registrada con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['id']         = $id;
                        $respuesta['codigo']     = $iu->codigo;
                    }
                    else
                    {
                        $iu                           = PvtSolicitud::find($id);
                        $iu->usuario_tipo             = $data1['usuario_tipo'];
                        $iu->usuario_tipo_descripcion = $data1['usuario_tipo_descripcion'];
                        $iu->usuario_nombre           = $data1['usuario_nombre'];
                        $iu->usuario_sexo             = $data1['usuario_sexo'];
                        $iu->usuario_celular          = $data1['usuario_celular'];
                        $iu->usuario_domicilio        = $data1['usuario_domicilio'];
                        $iu->usuario_otra_referencia  = $data1['usuario_otra_referencia'];
                        $iu->usuario_edad             = $data1['usuario_edad'];
                        $iu->save();

                        $respuesta['respuesta'] .= "El USUARIO se edito con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['iu']         = 2;
                    }
                return json_encode($respuesta);
                break;
            // === PASO 3 - UPDATE ===
            case '3':
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
                        'titulo'     => '<div class="text-center"><strong>SOLICITUD DE TRABAJO</strong></div>',
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
                            'dirigido_otro_trabajo' => 'max:1000'
                        ],
                        [
                            'dirigido_otro_trabajo.max' => 'El campo OTRO TRABAJO SOLICITADO debe contener :max caracteres como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['dirigido_a_psicologia']     = $request->input('dirigido_a_psicologia');
                    $data1['dirigido_psicologia']       = $request->input('dirigido_psicologia');
                    $data1['dirigido_a_trabajo_social'] = $request->input('dirigido_a_trabajo_social');
                    $data1['dirigido_trabajo_social']   = $request->input('dirigido_trabajo_social');
                    $data1['dirigido_a_otro_trabajo']   = $request->input('dirigido_a_otro_trabajo');
                    $data1['dirigido_otro_trabajo']     = strtoupper($util->getNoAcentoNoComilla(trim($request->input('dirigido_otro_trabajo'))));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $iu                            = new PvtSolicitud;
                        $iu->dirigido_a_psicologia     = $data1['dirigido_a_psicologia'];
                        $iu->dirigido_psicologia       = $data1['dirigido_psicologia'];
                        $iu->dirigido_a_trabajo_social = $data1['dirigido_a_trabajo_social'];
                        $iu->dirigido_trabajo_social   = $data1['dirigido_trabajo_social'];
                        $iu->dirigido_a_otro_trabajo   = $data1['dirigido_a_otro_trabajo'];
                        $iu->dirigido_otro_trabajo     = $data1['dirigido_otro_trabajo'];
                        $iu->save();

                        $id = $iu->id;

                        $respuesta['respuesta'] .= "El SOLICITUD DE TRABAJO fue registrada con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['id']         = $id;
                        $respuesta['codigo']     = $iu->codigo;
                    }
                    else
                    {
                        $iu                            = PvtSolicitud::find($id);
                        $iu->dirigido_a_psicologia     = $data1['dirigido_a_psicologia'];
                        $iu->dirigido_psicologia       = $data1['dirigido_psicologia'];
                        $iu->dirigido_a_trabajo_social = $data1['dirigido_a_trabajo_social'];
                        $iu->dirigido_trabajo_social   = $data1['dirigido_trabajo_social'];
                        $iu->dirigido_a_otro_trabajo   = $data1['dirigido_a_otro_trabajo'];
                        $iu->dirigido_otro_trabajo     = $data1['dirigido_otro_trabajo'];
                        $iu->save();

                        $respuesta['respuesta'] .= "El SOLICITUD DE TRABAJO se edito con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['iu']         = 2;
                    }
                return json_encode($respuesta);
                break;
            // === PASO 4 - UPDATE ===
            case '4':
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
                        'titulo'     => '<div class="text-center"><strong>SOLICITUD TRABAJO COMPLEMENTARIO</strong></div>',
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
                            'complementario_trabajo_solicitado' => 'max:1000'
                        ],
                        [
                            'complementario_trabajo_solicitado.max' => 'El campo TRABAJO SOLICITADO debe contener :max caracteres como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['estado']                            = $request->input('estado');

                    if($data1['estado'] ==  '')
                    {
                        $data1['estado'] = 1;
                    }

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $iu         = new PvtSolicitud;
                        $iu->estado = $data1['estado'];
                        $iu->save();

                        $id = $iu->id;

                        $respuesta['respuesta'] .= "La SOLICITUD TRABAJO COMPLEMENTARIO fue registrada con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['id']         = $id;
                        $respuesta['codigo']     = $iu->codigo;
                    }
                    else
                    {
                        $iu         = PvtSolicitud::find($id);
                        $iu->estado = $data1['estado'];
                        $iu->save();

                        $respuesta['respuesta'] .= "La SOLICITUD TRABAJO COMPLEMENTARIO se edito con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['iu']         = 2;
                    }
                return json_encode($respuesta);
                break;
            // === PASO 5 - UPDATE ===
            case '5':
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
                        'titulo'     => '<div class="text-center"><strong>PRESENTACION DE INFORMES</strong></div>',
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
                            'complementario_trabajo_solicitado' => 'max:1000'
                        ],
                        [
                            'complementario_trabajo_solicitado.max' => 'El campo TRABAJO SOLICITADO debe contener :max caracteres como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['plazo_fecha_solicitud']                   = trim($request->input('plazo_fecha_solicitud'));
                    $data1['plazo_psicologico_fecha_entrega_digital'] = trim($request->input('plazo_psicologico_fecha_entrega_digital'));
                    $data1['plazo_social_fecha_entrega_digital']      = trim($request->input('plazo_social_fecha_entrega_digital'));
                    $data1['plazo_complementario_fecha']              = trim($request->input('plazo_complementario_fecha'));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $iu                                          = new PvtSolicitud;
                        $iu->plazo_fecha_solicitud                   = $data1['plazo_fecha_solicitud'];
                        $iu->plazo_psicologico_fecha_entrega_digital = $data1['plazo_psicologico_fecha_entrega_digital'];
                        $iu->plazo_social_fecha_entrega_digital      = $data1['plazo_social_fecha_entrega_digital'];
                        $iu->plazo_complementario_fecha              = $data1['plazo_complementario_fecha'];
                        $iu->save();

                        $id = $iu->id;

                        $respuesta['respuesta'] .= "La PRESENTACION DE INFORMES fue registrada con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['id']         = $id;
                        $respuesta['codigo']     = $iu->codigo;
                    }
                    else
                    {
                        $iu                                          = PvtSolicitud::find($id);
                        $iu->plazo_fecha_solicitud                   = $data1['plazo_fecha_solicitud'];
                        $iu->plazo_psicologico_fecha_entrega_digital = $data1['plazo_psicologico_fecha_entrega_digital'];
                        $iu->plazo_social_fecha_entrega_digital      = $data1['plazo_social_fecha_entrega_digital'];
                        $iu->plazo_complementario_fecha              = $data1['plazo_complementario_fecha'];
                        $iu->save();

                        $respuesta['respuesta'] .= "La PRESENTACION DE INFORMES se edito con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['iu']         = 2;
                    }
                return json_encode($respuesta);
                break;

            // === UPLOAD PDF - 1 ===
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
                            case 2:
                                $nombre_archivo = uniqid('dirigido_psicologia_', true) . '.' . $archivo->getClientOriginalExtension();
                                break;
                            case 3:
                                $nombre_archivo = uniqid('dirigido_trabajo_social_', true) . '.' . $archivo->getClientOriginalExtension();
                                break;
                            case 4:
                                $nombre_archivo = uniqid('dirigido_otro_trabajo_', true) . '.' . $archivo->getClientOriginalExtension();
                                break;
                            case 5:
                                $nombre_archivo = uniqid('complementario_trabajo_solicitado_', true) . '.' . $archivo->getClientOriginalExtension();
                                break;
                            case 6:
                                $nombre_archivo = uniqid('plazo_psicologico_', true) . '.' . $archivo->getClientOriginalExtension();
                                break;
                            case 7:
                                $nombre_archivo = uniqid('plazo_social_', true) . '.' . $archivo->getClientOriginalExtension();
                                break;
                            case 8:
                                $nombre_archivo = uniqid('plazo_complementario_', true) . '.' . $archivo->getClientOriginalExtension();
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
                            case 2:
                                $iu->dirigido_psicologia_estado_pdf  = 2;
                                $iu->dirigido_psicologia_archivo_pdf = $nombre_archivo;
                                break;
                            case 3:
                                $iu->dirigido_trabajo_social_estado_pdf  = 2;
                                $iu->dirigido_trabajo_social_archivo_pdf = $nombre_archivo;
                                break;
                            case 4:
                                $iu->dirigido_otro_trabajo_estado_pdf  = 2;
                                $iu->dirigido_otro_trabajo_archivo_pdf = $nombre_archivo;
                                break;
                            case 5:
                                $iu->complementario_trabajo_solicitado_estado_pdf  = 2;
                                $iu->complementario_trabajo_solicitado_archivo_pdf = $nombre_archivo;
                                break;
                            case 6:
                                $iu->plazo_psicologico_estado_pdf  = 2;
                                $iu->plazo_psicologico_archivo_pdf = $nombre_archivo;
                                break;
                            case 7:
                                $iu->plazo_social_estado_pdf  = 2;
                                $iu->plazo_social_archivo_pdf = $nombre_archivo;
                                break;
                            case 8:
                                $iu->plazo_complementario_estado_pdf  = 2;
                                $iu->plazo_complementario_archivo_pdf = $nombre_archivo;
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
            // === ELIMINAR PDF - 1 ===
            case '12':
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
                        'titulo'     => '<div class="text-center"><strong>ELIMINAR PDF</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'error_sw'   => 1
                    );

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if($id != '')
                    {
                        if(!in_array(['codigo' => '1903'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para ELIMINAR PDF.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "No se tiene CODIGO.";
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    if($request->has('tipo_del'))
                    {
                        $select = "
                            id,
                            municipio_id,

                            estado,
                            cerrado_abierto,
                            gestion,
                            codigo,

                            solicitante,
                            nombre_solicitante,
                            delitos,
                            recalificacion_delitos,
                            n_caso,
                            denunciante,
                            denunciado,
                            victima,
                            persona_protegida,
                            etapa_proceso,
                            f_solicitud,
                            solicitud_estado_pdf,
                            solicitud_documento_pdf,

                            usuario_tipo,
                            usuario_tipo_descripcion,
                            usuario_nombre,
                            usuario_sexo,
                            usuario_edad,
                            usuario_celular,
                            usuario_domicilio,
                            usuario_otra_referencia,

                            dirigido_a_psicologia,
                            dirigido_psicologia,
                            dirigido_psicologia_estado_pdf,
                            dirigido_psicologia_archivo_pdf,

                            dirigido_a_trabajo_social,
                            dirigido_trabajo_social,
                            dirigido_trabajo_social_estado_pdf,
                            dirigido_trabajo_social_archivo_pdf,

                            dirigido_a_otro_trabajo,
                            dirigido_otro_trabajo,
                            dirigido_otro_trabajo_estado_pdf,
                            dirigido_otro_trabajo_archivo_pdf,

                            plazo_fecha_solicitud,

                            plazo_psicologico_fecha_entrega_digital,
                            plazo_psicologico_estado_pdf,
                            plazo_psicologico_archivo_pdf,

                            plazo_social_fecha_entrega_digital,
                            plazo_social_estado_pdf,
                            plazo_social_archivo_pdf,

                            plazo_complementario_fecha,
                            plazo_complementario_estado_pdf,
                            plazo_complementario_archivo_pdf,

                            created_at,
                            updated_at
                        ";

                        $consulta1 = PvtSolicitud::where('id', '=', $id)
                            ->select(DB::raw($select))
                            ->first();

                        $del_sw   = FALSE;
                        $del_file = '';

                        switch($request->input('tipo_del'))
                        {
                            case '1':
                                if($consulta1['solicitud_documento_pdf'] != '')
                                {
                                    $del_sw   = TRUE;
                                    $del_file = $consulta1['solicitud_documento_pdf'];

                                    $iu                          = PvtSolicitud::find($id);
                                    $iu->solicitud_estado_pdf    = 1;
                                    $iu->solicitud_documento_pdf = NULL;
                                    $iu->save();
                                }
                                break;
                            case '2':
                                if($consulta1['dirigido_psicologia_archivo_pdf'] != '')
                                {
                                    $del_sw   = TRUE;
                                    $del_file = $consulta1['dirigido_psicologia_archivo_pdf'];

                                    $iu                                  = PvtSolicitud::find($id);
                                    $iu->dirigido_psicologia_estado_pdf  = 1;
                                    $iu->dirigido_psicologia_archivo_pdf = NULL;
                                    $iu->save();
                                }
                                break;
                            case '3':
                                if($consulta1['dirigido_trabajo_social_archivo_pdf'] != '')
                                {
                                    $del_sw   = TRUE;
                                    $del_file = $consulta1['dirigido_trabajo_social_archivo_pdf'];

                                    $iu                                      = PvtSolicitud::find($id);
                                    $iu->dirigido_trabajo_social_estado_pdf  = 1;
                                    $iu->dirigido_trabajo_social_archivo_pdf = NULL;
                                    $iu->save();
                                }
                                break;
                            case '4':
                                if($consulta1['dirigido_otro_trabajo_archivo_pdf'] != '')
                                {
                                    $del_sw   = TRUE;
                                    $del_file = $consulta1['dirigido_otro_trabajo_archivo_pdf'];

                                    $iu                                    = PvtSolicitud::find($id);
                                    $iu->dirigido_otro_trabajo_estado_pdf  = 1;
                                    $iu->dirigido_otro_trabajo_archivo_pdf = NULL;
                                    $iu->save();
                                }
                                break;
                            case '5':
                                if($consulta1['plazo_psicologico_archivo_pdf'] != '')
                                {
                                    $del_sw   = TRUE;
                                    $del_file = $consulta1['plazo_psicologico_archivo_pdf'];

                                    $iu                                = PvtSolicitud::find($id);
                                    $iu->plazo_psicologico_estado_pdf  = 1;
                                    $iu->plazo_psicologico_archivo_pdf = NULL;
                                    $iu->save();
                                }
                                break;
                            case '6':
                                if($consulta1['plazo_social_archivo_pdf'] != '')
                                {
                                    $del_sw   = TRUE;
                                    $del_file = $consulta1['plazo_social_archivo_pdf'];

                                    $iu                           = PvtSolicitud::find($id);
                                    $iu->plazo_social_estado_pdf  = 1;
                                    $iu->plazo_social_archivo_pdf = NULL;
                                    $iu->save();
                                }
                                break;
                            case '7':
                                if($consulta1['plazo_complementario_archivo_pdf'] != '')
                                {
                                    $del_sw   = TRUE;
                                    $del_file = $consulta1['plazo_complementario_archivo_pdf'];

                                    $iu                                   = PvtSolicitud::find($id);
                                    $iu->plazo_complementario_estado_pdf  = 1;
                                    $iu->plazo_complementario_archivo_pdf = NULL;
                                    $iu->save();
                                }
                                break;
                            default:
                                # code...
                                break;
                        }

                        if($del_sw)
                        {
                            if(file_exists(public_path($this->public_dir) . '/' . $del_file))
                            {
                                unlink(public_path($this->public_dir) . '/' . $del_file);
                            }

                            $respuesta['respuesta'] .= "Se ELIMINO con éxito.";
                            $respuesta['sw']        = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El PDF no existe.";
                        }
                    }
                return json_encode($respuesta);
                break;
            // === UPLOAD PDF - 2 ===
            case '13':
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
                    $id = trim($request->input('solicitud_complementaria_id'));
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
                    $file_name = trim($request->input('file_name'));

                    try
                    {
                       $validator = $this->validate($request,[
                            'solicitud_id'                      => 'required',
                            'complementario_dirigido_a'         => 'required|max:1000',
                            'complementario_trabajo_solicitado' => 'required|max:1000',
                            $file_name                          => 'mimes:pdf|max:5120'
                        ],
                        [
                            'solicitud_id.required' => 'El campo SOLICITUD es obligatorio.',

                            'complementario_dirigido_a.required' => 'El campo DIRIGIDO A es obligatorio.',
                            'complementario_dirigido_a.max'      => 'El campo DIRIGIDO A debe contener :max caracteres como máximo.',

                            'complementario_trabajo_solicitado.required' => 'El campo TRABAJO SOLICITADO es obligatorio.',
                            'complementario_trabajo_solicitado.max'      => 'El campo TRABAJO SOLICITADO debe contener :max caracteres como máximo.',

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
                    $data1['solicitud_id']                      = trim($request->input('solicitud_id'));
                    $data1['complementario_dirigido_a']         = strtoupper($util->getNoAcentoNoComilla(trim($request->input('complementario_dirigido_a'))));
                    $data1['complementario_trabajo_solicitado'] = strtoupper($util->getNoAcentoNoComilla(trim($request->input('complementario_trabajo_solicitado'))));

                    $col_name = trim($request->input('col_name'));

                    if($opcion == 'n')
                    {
                        if($request->hasFile($file_name))
                        {
                            $archivo = $request->file($file_name);

                            switch($request->input('tipo_file'))
                            {
                                case 1:
                                    $nombre_archivo = uniqid('solicitud_complementaria_', true) . '.' . $archivo->getClientOriginalExtension();
                                    break;
                                default:
                                    # code...
                                    break;
                            }

                            $direccion_archivo = public_path($this->public_dir);

                            $archivo->move($direccion_archivo, $nombre_archivo);

                            $iu                                    = new PvtSolicitudComplementaria;
                            $iu->solicitud_id                      = $data1['solicitud_id'];
                            $iu->complementario_dirigido_a         = $data1['complementario_dirigido_a'];
                            $iu->complementario_trabajo_solicitado = $data1['complementario_trabajo_solicitado'];
                            switch($request->input('tipo_file'))
                            {
                                case 1:
                                    $iu->complementario_estado_pdf    = 2;
                                    $iu->complementario_archivo_pdf = $nombre_archivo;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                            $iu->save();

                            $respuesta['respuesta'] .= "El DOCUMENTO se subio con éxito, además, fue agregada nueva fila.";
                            $respuesta['sw']         = 1;

                            $id              = $iu->id;
                            $respuesta['id'] = $id;
                        }
                    }
                    else
                    {
                        $consulta1 = PvtSolicitudComplementaria::where('id', '=', $id)
                            ->select($col_name)
                            ->first();
                        if(count($consulta1) > 0)
                        {
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
                                        $nombre_archivo = uniqid('solicitud_complementaria_', true) . '.' . $archivo->getClientOriginalExtension();
                                        break;
                                        break;
                                    default:
                                        # code...
                                        break;
                                }

                                $direccion_archivo = public_path($this->public_dir);

                                $archivo->move($direccion_archivo, $nombre_archivo);

                                $iu                                    = PvtSolicitudComplementaria::find($id);
                                $iu->solicitud_id                      = $data1['solicitud_id'];
                                $iu->complementario_dirigido_a         = $data1['complementario_dirigido_a'];
                                $iu->complementario_trabajo_solicitado = $data1['complementario_trabajo_solicitado'];
                                switch($request->input('tipo_file'))
                                {
                                    case 1:
                                        $iu->complementario_estado_pdf    = 2;
                                        $iu->complementario_archivo_pdf = $nombre_archivo;
                                        break;
                                    default:
                                        # code...
                                        break;
                                }
                                $iu->save();

                                $respuesta['respuesta'] .= "El DOCUMENTO se subio con éxito, además, se edito.";
                                $respuesta['sw']         = 1;

                                $respuesta['id'] = $id;
                            }
                        }
                    }

                return json_encode($respuesta);
                break;
            // === ELIMINAR PDF - 2 ===
            case '14':
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
                        'titulo'     => '<div class="text-center"><strong>ELIMINAR PDF</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'error_sw'   => 1
                    );

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if($id != '')
                    {
                        if(!in_array(['codigo' => '1903'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para ELIMINAR PDF.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "No existe la SOLICITUD DE TRABAJO COMPLEMENTARIA.";
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    if($request->has('tipo_del'))
                    {
                        $select = "
                            id,

                            complementario_estado_pdf,
                            complementario_archivo_pdf
                        ";

                        $consulta1 = PvtSolicitudComplementaria::where('id', '=', $id)
                            ->select(DB::raw($select))
                            ->first();

                        $del_sw   = FALSE;
                        $del_file = '';

                        switch($request->input('tipo_del'))
                        {
                            case '1':
                                if($consulta1['complementario_archivo_pdf'] != '')
                                {
                                    $del_sw   = TRUE;
                                    $del_file = $consulta1['complementario_archivo_pdf'];

                                    $iu                             = PvtSolicitudComplementaria::find($id);
                                    $iu->complementario_estado_pdf  = 1;
                                    $iu->complementario_archivo_pdf = NULL;
                                    $iu->save();
                                }
                                break;
                            default:
                                # code...
                                break;
                        }

                        if($del_sw)
                        {
                            if(file_exists(public_path($this->public_dir) . '/' . $del_file))
                            {
                                unlink(public_path($this->public_dir) . '/' . $del_file);
                            }

                            $respuesta['respuesta'] .= "Se ELIMINO con éxito.";
                            $respuesta['sw']        = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El PDF no existe.";
                        }
                    }
                return json_encode($respuesta);
                break;
            // === UPLOAD PDF - 3 ===
            case '15':
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
                    $id = trim($request->input('resolucion_id'));
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
                    $file_name = trim($request->input('file_name'));

                    try
                    {
                       $validator = $this->validate($request,[
                            'solicitud_id'                          => 'required',
                            'resolucion_descripcion'                => 'required|max:500',
                            'resolucion_tipo_disposicion'           => 'max:50',
                            'resolucion_medidas_proteccion'         => 'max:50',
                            'resolucion_otra_medidas_proteccion'    => 'max:1000',
                            'resolucion_instituciones_coadyuvantes' => 'max:1000',
                            $file_name                              => 'mimes:pdf|max:5120'
                        ],
                        [
                            'solicitud_id.required' => 'MEDIDAS DE PROTECCION debe de existir.',

                            'resolucion_descripcion.required' => 'El campo DESCRIPCION DE LA RESOLUCION es obligatorio.',
                            'resolucion_descripcion.max'      => 'El campo DESCRIPCION DE LA RESOLUCION debe contener :max caracteres como máximo.',

                            'resolucion_tipo_disposicion.max' => 'El campo TIPO DE DISPOSICION debe contener :max caracteres como máximo.',

                            'resolucion_medidas_proteccion.max' => 'El campo MEDIDA DE PROTECCION DISPUESTA debe contener :max caracteres como máximo.',

                            'resolucion_otra_medidas_proteccion.max' => 'El campo OTRA MEDIDA DE PROTECCION DISPUESTA debe contener :max caracteres como máximo.',

                            'resolucion_instituciones_coadyuvantes.max' => 'El campo INSTITUCION COADYUVANTE debe contener :max caracteres como máximo.',

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
                    $data1['solicitud_id']                          = trim($request->input('solicitud_id'));
                    $data1['resolucion_descripcion']                = strtoupper($util->getNoAcentoNoComilla(trim($request->input('resolucion_descripcion'))));
                    $data1['resolucion_fecha_emision']              = trim($request->input('resolucion_fecha_emision'));
                    $data1['resolucion_tipo_disposicion']           = $request->input('resolucion_tipo_disposicion');
                    $data1['resolucion_medidas_proteccion']         = $request->input('resolucion_medidas_proteccion');
                    $data1['resolucion_otra_medidas_proteccion']    = strtoupper($util->getNoAcentoNoComilla(trim($request->input('resolucion_otra_medidas_proteccion'))));
                    $data1['resolucion_instituciones_coadyuvantes'] = strtoupper($util->getNoAcentoNoComilla(trim($request->input('resolucion_instituciones_coadyuvantes'))));

                    $data1['fecha_inicio']              = trim($request->input('fecha_inicio'));
                    $data1['fecha_entrega_digital']     = trim($request->input('fecha_entrega_digital'));
                    $data1['informe_seguimiento_fecha'] = trim($request->input('informe_seguimiento_fecha'));
                    $data1['complementario_fecha']      = trim($request->input('complementario_fecha'));

                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                    $col_name = trim($request->input('col_name'));

                    if($opcion == 'n')
                    {
                        if($request->hasFile($file_name))
                        {
                            $archivo = $request->file($file_name);

                            switch($request->input('tipo_file'))
                            {
                                case 1:
                                    $nombre_archivo = uniqid('resolucion_', true) . '.' . $archivo->getClientOriginalExtension();
                                    break;
                                case 2:
                                    $nombre_archivo = uniqid('resolucion_2_', true) . '.' . $archivo->getClientOriginalExtension();
                                    break;
                                case 3:
                                    $nombre_archivo = uniqid('informe_seguimiento_', true) . '.' . $archivo->getClientOriginalExtension();
                                    break;
                                case 4:
                                    $nombre_archivo = uniqid('complementario_', true) . '.' . $archivo->getClientOriginalExtension();
                                    break;
                                default:
                                    # code...
                                    break;
                            }

                            $direccion_archivo = public_path($this->public_dir);

                            $archivo->move($direccion_archivo, $nombre_archivo);

                            $iu                                        = new PvtResolucion;
                            $iu->solicitud_id                          = $data1['solicitud_id'];
                            $iu->resolucion_descripcion                = $data1['resolucion_descripcion'];
                            $iu->resolucion_fecha_emision              = $data1['resolucion_fecha_emision'];
                            $iu->resolucion_tipo_disposicion           = $data1['resolucion_tipo_disposicion'];
                            $iu->resolucion_medidas_proteccion         = $data1['resolucion_medidas_proteccion'];
                            $iu->resolucion_otra_medidas_proteccion    = $data1['resolucion_otra_medidas_proteccion'];
                            $iu->resolucion_instituciones_coadyuvantes = $data1['resolucion_instituciones_coadyuvantes'];

                            $iu->fecha_inicio              = $data1['fecha_inicio'];
                            $iu->fecha_entrega_digital     = $data1['fecha_entrega_digital'];
                            $iu->informe_seguimiento_fecha = $data1['informe_seguimiento_fecha'];
                            $iu->complementario_fecha      = $data1['complementario_fecha'];
                            switch($request->input('tipo_file'))
                            {
                                case 1:
                                    $iu->resolucion_estado_pdf  = 2;
                                    $iu->resolucion_archivo_pdf = $nombre_archivo;
                                    break;
                                case 2:
                                    $iu->resolucion_estado_pdf_2  = 2;
                                    $iu->resolucion_archivo_pdf_2 = $nombre_archivo;
                                    break;
                                case 3:
                                    $iu->informe_seguimiento_estado_pdf  = 2;
                                    $iu->informe_seguimiento_archivo_pdf = $nombre_archivo;
                                    break;
                                case 4:
                                    $iu->complementario_estado_pdf  = 2;
                                    $iu->complementario_archivo_pdf = $nombre_archivo;
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                            $iu->save();

                            $respuesta['respuesta'] .= "El DOCUMENTO se subio con éxito, además, fue agregada nueva fila.";
                            $respuesta['sw']         = 1;

                            $id              = $iu->id;
                            $respuesta['id'] = $id;
                        }
                    }
                    else
                    {
                        $consulta1 = PvtResolucion::where('id', '=', $id)
                            ->select($col_name)
                            ->first();
                        if(count($consulta1) > 0)
                        {
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
                                        $nombre_archivo = uniqid('resolucion_', true) . '.' . $archivo->getClientOriginalExtension();
                                        break;
                                    case 2:
                                        $nombre_archivo = uniqid('resolucion_2_', true) . '.' . $archivo->getClientOriginalExtension();
                                        break;
                                    case 3:
                                        $nombre_archivo = uniqid('informe_seguimiento_', true) . '.' . $archivo->getClientOriginalExtension();
                                        break;
                                    case 4:
                                        $nombre_archivo = uniqid('complementario_', true) . '.' . $archivo->getClientOriginalExtension();
                                        break;
                                    default:
                                        # code...
                                        break;
                                }

                                $direccion_archivo = public_path($this->public_dir);

                                $archivo->move($direccion_archivo, $nombre_archivo);

                                $iu                                        = PvtResolucion::find($id);
                                $iu->solicitud_id                          = $data1['solicitud_id'];
                                $iu->resolucion_descripcion                = $data1['resolucion_descripcion'];
                                $iu->resolucion_fecha_emision              = $data1['resolucion_fecha_emision'];
                                $iu->resolucion_tipo_disposicion           = $data1['resolucion_tipo_disposicion'];
                                $iu->resolucion_medidas_proteccion         = $data1['resolucion_medidas_proteccion'];
                                $iu->resolucion_otra_medidas_proteccion    = $data1['resolucion_otra_medidas_proteccion'];
                                $iu->resolucion_instituciones_coadyuvantes = $data1['resolucion_instituciones_coadyuvantes'];

                                $iu->fecha_inicio              = $data1['fecha_inicio'];
                                $iu->fecha_entrega_digital     = $data1['fecha_entrega_digital'];
                                $iu->informe_seguimiento_fecha = $data1['informe_seguimiento_fecha'];
                                $iu->complementario_fecha      = $data1['complementario_fecha'];

                                switch($request->input('tipo_file'))
                                {
                                    case 1:
                                        $iu->resolucion_estado_pdf  = 2;
                                        $iu->resolucion_archivo_pdf = $nombre_archivo;
                                        break;
                                    case 2:
                                        $iu->resolucion_estado_pdf_2  = 2;
                                        $iu->resolucion_archivo_pdf_2 = $nombre_archivo;
                                        break;
                                    case 3:
                                        $iu->informe_seguimiento_estado_pdf  = 2;
                                        $iu->informe_seguimiento_archivo_pdf = $nombre_archivo;
                                        break;
                                    case 4:
                                        $iu->complementario_estado_pdf  = 2;
                                        $iu->complementario_archivo_pdf = $nombre_archivo;
                                        break;
                                    default:
                                        # code...
                                        break;
                                }

                                $iu->save();

                                $respuesta['respuesta'] .= "El DOCUMENTO se subio con éxito, además, se edito.";
                                $respuesta['sw']         = 1;

                                $respuesta['id'] = $id;
                            }
                        }
                    }

                return json_encode($respuesta);
                break;
            // === ELIMINAR PDF - 3 ===
            case '16':
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
                        'titulo'     => '<div class="text-center"><strong>ELIMINAR PDF</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'error_sw'   => 1
                    );

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if($id != '')
                    {
                        if(!in_array(['codigo' => '1903'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para ELIMINAR PDF.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "No existe la SOLICITUD DE TRABAJO COMPLEMENTARIA.";
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    if($request->has('tipo_del'))
                    {
                        $select = "
                            id,

                            resolucion_estado_pdf,
                            resolucion_archivo_pdf,

                            resolucion_estado_pdf_2,
                            resolucion_archivo_pdf_2,

                            informe_seguimiento_estado_pdf,
                            informe_seguimiento_archivo_pdf,

                            complementario_estado_pdf,
                            complementario_archivo_pdf
                        ";

                        $consulta1 = PvtResolucion::where('id', '=', $id)
                            ->select(DB::raw($select))
                            ->first();

                        $del_sw   = FALSE;
                        $del_file = '';

                        switch($request->input('tipo_del'))
                        {
                            case '1':
                                if($consulta1['resolucion_archivo_pdf'] != '')
                                {
                                    $del_sw   = TRUE;
                                    $del_file = $consulta1['resolucion_archivo_pdf'];

                                    $iu                         = PvtResolucion::find($id);
                                    $iu->resolucion_estado_pdf  = 1;
                                    $iu->resolucion_archivo_pdf = NULL;
                                    $iu->save();
                                }
                                break;
                            case '2':
                                if($consulta1['resolucion_archivo_pdf_2'] != '')
                                {
                                    $del_sw   = TRUE;
                                    $del_file = $consulta1['resolucion_archivo_pdf_2'];

                                    $iu                           = PvtResolucion::find($id);
                                    $iu->resolucion_estado_pdf_2  = 1;
                                    $iu->resolucion_archivo_pdf_2 = NULL;
                                    $iu->save();
                                }
                                break;
                            case '3':
                                if($consulta1['informe_seguimiento_archivo_pdf'] != '')
                                {
                                    $del_sw   = TRUE;
                                    $del_file = $consulta1['informe_seguimiento_archivo_pdf'];

                                    $iu                                  = PvtResolucion::find($id);
                                    $iu->informe_seguimiento_estado_pdf  = 1;
                                    $iu->informe_seguimiento_archivo_pdf = NULL;
                                    $iu->save();
                                }
                                break;
                            case '4':
                                if($consulta1['complementario_archivo_pdf'] != '')
                                {
                                    $del_sw   = TRUE;
                                    $del_file = $consulta1['complementario_archivo_pdf'];

                                    $iu                         = PvtResolucion::find($id);
                                    $iu->complementario_estado_pdf  = 1;
                                    $iu->complementario_archivo_pdf = NULL;
                                    $iu->save();
                                }
                                break;
                            default:
                                # code...
                                break;
                        }

                        if($del_sw)
                        {
                            if(file_exists(public_path($this->public_dir) . '/' . $del_file))
                            {
                                unlink(public_path($this->public_dir) . '/' . $del_file);
                            }

                            $respuesta['respuesta'] .= "Se ELIMINO con éxito.";
                            $respuesta['sw']        = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El PDF no existe.";
                        }
                    }
                return json_encode($respuesta);
                break;

            // === DELITO - INSERT UPDATE ===
            case '21':
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
                        'titulo'     => '<div class="text-center"><strong>DELITO</strong></div>',
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
                            'solicitud_id' => 'required',
                            'delito_id'    => 'required'
                        ],
                        [
                            'solicitud_id.required' => 'MEDIDAS DE PROTECCION debe de existir.',
                            'delito_id.required'    => 'El campo DELITO es obligatorio.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['solicitud_id'] = trim($request->input('solicitud_id'));
                    $data1['delito_id']    = trim($request->input('delito_id'));
                    $data1['tentativa']    = trim($request->input('tentativa'));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $consulta1 = PvtSolicitudDelito::where('solicitud_id', '=', $data1['solicitud_id'])->where('delito_id', '=', $data1['delito_id'])->where('estado', '=', 1)->count();
                        if($consulta1 < 1)
                        {
                            $iu               = new PvtSolicitudDelito;
                            $iu->estado       = 1;
                            $iu->solicitud_id = $data1['solicitud_id'];
                            $iu->delito_id    = $data1['delito_id'];
                            if($data1['tentativa'] != "")
                            {
                                $iu->tentativa    = $data1['tentativa'];
                            }
                            $iu->save();

                            $respuesta['respuesta'] .= "El DELITO fue registrada con éxito.";
                            $respuesta['sw']         = 1;

                            $tabla1 = "pvt_solicitudes_delitos";
                            $tabla2 = "pvt_delitos";

                            $select = "
                                $tabla1.id,

                                $tabla1.solicitud_id,
                                $tabla1.delito_id,

                                $tabla1.estado,
                                $tabla1.tentativa,

                                a2.nombre
                            ";

                            $where_concatenar = "$tabla1.solicitud_id=" . $data1['solicitud_id'] . " AND $tabla1.estado=1";

                            $consulta2 = PvtSolicitudDelito::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.delito_id")
                                ->whereRaw($where_concatenar)
                                ->select(DB::raw($select))
                                ->get()
                                ->toArray();

                            $delitos = "";
                            foreach($consulta2 as $row2)
                            {
                                if($delitos == "")
                                {
                                    $delitos .= $row2["nombre"];
                                }
                                else
                                {
                                    $delitos .= "||" . $row2["nombre"];
                                }
                            }

                            $iu          = PvtSolicitud::find($data1['solicitud_id']);
                            $iu->delitos = $delitos;
                            $iu->save();
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El DELITO ya fue registrada.";
                        }
                    }
                    else
                    {
                        $iu               = PvtSolicitudDelito::find($id);
                        $iu->solicitud_id = $data1['solicitud_id'];
                        $iu->delito_id    = $data1['delito_id'];
                        $iu->tentativa    = $data1['tentativa'];
                        $iu->save();

                        $respuesta['respuesta'] .= "El DELITO se edito con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['iu']         = 2;
                    }
                return json_encode($respuesta);
                break;
            // === DELITO - DELETE ===
            case '211':
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
                        'titulo'     => '<div class="text-center"><strong>ELIMINAR DELITO</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );

                    $opcion = 'n';

                // === PERMISOS ===
                    $id = trim($request->input('solicitud_delito_id'));
                    if($id != '')
                    {
                        $opcion = 'e';
                        if(!in_array(['codigo' => '1903'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para ELIMINAR.";
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

                // === REGISTRAR MODIFICAR VALORES ===
                    $de = PvtSolicitudDelito::find($id);
                    $de->delete();

                    $respuesta['respuesta'] .= "El DELITO fue eliminado con éxito.";
                    $respuesta['sw']         = 1;


                    $tabla1 = "pvt_solicitudes_delitos";
                    $tabla2 = "pvt_delitos";

                    $select = "
                        $tabla1.id,

                        $tabla1.solicitud_id,
                        $tabla1.delito_id,

                        $tabla1.estado,
                        $tabla1.tentativa,

                        a2.nombre
                    ";

                    $where_concatenar = "$tabla1.solicitud_id=" . trim($request->input('solicitud_id')) . " AND $tabla1.estado=1";

                    $consulta2 = PvtSolicitudDelito::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.delito_id")
                        ->whereRaw($where_concatenar)
                        ->select(DB::raw($select))
                        ->get()
                        ->toArray();

                    $delitos = "";
                    if(count($consulta2) > 0)
                    {
                        foreach($consulta2 as $row2)
                        {
                            if($delitos == "")
                            {
                                $delitos .= $row2["nombre"];
                            }
                            else
                            {
                                $delitos .= "||" . $row2["nombre"];
                            }
                        }
                    }

                    $iu          = PvtSolicitud::find(trim($request->input('solicitud_id')));
                    $iu->delitos = $delitos;
                    $iu->save();

                return json_encode($respuesta);
                break;
            // === RECALIFICACION DEL DELITO - INSERT UPDATE ===
            case '22':
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
                        'titulo'     => '<div class="text-center"><strong>RECALIFICACION DEL DELITO</strong></div>',
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
                            'solicitud_id' => 'required',
                            'delito_id'    => 'required'
                        ],
                        [
                            'solicitud_id.required' => 'MEDIDAS DE PROTECCION debe de existir.',
                            'delito_id.required'    => 'El campo DELITO es obligatorio.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['solicitud_id'] = trim($request->input('solicitud_id'));
                    $data1['delito_id']    = trim($request->input('delito_id'));
                    $data1['tentativa']    = trim($request->input('tentativa'));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $consulta1 = PvtSolicitudDelito::where('solicitud_id', '=', $data1['solicitud_id'])->where('delito_id', '=', $data1['delito_id'])->where('estado', '=', 2)->count();
                        if($consulta1 < 1)
                        {
                            $iu               = new PvtSolicitudDelito;
                            $iu->estado       = 2;
                            $iu->solicitud_id = $data1['solicitud_id'];
                            $iu->delito_id    = $data1['delito_id'];
                            if($data1['tentativa'] != "")
                            {
                                $iu->tentativa    = $data1['tentativa'];
                            }
                            $iu->save();

                            $respuesta['respuesta'] .= "RECALIFICACION DEL DELITO fue registrada con éxito.";
                            $respuesta['sw']         = 1;

                            $tabla1 = "pvt_solicitudes_delitos";
                            $tabla2 = "pvt_delitos";

                            $select = "
                                $tabla1.id,

                                $tabla1.solicitud_id,
                                $tabla1.delito_id,

                                $tabla1.estado,
                                $tabla1.tentativa,

                                a2.nombre
                            ";

                            $where_concatenar = "$tabla1.solicitud_id=" . $data1['solicitud_id'] . " AND $tabla1.estado=2";

                            $consulta2 = PvtSolicitudDelito::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.delito_id")
                                ->whereRaw($where_concatenar)
                                ->select(DB::raw($select))
                                ->get()
                                ->toArray();

                            $delitos = "";
                            foreach($consulta2 as $row2)
                            {
                                if($delitos == "")
                                {
                                    $delitos .= $row2["nombre"];
                                }
                                else
                                {
                                    $delitos .= "||" . $row2["nombre"];
                                }
                            }

                            $iu                         = PvtSolicitud::find($data1['solicitud_id']);
                            $iu->recalificacion_delitos = $delitos;
                            $iu->save();
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "RECALIFICACION DEL DELITO ya fue registrada.";
                        }
                    }
                    else
                    {
                        $iu               = PvtSolicitudDelito::find($id);
                        $iu->solicitud_id = $data1['solicitud_id'];
                        $iu->delito_id    = $data1['delito_id'];
                        $iu->tentativa    = $data1['tentativa'];
                        $iu->save();

                        $respuesta['respuesta'] .= "El DELITO se edito con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['iu']         = 2;
                    }
                return json_encode($respuesta);
                break;
            // === RECALIFICACION DEL DELITO - DELETE ===
            case '221':
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
                        'titulo'     => '<div class="text-center"><strong>ELIMINAR RECALIFICACION DEL DELITO</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );

                    $opcion = 'n';

                // === PERMISOS ===
                    $id = trim($request->input('solicitud_delito_id'));
                    if($id != '')
                    {
                        $opcion = 'e';
                        if(!in_array(['codigo' => '1903'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para ELIMINAR.";
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

                // === REGISTRAR MODIFICAR VALORES ===
                    $de = PvtSolicitudDelito::find($id);
                    $de->delete();

                    $respuesta['respuesta'] .= "RECALIFICACION DEL DELITO fue eliminado con éxito.";
                    $respuesta['sw']         = 1;


                    $tabla1 = "pvt_solicitudes_delitos";
                    $tabla2 = "pvt_delitos";

                    $select = "
                        $tabla1.id,

                        $tabla1.solicitud_id,
                        $tabla1.delito_id,

                        $tabla1.estado,
                        $tabla1.tentativa,

                        a2.nombre
                    ";

                    $where_concatenar = "$tabla1.solicitud_id=" . trim($request->input('solicitud_id')) . " AND $tabla1.estado=2";

                    $consulta2 = PvtSolicitudDelito::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.delito_id")
                        ->whereRaw($where_concatenar)
                        ->select(DB::raw($select))
                        ->get()
                        ->toArray();

                    $delitos = "";
                    if(count($consulta2) > 0)
                    {
                        foreach($consulta2 as $row2)
                        {
                            if($delitos == "")
                            {
                                $delitos .= $row2["nombre"];
                            }
                            else
                            {
                                $delitos .= "||" . $row2["nombre"];
                            }
                        }
                    }

                    $iu                         = PvtSolicitud::find(trim($request->input('solicitud_id')));
                    $iu->recalificacion_delitos = $delitos;
                    $iu->save();

                return json_encode($respuesta);
                break;
            // === SOLICITUD TRABAJO COMPLEMENTARIO - INSERT UPDATE ===
            case '23':
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
                        'titulo'     => '<div class="text-center"><strong>SOLICITUD TRABAJO COMPLEMENTARIO</strong></div>',
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
                            'solicitud_id'                      => 'required',
                            'complementario_dirigido_a'         => 'required',
                            'complementario_trabajo_solicitado' => 'required'
                        ],
                        [
                            'solicitud_id.required'                      => 'MEDIDAS DE PROTECCION debe de existir.',
                            'complementario_dirigido_a.required'         => 'El campo DIRIGIDO A es obligatorio.',
                            'complementario_trabajo_solicitado.required' => 'El campo TRABAJO SOLICITADO es obligatorio.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['solicitud_id']                      = trim($request->input('solicitud_id'));
                    $data1['complementario_dirigido_a']         = strtoupper($util->getNoAcentoNoComilla(trim($request->input('complementario_dirigido_a'))));
                    $data1['complementario_trabajo_solicitado'] = strtoupper($util->getNoAcentoNoComilla(trim($request->input('complementario_trabajo_solicitado'))));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $iu                                    = new PvtSolicitudComplementaria;
                        $iu->solicitud_id                      = $data1['solicitud_id'];
                        $iu->complementario_dirigido_a         = $data1['complementario_dirigido_a'];
                        $iu->complementario_trabajo_solicitado = $data1['complementario_trabajo_solicitado'];
                        $iu->save();

                        $respuesta['respuesta'] .= "La SOLICITUD TRABAJO COMPLEMENTARIO fue registrada con éxito.";
                        $respuesta['sw']         = 1;

                        $id              = $iu->id;
                        $respuesta['id'] = $id;
                    }
                    else
                    {
                        $iu                                    = PvtSolicitudComplementaria::find($id);
                        $iu->solicitud_id                      = $data1['solicitud_id'];
                        $iu->complementario_dirigido_a         = $data1['complementario_dirigido_a'];
                        $iu->complementario_trabajo_solicitado = $data1['complementario_trabajo_solicitado'];
                        $iu->save();

                        $respuesta['respuesta'] .= "La SOLICITUD TRABAJO COMPLEMENTARIO se edito con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['iu']         = 2;

                        $respuesta['id'] = $id;
                    }

                return json_encode($respuesta);
                break;
            // === SOLICITUD TRABAJO COMPLEMENTARIO - DELETE ===
            case '231':
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
                        'titulo'     => '<div class="text-center"><strong>ELIMINAR SOLICITUD TRABAJO COMPLEMENTARIO</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );

                    $opcion = 'n';

                // === PERMISOS ===
                    $id = trim($request->input('solicitud_complementaria_id'));
                    if($id != '')
                    {
                        $opcion = 'e';
                        if(!in_array(['codigo' => '1903'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para ELIMINAR.";
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

                // === REGISTRAR MODIFICAR ELIMINAR VALORES ===
                    $complementario_archivo_pdf = trim($request->input('complementario_archivo_pdf'));

                    if(file_exists(public_path($this->public_dir) . '/' . $complementario_archivo_pdf))
                    {
                        unlink(public_path($this->public_dir) . '/' . $complementario_archivo_pdf);
                    }

                    $de = PvtSolicitudComplementaria::find($id);
                    $de->delete();

                    $respuesta['respuesta'] .= "La SOLICITUD TRABAJO COMPLEMENTARIO fue eliminado con éxito.";
                    $respuesta['sw']         = 1;

                return json_encode($respuesta);
                break;
            // === RESOLUCIONES DEL MP Y SEGUIMIENTO - INSERT UPDATE ===
            case '24':
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
                        'titulo'     => '<div class="text-center"><strong>RESOLUCIONES DEL MP Y SEGUIMIENTO</strong></div>',
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
                            'solicitud_id'                          => 'required',
                            'resolucion_descripcion'                => 'required|max:500',
                            'resolucion_tipo_disposicion'           => 'max:50',
                            'resolucion_medidas_proteccion'         => 'max:50',
                            'resolucion_otra_medidas_proteccion'    => 'max:1000',
                            'resolucion_instituciones_coadyuvantes' => 'max:1000'
                        ],
                        [
                            'solicitud_id.required' => 'MEDIDAS DE PROTECCION debe de existir.',

                            'resolucion_descripcion.required' => 'El campo DESCRIPCION DE LA RESOLUCION es obligatorio.',
                            'resolucion_descripcion.max'      => 'El campo DESCRIPCION DE LA RESOLUCION debe contener :max caracteres como máximo.',

                            'resolucion_tipo_disposicion.max' => 'El campo TIPO DE DISPOSICION debe contener :max caracteres como máximo.',

                            'resolucion_medidas_proteccion.max' => 'El campo MEDIDA DE PROTECCION DISPUESTA debe contener :max caracteres como máximo.',

                            'resolucion_otra_medidas_proteccion.max' => 'El campo OTRA MEDIDA DE PROTECCION DISPUESTA debe contener :max caracteres como máximo.',

                            'resolucion_instituciones_coadyuvantes.max' => 'El campo INSTITUCION COADYUVANTE debe contener :max caracteres como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['solicitud_id']                          = trim($request->input('solicitud_id'));
                    $data1['resolucion_descripcion']                = strtoupper($util->getNoAcentoNoComilla(trim($request->input('resolucion_descripcion'))));
                    $data1['resolucion_fecha_emision']              = trim($request->input('resolucion_fecha_emision'));
                    $data1['resolucion_tipo_disposicion']           = $request->input('resolucion_tipo_disposicion');
                    $data1['resolucion_medidas_proteccion']         = $request->input('resolucion_medidas_proteccion');
                    $data1['resolucion_otra_medidas_proteccion']    = strtoupper($util->getNoAcentoNoComilla(trim($request->input('resolucion_otra_medidas_proteccion'))));
                    $data1['resolucion_instituciones_coadyuvantes'] = strtoupper($util->getNoAcentoNoComilla(trim($request->input('resolucion_instituciones_coadyuvantes'))));

                    $data1['fecha_inicio']              = trim($request->input('fecha_inicio'));
                    $data1['fecha_entrega_digital']     = trim($request->input('fecha_entrega_digital'));
                    $data1['informe_seguimiento_fecha'] = trim($request->input('informe_seguimiento_fecha'));
                    $data1['complementario_fecha']      = trim($request->input('complementario_fecha'));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $iu                                        = new PvtResolucion;
                        $iu->solicitud_id                          = $data1['solicitud_id'];
                        $iu->resolucion_descripcion                = $data1['resolucion_descripcion'];
                        $iu->resolucion_fecha_emision              = $data1['resolucion_fecha_emision'];
                        $iu->resolucion_tipo_disposicion           = $data1['resolucion_tipo_disposicion'];
                        $iu->resolucion_medidas_proteccion         = $data1['resolucion_medidas_proteccion'];
                        $iu->resolucion_otra_medidas_proteccion    = $data1['resolucion_otra_medidas_proteccion'];
                        $iu->resolucion_instituciones_coadyuvantes = $data1['resolucion_instituciones_coadyuvantes'];

                        $iu->fecha_inicio              = $data1['fecha_inicio'];
                        $iu->fecha_entrega_digital     = $data1['fecha_entrega_digital'];
                        $iu->informe_seguimiento_fecha = $data1['informe_seguimiento_fecha'];
                        $iu->complementario_fecha      = $data1['complementario_fecha'];

                        $iu->save();

                        $respuesta['respuesta'] .= "La RESOLUCIONES DEL MP Y SEGUIMIENTO fue registrada con éxito.";
                        $respuesta['sw']         = 1;

                        $id              = $iu->id;
                        $respuesta['id'] = $id;
                    }
                    else
                    {
                        $iu                                        = PvtResolucion::find($id);
                        $iu->solicitud_id                          = $data1['solicitud_id'];
                        $iu->resolucion_descripcion                = $data1['resolucion_descripcion'];
                        $iu->resolucion_fecha_emision              = $data1['resolucion_fecha_emision'];
                        $iu->resolucion_tipo_disposicion           = $data1['resolucion_tipo_disposicion'];
                        $iu->resolucion_medidas_proteccion         = $data1['resolucion_medidas_proteccion'];
                        $iu->resolucion_otra_medidas_proteccion    = $data1['resolucion_otra_medidas_proteccion'];
                        $iu->resolucion_instituciones_coadyuvantes = $data1['resolucion_instituciones_coadyuvantes'];

                        $iu->fecha_inicio              = $data1['fecha_inicio'];
                        $iu->fecha_entrega_digital     = $data1['fecha_entrega_digital'];
                        $iu->informe_seguimiento_fecha = $data1['informe_seguimiento_fecha'];
                        $iu->complementario_fecha      = $data1['complementario_fecha'];

                        $iu->save();

                        $respuesta['respuesta'] .= "La RESOLUCIONES DEL MP Y SEGUIMIENTO se edito con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['iu']         = 2;

                        $respuesta['id'] = $id;
                    }

                return json_encode($respuesta);
                break;
            // === RESOLUCIONES DEL MP Y SEGUIMIENTO - DELETE ===
            case '241':
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
                        'titulo'     => '<div class="text-center"><strong>ELIMINAR RESOLUCIONES DEL MP Y SEGUIMIENTO</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );

                    $opcion = 'n';

                // === PERMISOS ===
                    $id = trim($request->input('resolucion_id'));
                    if($id != '')
                    {
                        $opcion = 'e';
                        if(!in_array(['codigo' => '1903'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para ELIMINAR.";
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

                // === REGISTRAR MODIFICAR ELIMINAR VALORES ===
                    $resolucion_archivo_pdf          = trim($request->input('resolucion_archivo_pdf'));
                    $resolucion_archivo_pdf_2        = trim($request->input('resolucion_archivo_pdf_2'));
                    $informe_seguimiento_archivo_pdf = trim($request->input('informe_seguimiento_archivo_pdf'));
                    $complementario_archivo_pdf      = trim($request->input('complementario_archivo_pdf'));

                    if(file_exists(public_path($this->public_dir) . '/' . $resolucion_archivo_pdf))
                    {
                        unlink(public_path($this->public_dir) . '/' . $resolucion_archivo_pdf);
                    }

                    if(file_exists(public_path($this->public_dir) . '/' . $resolucion_archivo_pdf_2))
                    {
                        unlink(public_path($this->public_dir) . '/' . $resolucion_archivo_pdf_2);
                    }

                    if(file_exists(public_path($this->public_dir) . '/' . $informe_seguimiento_archivo_pdf))
                    {
                        unlink(public_path($this->public_dir) . '/' . $informe_seguimiento_archivo_pdf);
                    }

                    if(file_exists(public_path($this->public_dir) . '/' . $complementario_archivo_pdf))
                    {
                        unlink(public_path($this->public_dir) . '/' . $complementario_archivo_pdf);
                    }

                    $de = PvtResolucion::find($id);
                    $de->delete();

                    $respuesta['respuesta'] .= "La RESOLUCION DEL MP Y SEGUIMIENTO fue eliminado con éxito.";
                    $respuesta['sw']         = 1;

                return json_encode($respuesta);
                break;

            // === CERRAR MEDIDA DE PROTECCION ===
            case '30':
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
                        'titulo'     => '<div class="text-center"><strong>CERRAR MEDIDA DE PROTECCION</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'error_sw'   => 1
                    );

                // === PERMISOS ===
                    if(!in_array(['codigo' => '1905'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para CERRAR MEDIDA DE PROTECCION.";
                        return json_encode($respuesta);
                    }

                // === VALIDATE ===
                    $id = trim($request->input('id'));
                    if($id == '')
                    {
                        $respuesta['respuesta'] .= "Seleccione una MEDIDA DE PROTECCION.";
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $iu                  = PvtSolicitud::find($id);
                    $iu->cerrado_abierto = 2;

                    $iu->save();

                    $respuesta['respuesta'] .= "La MEDIDA DE PROTECCION se cerro.";
                    $respuesta['sw']         = 1;

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
            // === SELECT2 DELITOS ===
            case '102':
                if($request->has('q'))
                {
                    $nombre     = $request->input('q');
                    $estado     = trim($request->input('estado'));
                    $page_limit = trim($request->input('page_limit'));

                    $query = PvtDelito::whereRaw("nombre ilike '%$nombre%'")
                        ->where("estado", "=", $estado)
                        ->select(DB::raw("id, nombre AS text"))
                        ->orderByRaw("nombre ASC")
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
                switch($valor['estado_pdf'])
                {
                    case '1':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->estado_pdf[$valor['estado_pdf']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<button type="button" class="btn btn-xs btn-primary" onclick="utilitarios([80, ' . $valor['id'] . ', ' . $valor['tipo_pdf'] . ']);" title="Clic para ver documento">
                            <strong>' . $this->estado_pdf[$valor['estado_pdf']] . '</strong>
                        </button>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '';
                        return($respuesta);
                        break;
                }
                break;
            case '2':
                switch($valor['cerrado_abierto'])
                {
                    case '1':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->cerrado_abierto[$valor['cerrado_abierto']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-primary font-sm">' . $this->cerrado_abierto[$valor['cerrado_abierto']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
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
            case '6':
                switch($valor['estado_pdf'])
                {
                    case '1':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->estado_pdf[$valor['estado_pdf']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<button type="button" class="btn btn-xs btn-primary" onclick="utilitarios([62, ' . $valor['tipo_pdf'] . ', ' . $valor['id'] . ']);" title="Clic para ver documento">
                            <strong>' . $this->estado_pdf[$valor['estado_pdf']] . '</strong>
                        </button>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '';
                        return($respuesta);
                        break;
                }
                break;
            case '7':
                switch($valor['estado_pdf'])
                {
                    case '1':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->estado_pdf[$valor['estado_pdf']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<button type="button" class="btn btn-xs btn-primary" onclick="utilitarios([64, ' . $valor['tipo_pdf'] . ', ' . $valor['id'] . ']);" title="Clic para ver documento">
                            <strong>' . $this->estado_pdf[$valor['estado_pdf']] . '</strong>
                        </button>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '';
                        return($respuesta);
                        break;
                }
                break;
            case '8':
                $respuesta = '';
                if($valor['valor'] != '')
                {
                    $valor_array = explode(",", $valor['valor']);

                    $valor1_sw = TRUE;
                    foreach($valor_array as $valor1)
                    {
                        switch($valor['tipo1'])
                        {
                            case '1':
                                if($valor1_sw)
                                {
                                    $respuesta .= $this->resolucion_tipo_disposicion[$valor1];
                                    $valor1_sw = FALSE;
                                }
                                else
                                {
                                    $respuesta .= "<br>" . $this->resolucion_tipo_disposicion[$valor1];
                                }
                                break;
                            case '2':
                                if($valor1_sw)
                                {
                                    $respuesta .= $this->resolucion_mpd[$valor1];
                                    $valor1_sw = FALSE;
                                }
                                else
                                {
                                    $respuesta .= "<br>" . $this->resolucion_mpd[$valor1];
                                }
                                break;
                            default:
                                break;
                        }
                    }
                }
                return($respuesta);
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
}