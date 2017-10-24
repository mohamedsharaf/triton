<?php

namespace App\Http\Controllers\Seguridad;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;

use App\Models\Seguridad\SegModulo;
use App\Models\Seguridad\SegPermiso;
use App\Models\Seguridad\SegRol;
use App\Models\Seguridad\SegPermisoRol;

class PermisoRolController extends Controller
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
        if($this->rol_id == 1)
        {
            $data = [
                'rol_id'       => $this->rol_id,
                'permisos'     => $this->permisos,
                'title'       => 'Asignación de permisos',
                'home'        => 'Inicio',
                'sistema'     => 'Seguridad',
                'modulo'      => 'Asignación de permisos',
                'title_table' => 'Roles',
                'mp_array'    => SegPermiso::leftJoin("seg_modulos", "seg_modulos.id", "=", "seg_permisos.modulo_id")
                                    ->where("seg_modulos.estado", "=", 1)
                                    ->where("seg_permisos.estado", "=", 1)
                                    ->select(
                                        "seg_modulos.codigo AS modulo_codigo",
                                        "seg_modulos.nombre AS mudulo_nombre",
                                        "seg_permisos.id",
                                        "seg_permisos.codigo",
                                        "seg_permisos.nombre"
                                    )
                                    ->orderBy("seg_permisos.codigo", "ASC")
                                    ->get()
                                    ->toArray()
            ];
            return view('seguridad.permiso_rol.permiso_rol')->with($data);
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

                $select = "
                    seg_roles.id,
                    seg_roles.nombre
                ";

                $array_where = [
                    ["seg_roles.estado", "=", 1]
                ];

                $array_where = array_merge($array_where, $jqgrid->getWhere());

                $count = SegRol::where($array_where)->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = SegRol::where($array_where)
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
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',
                        $row["nombre"],
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
                'titulo'    => 'ASIGNACION DE PERMISOS',
                'respuesta' => 'No es solicitud AJAX.'
            ];
            return json_encode($respuesta);
        }

        $tipo = $request->input('tipo');

        switch($tipo)
        {
            // === INSERT UPDATE GESTOR DE MODULOS ===
            case '1':
                // === LIBRERIAS ===

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>ASIGNACION DE PERMISOS</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1
                    );

                // === PERMISOS ROL ===
                    $rol_id = trim($request->input('rol_id'));

                    $del_pr = SegPermisoRol::where('rol_id', '=', $rol_id);
                    $del_pr->delete();

                //=== OPERACION ===
                    if($request->has('permiso_id'))
                    {
                        $permiso_id = $request->input('permiso_id');
                        foreach($permiso_id as $value)
                        {
                            $iu             = new SegPermisoRol;
                            $iu->rol_id     = $rol_id;
                            $iu->permiso_id = $value;
                            $iu->save();
                        }
                    }

                    $respuesta['respuesta'] .= "Se asignaron permisos con éxito.";
                    $respuesta['sw']         = 1;
                    $respuesta['iu']         = 2;

                //=== respuesta ===
                    return json_encode($respuesta);
                break;
            // === ROL PERMISOS ===
            case '100':
                // === LIBRERIAS ===

                // === INICIALIZACION DE VARIABLES ===
                    $respuesta = array(
                        'sw'       => 0,
                        'tipo'     => $tipo,
                        'consulta' => []
                    );

                // === PERMISOS ROL ===
                    if($request->has('rol_id'))
                    {
                        $rol_id = trim($request->input('rol_id'));

                        $respuesta['consulta'] = SegPermisoRol::where('rol_id', '=', $rol_id)->select("permiso_id")->get()->toArray();

                        $respuesta['sw'] = 1;
                    }
                //=== respuesta ===
                    return json_encode($respuesta);
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
        switch($valor['estado'])
        {
          case '1':
            $respuesta = '<span class="label label-primary font-sm">' . $this->estado[$valor['estado']] . '</span>';
            return($respuesta);
            break;
          case '2':
            $respuesta = '<span class="label label-danger font-sm">' . $this->estado[$valor['estado']] . '</span>';
            return($respuesta);
            break;
          default:
            $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
            return($respuesta);
            break;
        }
        break;
      default:
        break;
    }
  }
}
