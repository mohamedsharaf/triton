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

        $this->public_dir = 'storage/dpvt/solicitud/pdf/';
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

    public function send_ajax(Request $request)
    {
        if( ! $request->ajax())
        {
            $respuesta = [
                'sw'        => 0,
                'titulo'    => 'GESTOR DE PERSONAS',
                'respuesta' => 'No es solicitud AJAX.'
            ];
            return json_encode($respuesta);
        }

        $tipo = $request->input('tipo');

        switch($tipo)
        {
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