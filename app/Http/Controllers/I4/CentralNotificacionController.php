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

class CentralNotificacionController extends Controller
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
                'notificacion_estado_array' => $this->notificacion_estado
            ];
            return view('i4.central_notificacion.central_notificacion')->with($data);
        }
        else
        {
            return back()->withInput();
        }
    }

}