<?php

namespace App\Http\Controllers\Institucion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;
use App\Libraries\UtilClass;

use App\Models\Seguridad\SegPermisoRol;
use App\Models\Seguridad\SegLdUser;
use App\Models\Institucion\InstLugarDependencia;
use App\Models\Institucion\InstAuo;

use Exception;

class AuoController extends Controller
{
    private $estado;

    private $rol_id;
    private $permisos;

    public function __construct()
    {
        $this->middleware('auth');

        $this->estado = [
            '1' => 'HABILITADO',
            '2' => 'INHABILITADO'
        ];
    }

    public function index()
    {
        $this->rol_id   = Auth::user()->rol_id;
        $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                            ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                            ->select("seg_permisos.codigo")
                            ->get()
                            ->toArray();
        if(in_array(['codigo' => '0301'], $this->permisos))
        {
            $user_id = Auth::user()->id;

            $consulta1 = SegLdUser::where("seg_ld_users.user_id", "=", $user_id)
                    ->select('lugar_dependencia_id')
                    ->get()
                    ->toArray();

            $array_where = 'estado=1';
            if(count($consulta1) > 0)
            {
                $c_1_sw        = TRUE;
                $c_2_sw        = TRUE;
                $array_where_1 = '';
                foreach ($consulta1 as $valor)
                {
                    if($valor['lugar_dependencia_id'] == '1')
                    {
                        $c_2_sw = FALSE;
                        break;
                    }

                    if($c_1_sw)
                    {
                        $array_where_1 .= " AND (id=" . $valor['lugar_dependencia_id'];
                        $c_1_sw      = FALSE;
                    }
                    else
                    {
                        $array_where_1 .= " OR id=" . $valor['lugar_dependencia_id'];
                    }
                }
                $array_where_1 .= ")";

                if($c_2_sw)
                {
                    $array_where .= $array_where_1;
                }
            }
            else
            {
                $array_where .= " AND id=0";
            }

            $data = [
                'rol_id'                  => $this->rol_id,
                'permisos'                => $this->permisos,
                'title'                   => 'Áreas y Unidades Organizacionales',
                'home'                    => 'Inicio',
                'sistema'                 => 'Institución',
                'modulo'                  => 'Áreas y Unidades Organizacionales',
                'title_table'             => 'Áreas y Unidades Organizacionales',
                'estado_array'            => $this->estado,
                'lugar_dependencia_array' => InstLugarDependencia::whereRaw($array_where)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray()
            ];
            return view('institucion.auo.auo')->with($data);
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

                $tabla1 = "inst_auos";
                $tabla2 = "inst_lugares_dependencia";

                $select = "
                    $tabla1.id,
                    $tabla1.lugar_dependencia_id,
                    $tabla1.auo_id,
                    $tabla1.estado,
                    $tabla1.nombre,

                    a2.nombre AS auo,

                    a3.nombre AS lugar_dependencia
                ";

                $array_where = 'TRUE';

                $user_id = Auth::user()->id;

                $consulta1 = SegLdUser::where("user_id", "=", $user_id)
                    ->select('lugar_dependencia_id')
                    ->get()
                    ->toArray();
                if(count($consulta1) > 0)
                {
                    $c_1_sw        = TRUE;
                    $c_2_sw        = TRUE;
                    $array_where_1 = '';
                    foreach ($consulta1 as $valor)
                    {
                        if($valor['lugar_dependencia_id'] == '1')
                        {
                            $c_2_sw = FALSE;
                            break;
                        }

                        if($c_1_sw)
                        {
                            $array_where_1 .= " AND ($tabla1.lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
                            $c_1_sw        = FALSE;
                        }
                        else
                        {
                            $array_where_1 .= " OR $tabla1.lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
                        }
                    }
                    $array_where_1 .= ")";

                    if($c_2_sw)
                    {
                        $array_where .= $array_where_1;
                    }
                }
                else
                {
                    $array_where .= " AND $tabla1.lugar_dependencia_id=0 AND ";
                }

                $array_where .= $jqgrid->getWhere();

                $count = InstAuo::leftJoin("$tabla1 AS a2", "a2.id", "=", "$tabla1.auo_id")
                    ->leftJoin("$tabla2 AS a3", "a3.id", "=", "$tabla1.lugar_dependencia_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = InstAuo::leftJoin("$tabla1 AS a2", "a2.id", "=", "$tabla1.auo_id")
                    ->leftJoin("$tabla2 AS a3", "a3.id", "=", "$tabla1.lugar_dependencia_id")
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
                        'lugar_dependencia_id' => $row["lugar_dependencia_id"],
                        'auo_id'               => $row["auo_id"],
                        'estado'               => $row["estado"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',
                        $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                        $row["nombre"],
                        $row["auo"],
                        $row["lugar_dependencia"],
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
                'titulo'    => 'GESTOR DE USUARIO',
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
                        'titulo'     => '<div class="text-center"><strong>ÁREA O UNIDAD ORGANIZACIONAL</strong></div>',
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
                        if(!in_array(['codigo' => '0303'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '0302'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                // === VALIDATE ===
                    try
                    {
                        $validator = $this->validate($request,[
                            'lugar_dependencia_id' => 'required',
                            'auo_id'               => 'required',
                            'nombre'               => 'required|max:250'
                        ],
                        [
                            'lugar_dependencia_id.required' => 'El campo LUGAR DE DEPENDENCIA es obligatorio.',

                            'auo_id.required' => 'El campo ÁREA O UNIDAD ORGANIZACIONAL DE DEPENDENCIA es obligatorio.',

                            'nombre.required' => 'El campo ÁREA O UNIDAD ORGANIZACIONAL es obligatorio.',
                            'nombre.max'      => 'El campo ÁREA O UNIDAD ORGANIZACIONAL debe ser :max caracteres como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['estado']               = trim($request->input('estado'));
                    $data1['lugar_dependencia_id'] = trim($request->input('lugar_dependencia_id'));
                    $data1['auo_id']               = trim($request->input('auo_id'));
                    $data1['nombre']               = strtoupper($util->getNoAcentoNoComilla(trim($request->input('nombre'))));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $consulta1 = InstAuo::where('lugar_dependencia_id', '=', $data1['lugar_dependencia_id'])->where('nombre', '=', $data1['nombre'])->count();
                        if($consulta1 < 1)
                        {
                            $iu                       = new InstAuo;
                            $iu->lugar_dependencia_id = $data1['lugar_dependencia_id'];
                            $iu->auo_id               = $data1['auo_id'];
                            $iu->estado               = $data1['estado'];
                            $iu->nombre               = $data1['nombre'];
                            $iu->save();

                            $id = $iu->id;

                            $respuesta['respuesta'] .= "El ÁREA O UNIDAD ORGANIZACIONAL fue registrado con éxito.";
                            $respuesta['sw']         = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El ÁREA O UNIDAD ORGANIZACIONAL en el LUGAR DE DEPENDENCIA ya fue registrada.";
                        }
                    }
                    else
                    {
                        $consulta1 = InstAuo::where('lugar_dependencia_id', '=', $data1['lugar_dependencia_id'])->where('nombre', '=', $data1['nombre'])->where('id', '<>', $id)->count();
                        if($consulta1 < 1)
                        {
                            $iu                       = InstAuo::find($id);
                            $iu->lugar_dependencia_id = $data1['lugar_dependencia_id'];
                            $iu->auo_id               = $data1['auo_id'];
                            $iu->estado               = $data1['estado'];
                            $iu->nombre               = $data1['nombre'];
                            $iu->save();

                            $respuesta['respuesta'] .= "El ÁREA O UNIDAD ORGANIZACIONAL se edito con éxito.";
                            $respuesta['sw']         = 1;
                            $respuesta['iu']         = 2;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El ÁREA O UNIDAD ORGANIZACIONAL en el LUGAR DE DEPENDENCIA ya fue registrada.";
                        }
                    }
                return json_encode($respuesta);
                break;

            // === SELECT2 RELLENAR AREA O UNIDAD DESCONCENTRADA POR LUGAR DE DEPENDENCIA ===
            case '100':
                if($request->has('q'))
                {
                    $nombre     = $request->input('q');
                    $estado     = trim($request->input('estado'));
                    $page_limit = trim($request->input('page_limit'));

                    $user_id = Auth::user()->id;

                    $consulta1 = SegLdUser::where("seg_ld_users.user_id", "=", $user_id)
                            ->select('lugar_dependencia_id')
                            ->get()
                            ->toArray();

                    $array_where = "nombre ilike '%$nombre%'";

                    if(count($consulta1) > 0)
                    {
                        $c_1_sw        = TRUE;
                        $c_2_sw        = TRUE;
                        $array_where_1 = "";
                        foreach ($consulta1 as $valor)
                        {
                            if($valor['lugar_dependencia_id'] == '1')
                            {
                                $c_2_sw = FALSE;
                                break;
                            }

                            if($c_1_sw)
                            {
                                $array_where_1 .= " AND (id=" . $valor['lugar_dependencia_id'];
                                $c_1_sw      = FALSE;
                            }
                            else
                            {
                                $array_where_1 .= " OR id=" . $valor['lugar_dependencia_id'];
                            }
                        }
                        $array_where_1 .= ")";

                        if($c_2_sw)
                        {
                            $array_where .= $array_where_1;
                        }
                    }
                    else
                    {
                        $array_where .= " AND id=0";
                    }

                    $query = InstAuo::whereRaw($array_where)
                                ->where("estado", "=", $estado)
                                ->select('id', 'nombre AS text')
                                ->orderBy("nombre")
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

            // === SELECT2 ORGANIGRAMA AREA O UNIDAD DESCONCENTRADA ===
            case '101':
                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>ALERTA</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo
                    );
                //=== OPERACION ===
                    if($request->has('auo_id'))
                    {
                        $auo_id     = trim($request->input('auo_id'));

                        $user_id = Auth::user()->id;

                        $consulta1 = SegLdUser::where("seg_ld_users.user_id", "=", $user_id)
                                ->select('lugar_dependencia_id')
                                ->get()
                                ->toArray();

                        $array_where = "TRUE";

                        if(count($consulta1) > 0)
                        {
                            $c_1_sw        = TRUE;
                            $c_2_sw        = TRUE;
                            $array_where_1 = "";
                            foreach ($consulta1 as $valor)
                            {
                                if($valor['lugar_dependencia_id'] == '1')
                                {
                                    $c_2_sw = FALSE;
                                    break;
                                }

                                if($c_1_sw)
                                {
                                    $array_where_1 .= " AND (id=" . $valor['lugar_dependencia_id'];
                                    $c_1_sw      = FALSE;
                                }
                                else
                                {
                                    $array_where_1 .= " OR id=" . $valor['lugar_dependencia_id'];
                                }
                            }
                            $array_where_1 .= ")";

                            if($c_2_sw)
                            {
                                $array_where .= $array_where_1;
                            }
                        }
                        else
                        {
                            $array_where .= " AND id=0";
                        }

                        $organigrama_array = [];

                        $consulta1 = InstAuo::whereRaw($array_where)
                                    ->where("estado", "=", 1)
                                    ->where("id", "=", $auo_id)
                                    ->select('id', 'nombre')
                                    ->first()
                                    ->toArray();

                        if(count($consulta1) > 0)
                        {
                            $organigrama_array['name'] = $consulta1['nombre'];

                            $organigrama_array['children'] = $this->utilitarios(['tipo' => '10', 'auo_id' => $auo_id]);

                            $respuesta['respuesta'] = $organigrama_array;
                            $respuesta['sw'] = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "¡No existe en su LUGAR DE DEPENDENCIA el ÁREA O UNIDAD ORGANIZACIONAL!<br>¡Verifique!";
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "¡Favor seleccione un ÁREA O UNIDAD ORGANIZACIONAL!<br>¡Verifique!";
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
            case '2':
                if($valor['imagen'] == '')
                {
                    return '';
                }
                else
                {
                    return "<img  width='100%' class='img-thumbnail' alt='Imagen Personal' src='" . asset($this->public_url . $valor['imagen']) . "' />";
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
            case '10':
                $organigrama_array = [];
                $consulta1 = InstAuo::where("estado", "=", 1)
                                    ->where("auo_id", "=", $valor['auo_id'])
                                    ->select('id', 'nombre')
                                    ->orderBy("nombre")
                                    ->get()
                                    ->toArray();

                if(count($consulta1) > 0)
                {
                    foreach ($consulta1 as $row1)
                    {
                        $organigrama_array[] = [
                            'name'     => $row1['nombre'],
                            'children' => $this->utilitarios(['tipo' => '10', 'auo_id' => $row1['id']])
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
