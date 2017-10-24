<?php

namespace App\Http\Controllers\Rrhh;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;
use App\Libraries\UtilClass;

use App\Models\Seguridad\SegModulo;
use App\Models\Seguridad\SegPermiso;
use App\Models\Seguridad\SegPermisoRol;

class PersonaController extends Controller
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
                'rol_id'       => $this->rol_id,
                'permisos'     => $this->permisos,
                'title'        => 'Gestor de permisos',
                'home'         => 'Inicio',
                'sistema'      => 'Seguridad',
                'modulo'       => 'Gestor de permisos',
                'title_table'  => 'Permisos',
                'estado_array' => $this->estado,
                'modulo_array' => SegModulo::where('estado', '=', 1)->select("id", "nombre")->orderBy("nombre")->get()->toArray()
            ];
            return view('seguridad.permiso.permiso')->with($data);
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
          seg_permisos.id,
          seg_permisos.modulo_id,
          seg_permisos.estado,
          seg_permisos.codigo,
          seg_permisos.nombre,
          seg_modulos.nombre AS modulo
        ";

        $array_where = [
        ];

        $array_where = array_merge($array_where, $jqgrid->getWhere());

        $count = SegPermiso::leftJoin("seg_modulos", "seg_modulos.id", "=", "seg_permisos.modulo_id")
          ->where($array_where)
          ->count();

        $limit_offset = $jqgrid->getLimitOffset($count);

        $query = SegPermiso::leftJoin("seg_modulos", "seg_modulos.id", "=", "seg_permisos.modulo_id")
          ->where($array_where)
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
              'estado'    => $row["estado"],
              'modulo_id' => $row["modulo_id"]
            );

            $respuesta['rows'][$i]['id'] = $row["id"];
            $respuesta['rows'][$i]['cell'] = array(
                '',
                $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                // $row["estado"],
                $row["codigo"],
                $row["nombre"],
                $row["modulo"],
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
        'titulo'    => 'GESTOR DE PERMISOS',
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
          $util = new UtilClass();
          // return strtoupper($util->getNoAcentoNoComilla(trim('"   sérÁñ    "')));

        // === INICIALIZACION DE VARIABLES ===
          $data1     = array();
          $respuesta = array(
            'sw'         => 0,
            'titulo'     => '<div class="text-center"><strong>GESTOR DE PERMISOS</strong></div>',
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
            $modulo_id = trim($request->input('modulo_id'));
            $estado    = trim($request->input('estado'));
            $nombre    = strtoupper($util->getNoAcentoNoComilla(trim($request->input('nombre'))));
            if($opcion == 'n')
            {
              $c_nombre = SegPermiso::where('nombre', '=', $nombre)->where('modulo_id', '=', $modulo_id)->count();
              if($c_nombre < 1)
              {
                $seg_modulo    = SegModulo::where('id', '=', $modulo_id)->select("codigo")->first();
                $iu            = new SegPermiso;
                $iu->modulo_id = $modulo_id;
                $iu->estado    = $estado;
                $iu->codigo    = $seg_modulo->codigo . str_pad((SegPermiso::where('modulo_id', '=', $modulo_id)->count())+1, 2, "0", STR_PAD_LEFT);
                $iu->nombre    = $nombre;
                $iu->save();

                $respuesta['respuesta'] .= "El PERMISO se registro con éxito.";
                $respuesta['sw']         = 1;
              }
              else
              {
                $respuesta['respuesta'] .= "El NOMBRE del PERMISO ya fue registro.";
              }
            }
            else
            {
              $seg_permiso = SegPermiso::where('id', '=', $id)->select("modulo_id")->first();
              $c_nombre    = SegPermiso::where('nombre', '=', $nombre)->where('id', '<>', $id)->where('modulo_id', '=', $seg_permiso->modulo_id)->count();
              if($c_nombre < 1)
              {
                $iu         = SegPermiso::find($id);
                $iu->estado = $estado;
                $iu->nombre = $nombre;
                $iu->save();

                $respuesta['respuesta'] .= "El PERMISO se edito con éxito.";
                $respuesta['sw']         = 1;
                $respuesta['iu']         = 2;
              }
              else
              {
                $respuesta['respuesta'] .= "El NOMBRE del PERMISO ya fue registro.";
              }
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
