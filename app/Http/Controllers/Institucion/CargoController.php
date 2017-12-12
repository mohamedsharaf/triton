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
use App\Models\Institucion\InstTipoCargo;
use App\Models\Institucion\InstCargo;

use Maatwebsite\Excel\Facades\Excel;

use Exception;

class CargoController extends Controller
{
    private $estado;
    private $acefalia;

    private $rol_id;
    private $permisos;

    private $reporte_1;

    public function __construct()
    {
        $this->middleware('auth');

        $this->estado = [
            '1' => 'HABILITADO',
            '2' => 'INHABILITADO'
        ];

        $this->acefalia = [
            '1' => 'SI',
            '2' => 'NO'
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
        if(in_array(['codigo' => '0401'], $this->permisos))
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
                'title'                   => 'Cargos',
                'home'                    => 'Inicio',
                'sistema'                 => 'Institución',
                'modulo'                  => 'Cargos',
                'title_table'             => 'Cargos',
                'estado_array'            => $this->estado,
                'acefalia_array'          => $this->acefalia,
                'tipo_cargo_array'        => InstTipoCargo::where("estado", "=", 1)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray(),
                'lugar_dependencia_array' => InstLugarDependencia::whereRaw($array_where)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray()
            ];
            return view('institucion.cargo.cargo')->with($data);
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

                $tabla1 = "inst_cargos";
                $tabla2 = "inst_tipos_cargo";
                $tabla3 = "inst_auos";
                $tabla4 = "inst_lugares_dependencia";

                $select = "
                    $tabla1.id,
                    $tabla1.auo_id,
                    $tabla1.cargo_id,
                    $tabla1.tipo_cargo_id,
                    $tabla1.estado,
                    $tabla1.item_contrato,
                    $tabla1.acefalia,
                    $tabla1.nombre,

                    a2.nombre AS tipo_cargo,

                    a3.nombre AS cargo,

                    a4.lugar_dependencia_id,
                    a4.nombre AS auo,

                    a5.nombre AS lugar_dependencia
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
                            $array_where_1 .= " AND (a4.lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
                            $c_1_sw        = FALSE;
                        }
                        else
                        {
                            $array_where_1 .= " OR a4.lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
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
                    $array_where .= " AND a4.lugar_dependencia_id=0 AND ";
                }

                $array_where .= $jqgrid->getWhere();

                $count = InstCargo::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_cargo_id")
                    ->leftJoin("$tabla1 AS a3", "a3.id", "=", "$tabla1.cargo_id")
                    ->leftJoin("$tabla3 AS a4", "a4.id", "=", "$tabla1.auo_id")
                    ->leftJoin("$tabla4 AS a5", "a5.id", "=", "a4.lugar_dependencia_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = InstCargo::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_cargo_id")
                    ->leftJoin("$tabla1 AS a3", "a3.id", "=", "$tabla1.cargo_id")
                    ->leftJoin("$tabla3 AS a4", "a4.id", "=", "$tabla1.auo_id")
                    ->leftJoin("$tabla4 AS a5", "a5.id", "=", "a4.lugar_dependencia_id")
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
                        'cargo_id'             => $row["cargo_id"],
                        'tipo_cargo_id'        => $row["tipo_cargo_id"],
                        'acefalia'             => $row["acefalia"],
                        'estado'               => $row["estado"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',
                        $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                        $this->utilitarios(array('tipo' => '2', 'acefalia' => $row["acefalia"])),
                        $row["tipo_cargo"],
                        $row["item_contrato"],
                        $row["nombre"],
                        $row["cargo"],
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
                        'titulo'     => '<div class="text-center"><strong>CARGO</strong></div>',
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
                        if(!in_array(['codigo' => '0403'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '0402'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                // === VALIDATE ===
                    try
                    {
                        $validator = $this->validate($request,[
                            'auo_id'        => 'required',
                            'tipo_cargo_id' => 'required',
                            'item_contrato' => 'required|max:50',
                            'nombre'        => 'required|max:250'
                        ],
                        [
                            'auo_id.required' => 'El campo ÁREA O UNIDAD ORGANIZACIONAL es obligatorio.',

                            'tipo_cargo_id.required' => 'El campo TIPO DE CARGO es obligatorio.',

                            'item_contrato.required' => 'El campo NUMERO es obligatorio.',
                            'item_contrato.max'      => 'El campo NUMERO debe ser :max caracteres como máximo.',

                            'nombre.required' => 'El campo CARGO es obligatorio.',
                            'nombre.max'      => 'El campo CARGO debe ser :max caracteres como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['estado']        = trim($request->input('estado'));
                    // $data1['acefalia']      = trim($request->input('acefalia'));
                    $data1['auo_id']        = trim($request->input('auo_id'));
                    $data1['cargo_id']      = trim($request->input('cargo_id'));
                    $data1['tipo_cargo_id'] = trim($request->input('tipo_cargo_id'));
                    $data1['item_contrato'] = strtoupper($util->getNoAcentoNoComilla(trim($request->input('item_contrato'))));
                    $data1['nombre']        = strtoupper($util->getNoAcentoNoComilla(trim($request->input('nombre'))));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $sw_tipo_cargo = FALSE;

                        $consulta2 = InstCargo::where('auo_id', '=', $data1['auo_id'])
                            ->whereNull('cargo_id')
                            ->count();

                        if($consulta2 < 2)
                        {
                            $sw_cargo_null = FALSE;
                            if($consulta2 == 0)
                            {
                                $sw_cargo_null = TRUE;
                            }
                            else if($consulta2 == 1)
                            {
                                if($data1['cargo_id'] != '')
                                {
                                    $sw_cargo_null = TRUE;
                                }
                            }

                            if($sw_cargo_null)
                            {
                                switch($data1['tipo_cargo_id'])
                                {
                                    case '1':
                                        if(is_numeric($data1['item_contrato']))
                                        {
                                            $consulta1 = InstCargo::where('item_contrato', '=', $data1['item_contrato'])
                                                ->count();
                                            if($consulta1 < 1)
                                            {
                                                $sw_tipo_cargo = TRUE;
                                            }
                                            else
                                            {
                                                $respuesta['respuesta'] .= "El NÚMERO DE ITEM ya existe.";
                                            }
                                        }
                                        else
                                        {
                                            $respuesta['respuesta'] .= "El NÚMERO DE ITEM debe de ser número entero.";
                                        }
                                        break;
                                    default:
                                        $sw_tipo_cargo = TRUE;
                                        break;
                                }
                            }
                            else
                            {
                                $respuesta['respuesta'] .= "Favor seleccione CARGO DE DEPENDENCIA.";
                            }
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "Existe " . $consulta2 . " cargos que no tienen CARGO DE DEPENDENCIA.";
                        }

                        if($sw_tipo_cargo)
                        {
                            $iu                = new InstCargo;
                            $iu->estado        = $data1['estado'];
                            // $iu->acefalia      = $data1['acefalia'];
                            $iu->auo_id        = $data1['auo_id'];
                            $iu->cargo_id      = $data1['cargo_id'];
                            $iu->tipo_cargo_id = $data1['tipo_cargo_id'];
                            $iu->item_contrato = $data1['item_contrato'];
                            $iu->nombre        = $data1['nombre'];
                            $iu->save();

                            $respuesta['respuesta'] .= "El CARGO fue registrado con éxito.";
                            $respuesta['sw']         = 1;
                        }
                    }
                    else
                    {
                        $sw_tipo_cargo = FALSE;

                        $consulta2 = InstCargo::where('auo_id', '=', $data1['auo_id'])
                            ->whereNull('cargo_id')
                            ->where('id', '<>', $id)
                            ->count();

                        if($consulta2 < 2)
                        {
                            $sw_cargo_null = FALSE;
                            if($consulta2 == 0)
                            {
                                $sw_cargo_null = TRUE;
                            }
                            else if($consulta2 == 1)
                            {
                                if($data1['cargo_id'] != '')
                                {
                                    $sw_cargo_null = TRUE;
                                }
                            }

                            if($sw_cargo_null)
                            {
                                switch($data1['tipo_cargo_id'])
                                {
                                    case '1':
                                        if(is_numeric($data1['item_contrato']))
                                        {
                                            $consulta1 = InstCargo::where('item_contrato', '=', $data1['item_contrato'])
                                                ->where('id', '<>', $id)
                                                ->count();
                                            if($consulta1 < 1)
                                            {
                                                $sw_tipo_cargo = TRUE;
                                            }
                                            else
                                            {
                                                $respuesta['respuesta'] .= "El NÚMERO DE ITEM ya existe.";
                                            }
                                        }
                                        else
                                        {
                                            $respuesta['respuesta'] .= "El NÚMERO DE ITEM debe ser número entero.";
                                        }
                                        break;
                                    default:
                                        $sw_tipo_cargo = TRUE;
                                        break;
                                }
                            }
                            else
                            {
                                $respuesta['respuesta'] .= "Favor seleccione CARGO DE DEPENDENCIA.";
                            }
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "Existe " . $consulta2 . " cargos que no tienen CARGO DE DEPENDENCIA.";
                        }

                        if($sw_tipo_cargo)
                        {
                            $iu                = InstCargo::find($id);
                            $iu->estado        = $data1['estado'];
                            // $iu->acefalia      = $data1['acefalia'];
                            $iu->auo_id        = $data1['auo_id'];
                            $iu->cargo_id      = $data1['cargo_id'];
                            $iu->tipo_cargo_id = $data1['tipo_cargo_id'];
                            $iu->item_contrato = $data1['item_contrato'];
                            $iu->nombre        = $data1['nombre'];
                            $iu->save();

                            $respuesta['respuesta'] .= "El CARGO se edito con éxito.";
                            $respuesta['sw']         = 1;
                            $respuesta['iu']         = 2;
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

                    $array_where = "CONCAT_WS(' - ', a2.nombre, inst_auos.nombre) ilike '%$nombre%'";

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
                                $array_where_1 .= " AND (lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
                                $c_1_sw      = FALSE;
                            }
                            else
                            {
                                $array_where_1 .= " OR lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
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
                        $array_where .= " AND lugar_dependencia_id=0";
                    }

                    $query = InstAuo::leftJoin("inst_lugares_dependencia AS a2", "a2.id", "=", "inst_auos.lugar_dependencia_id")
                        ->whereRaw($array_where)
                        ->where("inst_auos.estado", "=", $estado)
                        ->select(DB::raw("inst_auos.id, CONCAT_WS(' - ', a2.nombre, inst_auos.nombre) AS text"))
                        ->orderByRaw("CONCAT_WS(' - ', a2.nombre, inst_auos.nombre) ASC")
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

            // === SELECT2 ORGANIGRAMA CARGOS POR AREA O UNIDAD DESCONCENTRADA ===
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

                        $organigrama_array = [];

                        $consulta1 = InstCargo::leftJoin("inst_tipos_cargo AS a2", "a2.id", "=", "inst_cargos.tipo_cargo_id")
                            ->whereNull('inst_cargos.cargo_id')
                            ->where("inst_cargos.estado", "=", 1)
                            ->where("inst_cargos.auo_id", "=", $auo_id)
                            ->select('inst_cargos.id', 'inst_cargos.tipo_cargo_id', 'inst_cargos.item_contrato', 'inst_cargos.nombre', 'inst_cargos.acefalia', 'a2.nombre AS tipo_cargo')
                            ->first()
                            ->toArray();

                        if(count($consulta1) > 0)
                        {
                            if($consulta1['tipo_cargo_id'] == 1)
                            {
                                $organigrama_array['name']  = $consulta1['tipo_cargo'] . ' ' . $consulta1['item_contrato'] . ' - ¿ACEFALO? ' . $this->acefalia[$consulta1['acefalia']];
                            }
                            else
                            {
                                $organigrama_array['name']  = $consulta1['tipo_cargo'] . ' - ¿ACEFALO? ' . $this->acefalia[$consulta1['acefalia']];
                            }
                            $organigrama_array['title'] = $consulta1['nombre'];

                            $organigrama_array['children'] = $this->utilitarios(['tipo' => '10', 'cargo_id' => $consulta1['id']]);

                            $respuesta['respuesta'] = $organigrama_array;
                            $respuesta['sw']        = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "¡No existe CARGOS en el ÁREA O UNIDAD ORGANIZACIONAL seleccionada!<br>¡Verifique!";
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "¡Favor seleccione un ÁREA O UNIDAD ORGANIZACIONAL!<br>¡Verifique!";
                    }

                return json_encode($respuesta);
                break;

            // === SELECT2 CARGOS POR UNIDAD DESCONCENTRADA ===
            case '102':
                $respuesta = [
                    'tipo' => $tipo,
                    'sw'   => 1
                ];
                if($request->has('auo_id'))
                {
                    $auo_id  = $request->input('auo_id');
                    $query = InstCargo::where("auo_id", "=", $auo_id)
                        ->where("estado", "=", 1)
                        ->select('id', 'nombre')
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
            case '2':
                switch($valor['acefalia'])
                {
                    case '1':
                        $respuesta = '<span class="label label-primary font-sm">' . $this->acefalia[$valor['acefalia']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->acefalia[$valor['acefalia']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">?</span>';
                        return($respuesta);
                        break;
                }
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

    public function reportes(Request $request)
    {
        $tipo = $request->input('tipo');

        switch($tipo)
        {
            case '1':
                Excel::create('Cargos_' . date('Y-m-d_H-i-s'), function($excel){
                    $tabla1 = "inst_cargos";
                    $tabla2 = "inst_tipos_cargo";
                    $tabla3 = "inst_auos";
                    $tabla4 = "inst_lugares_dependencia";

                    $select = "
                        $tabla1.id,
                        $tabla1.auo_id,
                        $tabla1.cargo_id,
                        $tabla1.tipo_cargo_id,
                        $tabla1.estado,
                        $tabla1.item_contrato,
                        $tabla1.acefalia,
                        $tabla1.nombre,

                        a2.nombre AS tipo_cargo,

                        a3.nombre AS cargo,

                        a4.lugar_dependencia_id,
                        a4.nombre AS auo,

                        a5.nombre AS lugar_dependencia
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
                                $array_where_1 .= " AND (a4.lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
                                $c_1_sw        = FALSE;
                            }
                            else
                            {
                                $array_where_1 .= " OR a4.lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
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
                        $array_where .= " AND a4.lugar_dependencia_id=0 AND ";
                    }

                    $this->reporte_1 = InstCargo::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_cargo_id")
                        ->leftJoin("$tabla1 AS a3", "a3.id", "=", "$tabla1.cargo_id")
                        ->leftJoin("$tabla3 AS a4", "a4.id", "=", "$tabla1.auo_id")
                        ->leftJoin("$tabla4 AS a5", "a5.id", "=", "a4.lugar_dependencia_id")
                        ->whereRaw($array_where)
                        ->select(DB::raw($select))
                        ->orderBy('a5.nombre', 'ASC')
                        ->orderBy('a4.nombre', 'ASC')
                        ->orderBy("$tabla1.tipo_cargo_id", 'ASC')
                        ->orderBy("$tabla1.item_contrato", 'ASC')
                        ->orderBy("$tabla1.nombre", 'ASC')
                        ->get()
                        ->toArray();

                    $excel->sheet('Cargos Dependencia', function($sheet){
                        $sheet->row(1, [
                            'LUGAR DE DEPENDENCIA',
                            'AREA UNIDAD ORGANIZACIONAL',
                            'CARGO DE DEPENDENCIA',
                            'ESTADO',
                            '¿ACEFALO?',
                            'TIPO DE CARGO',
                            'NUMERO',
                            'CARGO'
                        ]);

                        $sheet->row(1, function($row){
                            $row->setBackground('#CCCCCC');
                            $row->setFontWeight('bold');
                            $row->setAlignment('center');
                        });

                        $sheet->freezeFirstRow();
                        $sheet->setAutoFilter();

                        foreach($this->reporte_1 as $index => $row1)
                        {
                            $sheet->row($index+2, [
                                $row1["lugar_dependencia"],
                                $row1["auo"],
                                $row1["cargo"],
                                $this->estado[$row1["estado"]],
                                $this->acefalia[$row1["acefalia"]],
                                $row1["tipo_cargo"],
                                $row1["item_contrato"],
                                $row1["nombre"]
                            ]);
                        }

                        $sheet->setAutoSize(true);
                    });

                    $excel->sheet('Cargos', function($sheet){
                        $sheet->row(1, [
                            'LUGAR DE DEPENDENCIA',
                            'AREA UNIDAD ORGANIZACIONAL',
                            '¿ACEFALO?',
                            'TIPO DE CARGO',
                            'NUMERO',
                            'CARGO'
                        ]);

                        $sheet->row(1, function($row){
                            $row->setBackground('#CCCCCC');
                            $row->setFontWeight('bold');
                            $row->setAlignment('center');
                        });

                        $sheet->freezeFirstRow();
                        $sheet->setAutoFilter();

                        foreach($this->reporte_1 as $index => $row1)
                        {
                            $sheet->row($index+2, [
                                $row1["lugar_dependencia"],
                                $row1["auo"],
                                $this->acefalia[$row1["acefalia"]],
                                $row1["tipo_cargo"],
                                $row1["item_contrato"],
                                $row1["nombre"]
                            ]);

                            if($row1["acefalia"] == 1)
                            {
                                $sheet->row($index+2, function($row){
                                    $row->setBackground('#ffc7ce');
                                    $row->setFontColor('#9c0006');
                                });
                            }
                        }

                        $sheet->cells('C1:D' . ($index + 2), function($cells){
                            $cells->setAlignment('center');
                        });

                        $sheet->setAutoSize(true);
                    });
                })->export('xlsx');
                break;
            default:
                break;
        }
    }
}