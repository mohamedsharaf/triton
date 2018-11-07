<?php

namespace App\Http\Controllers\Seguridad;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;
use App\Libraries\UtilClass;

use App\Models\Seguridad\SegRol;
use App\Models\Seguridad\SegPermisoRol;
use App\Models\Seguridad\SegLdRol;
use App\Models\Institucion\InstLugarDependencia;

class RolController extends Controller
{
  private $estado;

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

    $this->estado = [
      '1' => 'HABILITADO',
      '2' => 'INHABILITADO'
    ];
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
                'rol_id'                  => $this->rol_id,
                'permisos'                => $this->permisos,
                'title'                   => 'Gestor de roles',
                'home'                    => 'Inicio',
                'sistema'                 => 'Seguridad',
                'modulo'                  => 'Gestor de roles',
                'title_table'             => 'Roles',
                'estado_array'            => $this->estado,
                'lugar_dependencia_array' => InstLugarDependencia::select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray()
            ];
            return view('seguridad.rol.rol')->with($data);
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
          seg_roles.estado,
          seg_roles.nombre,
          seg_roles.lugar_dependencia
        ";

        $array_where = "TRUE";
        $array_where .= $jqgrid->getWhere();

        $count = SegRol::whereRaw($array_where)->count();

        $limit_offset = $jqgrid->getLimitOffset($count);

        $query = SegRol::whereRaw($array_where)
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
                'estado'=> $row["estado"]
            );

            $respuesta['rows'][$i]['id'] = $row["id"];
            $respuesta['rows'][$i]['cell'] = array(
                '',
                $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                $row["nombre"],
                $this->utilitarios(array('tipo' => '3', 'd_json' => $row["lugar_dependencia"])),
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
        'titulo'    => 'GESTOR DE ROLES',
        'respuesta' => 'No es solicitud AJAX.'
      ];
      return json_encode($respuesta);
    }

    $tipo = $request->input('tipo');

    switch($tipo)
    {
      // === INSERT UPDATE GESTOR DE MODULOS ===
      case '1':
        // dd($request->all);
        // === LIBRERIAS ===
          $util = new UtilClass();
          // return strtoupper($util->getNoAcentoNoComilla(trim('"   sérÁñ    "')));

        // === INICIALIZACION DE VARIABLES ===
          $data1     = array();
          $respuesta = array(
            'sw'         => 0,
            'titulo'     => '<div class="text-center"><strong>GESTOR DE ROLES</strong></div>',
            'respuesta'  => '',
            'tipo'       => $tipo,
            'iu'         => 1
          );
          $opcion = 'n';
          $error  = FALSE;

          // $f_actual       = date("Y-m-d");
          // $f_modificacion = date("Y-m-d H:i:s");

        // === PERMISOS ===
            $id = trim($request->input('id'));
            if($id != '')
            {
              $opcion              = 'e';
              // $data1['updated_at'] = $f_modificacion;
            }
            else
            {
              // $data1['created_at'] = $f_modificacion;
            }
          //=== OPERACION ===
            $estado = trim($request->input('estado'));
            $nombre = strtoupper($util->getNoAcentoNoComilla(trim($request->input('nombre'))));

            if($request->has('lugar_dependencia'))
            {
                $lugar_dependencia = $request->input('lugar_dependencia');

                $i = 0;
                foreach($lugar_dependencia as $lugar_dependencia_id)
                {
                    $ld_query = InstLugarDependencia::where('id', '=', $lugar_dependencia_id)
                        ->select("id", "nombre")
                        ->first()
                        ->toArray();

                    $ld_nombre_array[$i] = $ld_query['nombre'];
                    $i++;
                }
                $ld_json = json_encode($ld_nombre_array);
            }
            else
            {
                $lugar_dependencia = NULL;
                $ld_json           = NULL;
            }

            if($opcion == 'n')
            {
              $c_nombre = SegRol::where('nombre', '=', $nombre)->count();
              if($c_nombre < 1)
              {
                $iu                    = new SegRol;
                $iu->estado            = $estado;
                $iu->nombre            = $nombre;
                $iu->lugar_dependencia = $ld_json;
                $iu->save();

                $id = $iu->id;

                $respuesta['respuesta'] .= "El ROL se registro con éxito.";
                $respuesta['sw']         = 1;

                if($request->has('lugar_dependencia'))
                {
                    foreach($lugar_dependencia as $lugar_dependencia_id)
                    {
                        $iu1                       = new SegLdRol;
                        $iu1->lugar_dependencia_id = $lugar_dependencia_id;
                        $iu1->rol_id               = $id;
                        $iu1->save();
                    }
                }
              }
              else
              {
                $respuesta['respuesta'] .= "El NOMBRE del ROL ya fue registrado.";
              }
            }
            else
            {
              $c_nombre = SegRol::where('nombre', '=', $nombre)->where('id', '<>', $id)->count();
              if($c_nombre < 1)
              {
                $iu                    = SegRol::find($id);
                $iu->estado            = $estado;
                $iu->nombre            = $nombre;
                $iu->lugar_dependencia = $ld_json;
                $iu->save();

                $respuesta['respuesta'] .= "El ROL se edito con éxito.";
                $respuesta['sw']         = 1;
                $respuesta['iu']         = 2;

                $del1 = SegLdRol::where('rol_id', '=', $id);
                $del1->delete();

                if($request->has('lugar_dependencia'))
                {
                    foreach($lugar_dependencia as $lugar_dependencia_id)
                    {
                        $iu1                       = new SegLdRol;
                        $iu1->lugar_dependencia_id = $lugar_dependencia_id;
                        $iu1->rol_id              = $id;
                        $iu1->save();
                    }
                }
              }
              else
              {
                $respuesta['respuesta'] .= "El NOMBRE del ROL ya fue registrado.";
              }
            }
          //=== respuesta ===
            // sleep(5);
            return json_encode($respuesta);
        break;
      // === SELECT2 RELLENAR LUGAR DE DEPENDENCIA ===
      case '102':
        $respuesta = [
            'tipo' => $tipo,
            'sw'   => 1
        ];

        if($request->has('rol_id'))
        {
            $rol_id  = $request->input('rol_id');

            $query = SegLdRol::where("rol_id", "=", $rol_id)
                        ->select('lugar_dependencia_id')
                        ->get()
                        ->toArray();

            if(count($query) > 0)
            {
                $respuesta['consulta'] = $query;
                $respuesta['sw']       = 2;
            }
        }
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
      case '3':
        $respuesta = '';
        if($valor['d_json'] != '')
        {
            $sw = TRUE;
            foreach(json_decode($valor['d_json']) as $valor)
            {
                if($sw)
                {
                    $respuesta .= $valor;
                    $sw = FALSE;
                }
                else
                {
                    $respuesta .= "<br>" . $valor;
                }
            }
        }
        return($respuesta);
        break;
      default:
        break;
    }
  }
}
