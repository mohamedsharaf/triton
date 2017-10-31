<?php

namespace App\Http\Controllers\Institucion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;
use App\Libraries\UtilClass;

use App\Models\Seguridad\SegPermisoRol;
use App\Models\UbicacionGeografica\UbgeDepartamento;
use App\Models\UbicacionGeografica\UbgeMunicipio;
use App\Models\Institucion\InstUnidadDesconcentrada;
use App\Models\Institucion\InstLugarDependencia;

class UnidadDesconcentradaController extends Controller
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
        if(in_array(['codigo' => '0201'], $this->permisos))
        {
            $data = [
                'rol_id'                  => $this->rol_id,
                'permisos'                => $this->permisos,
                'title'                   => 'Unidades Desconcentradas',
                'home'                    => 'Inicio',
                'sistema'                 => 'Institución',
                'modulo'                  => 'Unidades Desconcentradas',
                'title_table'             => 'Unidades Desconcentradas',
                'estado_array'            => $this->estado,
                'lugar_dependencia_array' => InstLugarDependencia::where('estado', '=', 1)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray(),
                'departamento_array'      => UbgeDepartamento::where('estado', '=', 1)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray()
            ];
            return view('institucion.unidad_desconcentrada.unidad_desconcentrada')->with($data);
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
                    inst_unidades_desconcentradas.id,
                    inst_unidades_desconcentradas.lugar_dependencia_id,
                    inst_unidades_desconcentradas.municipio_id,
                    inst_unidades_desconcentradas.estado,
                    inst_unidades_desconcentradas.nombre,
                    inst_unidades_desconcentradas.direccion,

                    inst_lugares_dependencia.nombre AS lugar_dependencia,

                    ubge_municipios.nombre AS municipio,
                    ubge_municipios.provincia_id,

                    ubge_provincias.nombre AS provincia,
                    ubge_provincias.departamento_id,

                    ubge_departamentos.nombre AS departamento
                ";

                $array_where = "TRUE";
                $array_where .= $jqgrid->getWhere();

                $count = InstUnidadDesconcentrada::leftJoin("inst_lugares_dependencia", "inst_lugares_dependencia.id", "=", "inst_unidades_desconcentradas.lugar_dependencia_id")
                            ->leftJoin("ubge_municipios", "ubge_municipios.id", "=", "inst_unidades_desconcentradas.municipio_id")
                            ->leftJoin("ubge_provincias", "ubge_provincias.id", "=", "ubge_municipios.provincia_id")
                            ->leftJoin("ubge_departamentos", "ubge_departamentos.id", "=", "ubge_provincias.departamento_id")
                            ->whereRaw($array_where)
                            ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = InstUnidadDesconcentrada::leftJoin("inst_lugares_dependencia", "inst_lugares_dependencia.id", "=", "inst_unidades_desconcentradas.lugar_dependencia_id")
                            ->leftJoin("ubge_municipios", "ubge_municipios.id", "=", "inst_unidades_desconcentradas.municipio_id")
                            ->leftJoin("ubge_provincias", "ubge_provincias.id", "=", "ubge_municipios.provincia_id")
                            ->leftJoin("ubge_departamentos", "ubge_departamentos.id", "=", "ubge_provincias.departamento_id")
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
                        'estado'               => $row["estado"],
                        'lugar_dependencia_id' => $row["lugar_dependencia_id"],
                        'municipio_id'         => $row["municipio_id"],
                        'provincia_id'         => $row["provincia_id"],
                        'departamento_id'      => $row["departamento_id"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',
                        $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                        $row["nombre"],

                        $row["lugar_dependencia"],

                        $row["municipio"],

                        $row["provincia"],

                        $row["departamento"],

                        $row["direccion"],
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
                        'titulo'     => '<div class="text-center"><strong>UNIDADES DESCONCENTRADAS</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1
                    );
                    $opcion = 'n';
                    $error  = FALSE;

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if($id != '')
                    {
                        $opcion = 'e';
                        if(!in_array(['codigo' => '0203'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '0202'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                //=== OPERACION ===
                    $estado               = trim($request->input('estado'));
                    $municipio_id         = trim($request->input('municipio_id'));
                    $lugar_dependencia_id = trim($request->input('lugar_dependencia_id'));
                    $nombre               = strtoupper($util->getNoAcentoNoComilla(trim($request->input('nombre'))));
                    $direccion            = strtoupper($util->getNoAcentoNoComilla(trim($request->input('direccion'))));
                    if($opcion == 'n')
                    {
                        $c_nombre = InstUnidadDesconcentrada::where('nombre', '=', $nombre)->where('lugar_dependencia_id', '=', $lugar_dependencia_id)->count();
                        if($c_nombre < 1)
                        {
                            $iu                       = new InstUnidadDesconcentrada;
                            $iu->estado               = $estado;
                            $iu->municipio_id         = $municipio_id;
                            $iu->lugar_dependencia_id = $lugar_dependencia_id;
                            $iu->nombre               = $nombre;
                            $iu->direccion            = $direccion;
                            $iu->save();

                            $respuesta['respuesta'] .= "La UNIDAD DESCONCENTRADA se registro con éxito.";
                            $respuesta['sw']         = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El NOMBRE de la UNIDAD DESCONCENTRADA ya fue registrada.";
                        }
                    }
                    else
                    {
                        $c_nombre = InstUnidadDesconcentrada::where('nombre', '=', $nombre)->where('id', '<>', $id)->where('lugar_dependencia_id', '=', $lugar_dependencia_id)->count();
                        if($c_nombre < 1)
                        {
                            $iu                       = InstUnidadDesconcentrada::find($id);
                            $iu->estado               = $estado;
                            $iu->municipio_id         = $municipio_id;
                            $iu->lugar_dependencia_id = $lugar_dependencia_id;
                            $iu->nombre               = $nombre;
                            $iu->direccion            = $direccion;
                            $iu->save();

                            $respuesta['respuesta'] .= "La UNIDAD DESCONCENTRADA se edito con éxito.";
                            $respuesta['sw']         = 1;
                            $respuesta['iu']         = 2;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El NOMBRE de la UNIDAD DESCONCENTRADA ya fue registrada.";
                        }
                    }
                //=== respuesta ===
                return json_encode($respuesta);
                break;
            // === SELECT2 DEPARTAMENTO, PROVINCIA Y MUNICIPIO ===
            case '100':

                if($request->has('q'))
                {
                    $nombre     = $request->input('q');
                    // $nombre     = $q['term'];
                    $estado     = trim($request->input('estado'));
                    $page_limit = trim($request->input('page_limit'));

                    $query = UbgeMunicipio::leftJoin("ubge_provincias", "ubge_provincias.id", "=", "ubge_municipios.provincia_id")
                                ->leftJoin("ubge_departamentos", "ubge_departamentos.id", "=", "ubge_provincias.departamento_id")
                                ->whereRaw("CONCAT_WS(', ', ubge_departamentos.nombre, ubge_provincias.nombre, ubge_municipios.nombre) ilike '%$nombre%'")
                                //->where(DB::raw("CONCAT_WS(', ', ubge_departamentos.nombre, ubge_provincias.nombres, ubge_municipios.nombre)"), "ilike", "'%$nombre%'")
                                ->where("ubge_municipios.estado", "=", $estado)
                                ->select(DB::raw("ubge_municipios.id, CONCAT_WS(', ', ubge_departamentos.nombre, ubge_provincias.nombre, ubge_municipios.nombre) AS text"))
                                ->orderByRaw("CONCAT_WS(', ', ubge_departamentos.nombre, ubge_provincias.nombre, ubge_municipios.nombre) ASC")
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
