<?php

namespace App\Http\Controllers\Rrhh;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;

use App\Models\Seguridad\SegPermisoRol;

use App\Models\Rrhh\RrhhLogMarcacion;
use App\Models\Rrhh\RrhhLogMarcacionBackup;
use App\Models\Rrhh\RrhhBiometrico;
use App\Models\Rrhh\RrhhPersonaBiometrico;
use App\User;

class MarcacionBiometricoController extends Controller
{
    private $rol_id;
    private $permisos;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->rol_id   = Auth::user()->rol_id;
        $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                            ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                            ->select("seg_permisos.codigo")
                            ->get()
                            ->toArray();
    }
}