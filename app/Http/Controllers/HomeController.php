<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Seguridad\SegPermisoRol;

class HomeController extends Controller
{
    private $rol_id;
    private $permisos;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->rol_id   = Auth::user()->rol_id;
        $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                            ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                            ->select("seg_permisos.codigo")
                            ->get()
                            ->toArray();
        $data = array(
            'rol_id'   => $this->rol_id,
            'permisos' => $this->permisos,
            'title'    => 'Inicio',
            'home'     => 'Inicio',
            'sistema'  => 'Recursos Humanos',
            'modulo'   => 'Mi perfil'
        );
        return view('home')->with($data);
    }
}
