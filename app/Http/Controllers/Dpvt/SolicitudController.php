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
            '2' => 'PENDIENTE DE INFORME DE LA UPVT U OTRA INSTANCIA',
            '3' => 'PENDIENTE RESOLUCION',
            '4' => 'ARCHIVO DE OBRADOS',
            '5' => 'PENDIENTE DE INFORME DE SEGUIMIENTO'
        ];

        $this->cerrado_abierto = [
            '1' => 'ABIERTA',
            '2' => 'CERRADA'
        ];

        $this->solicitante = [
            '1' => 'MINISTERIO DE TRABAJO EMPLEO Y PREVISION SOCIAL',
            '2' => 'MINISTERIO DE JUSTICIA',
            '3' => 'MINSITERIO DE GOBIERNO',
            '4' => 'FISCAL DEPARTAMENTAL',
            '5' => 'FISCAL DE MATERIA',
            '6' => 'CUALQUIER PERSONA'
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
            '1' => 'VARON',
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

        $this->resolucion_tipo_disposicion = [
            '1' => 'OTORGA',
            '2' => 'NIEGA',
            '3' => 'AMPLIA',
            '4' => 'RETIRA/SUSPENDE',
            '5' => 'PRORROGA',
            '6' => 'ELIMINA'
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
                'title'                             => 'Solicitud a la DPVTMMP',
                'home'                              => 'Inicio',
                'sistema'                           => 'DPVTMMP',
                'modulo'                            => 'Solicitud a la DPVTMMP',
                'title_table'                       => 'Solicitudes a la DPVTMMP',
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
                'resolucion_tipo_disposicion_array' => $this->resolucion_tipo_disposicion
            ];
            return view('dpvt.solicitud.solicitud')->with($data);
        }
        else
        {
            return back()->withInput();
        }
    }
}