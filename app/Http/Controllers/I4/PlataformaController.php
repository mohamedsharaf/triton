<?php

namespace App\Http\Controllers\I4;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;
use App\Libraries\JqgridMysqlClass;
use App\Libraries\UtilClass;

use App\Models\Seguridad\SegPermisoRol;
use App\Models\Seguridad\SegLdUser;
use App\User;

use App\Models\I4\RecintoCarcelario;
use App\Models\I4\Muni;
use App\Models\I4\Dep;

use Maatwebsite\Excel\Facades\Excel;
use PDF;

use Exception;

class PlataformaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->estado = [
            '1' => 'HABILITADO',
            '2' => 'INHABILITADO'
        ];

        $this->tipo_recinto = [
            '1' => 'RECINTO PENITENCIARIO',
            '2' => 'CARCELETA',
            '3' => 'CENTRO DE REHABILITACION JUVENIL'
        ];
    }

    public function index()
    {
        // $this->rol_id            = Auth::user()->rol_id;

        // $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
        //     ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
        //     ->select("seg_permisos.codigo")
        //     ->get()
        //     ->toArray();

        // if(in_array(['codigo' => '2101'], $this->permisos))
        // {
        //     $data = [
        //         'rol_id'             => $this->rol_id,
        //         'permisos'           => $this->permisos,
        //         'title'              => 'Recintos carcelarios',
        //         'home'               => 'Inicio',
        //         'sistema'            => 'i4',
        //         'modulo'             => 'Recinto carcelario',
        //         'title_table'        => 'Recintos carcelarios',
        //         'estado_array'       => $this->estado,
        //         'tipo_recinto_array' => $this->tipo_recinto,
        //         'departamento_array' => Dep::select(DB::raw("id, UPPER(Dep) AS nombre"))
        //                                     ->orderBy("Dep")
        //                                     ->get()
        //                                     ->toArray()
        //     ];
        //     return view('i4.recinto_carcelario.recinto_carcelario')->with($data);
        // }
        // else
        // {
        //     return back()->withInput();
        // }
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
                $jqgrid = new JqgridMysqlClass($request);

                $tabla1  = "RecintosCarcelarios";
                $tabla2  = "Muni";
                $tabla3  = "Dep";

                $select = "
                    $tabla1.id,
                    $tabla1.Muni_id,
                    $tabla1.estado,
                    $tabla1.tipo_recinto,
                    $tabla1.nombre,
                    $tabla1.created_at,
                    $tabla1.updated_at,

                    a2.Dep AS departamento_id,
                    UPPER(a2.Muni) AS municipio,

                    UPPER(a3.Dep) AS departamento
                ";

                $array_where = '';

                $user_id = Auth::user()->id;

                $array_where .= "TRUE";
                $array_where .= $jqgrid->getWhere();

                $count = RecintoCarcelario::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.Muni_id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.Dep")
                            ->whereRaw($array_where)
                            ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = RecintoCarcelario::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.Muni_id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.Dep")
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
                        'estado'          => $row["estado"],
                        'tipo_recinto'    => $row["tipo_recinto"],
                        'Muni_id'         => $row["Muni_id"],
                        'departamento_id' => $row["departamento_id"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',

                        $this->utilitarios(array('tipo' => '1', 'valor' => $row["estado"])),
                        ($row["tipo_recinto"] =="") ? "" : $this->tipo_recinto[$row["tipo_recinto"]],
                        $row["nombre"],
                        $row["municipio"],
                        $row["departamento"],

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
                'titulo'    => 'ERROR 500',
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
                        'titulo'     => '<div class="text-center"><strong>Recinto carcelario</strong></div>',
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
                        if(!in_array(['codigo' => '2103'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para MODIFICAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '2102'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                // === VALIDATE ===
                    try
                    {
                        $validator = $this->validate($request,[
                            'Muni_id'      => 'required',
                            'tipo_recinto' => 'required',
                            'nombre'       => 'required|max: 500'
                        ],
                        [
                            'Muni_id.required' => 'El campo UBICACION es obligatorio.',

                            'tipo_recinto.required' => 'El campo TIPO DE RECINTO es obligatorio.',

                            'nombre.required' => 'El campo NOMBRE es obligatorio.',
                            'nombre.max'     => 'El campo NOMBRE debe contener :max caracteres como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['estado']       = trim($request->input('estado'));
                    $data1['Muni_id']      = trim($request->input('Muni_id'));
                    $data1['tipo_recinto'] = trim($request->input('tipo_recinto'));
                    $data1['nombre']       = strtoupper($util->getNoAcentoNoComilla(trim($request->input('nombre'))));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $consulta1 = RecintoCarcelario::where('nombre', '=', $data1['nombre'])
                            ->where('Muni_id', '=', $data1['Muni_id'])
                            ->count();

                        if($consulta1 < 1)
                        {
                            $iu               = new RecintoCarcelario;
                            $iu->Muni_id      = $data1['Muni_id'];
                            $iu->estado       = $data1['estado'];
                            $iu->tipo_recinto = $data1['tipo_recinto'];
                            $iu->nombre       = $data1['nombre'];

                            $iu->save();

                            $respuesta['respuesta'] .= "El RECINTO CARCELARIO fue registrado con éxito.";
                            $respuesta['sw']         = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El NOMBRE del RECINTO CARCELARIO ya fue registrado.";
                        }
                    }
                    else
                    {
                        $consulta1 = RecintoCarcelario::where('nombre', '=', $data1['nombre'])
                            ->where('Muni_id', '=', $data1['Muni_id'])
                            ->where('id', '<>', $id)
                            ->count();

                        if($consulta1 < 1)
                        {
                            $iu               = RecintoCarcelario::find($id);
                            $iu->Muni_id      = $data1['Muni_id'];
                            $iu->estado       = $data1['estado'];
                            $iu->tipo_recinto = $data1['tipo_recinto'];
                            $iu->nombre       = $data1['nombre'];

                            $iu->save();

                            $respuesta['respuesta'] .= "El RECINTO CARCELARIO se edito con éxito.";
                            $respuesta['sw']         = 1;
                            $respuesta['iu']         = 2;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El NOMBRE del RECINTO CARCELARIO ya fue registrado.";
                        }
                    }
                return json_encode($respuesta);
                break;

            // === SELECT2 DEPARTAMENTO, MUNICIPIO  ===
            case '101':
                if($request->has('q'))
                {
                    $nombre     = $request->input('q');
                    $estado     = trim($request->input('estado'));
                    $page_limit = trim($request->input('page_limit'));
                    // ->select(DB::raw("Muni.id, UPPER(CONVERT(CAST(CONCAT_WS(', ', Dep.Dep, Muni.Muni) AS BINARY) USING utf8)) AS text"))
                    $query = Muni::leftJoin("Dep", "Dep.id", "=", "Muni.Dep")
                                ->whereRaw("CONCAT_WS(', ', Dep.Dep, Muni.Muni) LIKE '%$nombre%'")
                                ->select(DB::raw("Muni.id, UPPER(CONCAT_WS(', ', Dep.Dep, Muni.Muni)) AS text"))
                                ->orderByRaw("Dep.Dep ASC, Muni.Muni ASC")
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
        }
    }

    public function reportes(Request $request)
    {
        $tipo = $request->input('tipo');

        switch($tipo)
        {
            case '10':
                // === SEGURIDAD ===
                    $this->rol_id   = Auth::user()->rol_id;
                    $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                                        ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                                        ->select("seg_permisos.codigo")
                                        ->get()
                                        ->toArray();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();

                // === PERMISOS ===
                    if(!in_array(['codigo' => '2104'], $this->permisos))
                    {
                        return "No tiene permiso para GENERAR REPORTES.";
                    }

                //=== CARGAR VARIABLES ===

                //=== CONSULTA BASE DE DATOS ===
                    //=== CONSULTA 1 ===
                        $tabla1 = "RecintosCarcelarios";
                        $tabla2 = "Muni";
                        $tabla3 = "Dep";

                        $select = "
                            $tabla1.id,
                            $tabla1.Muni_id,
                            $tabla1.estado,
                            $tabla1.tipo_recinto,
                            $tabla1.nombre,
                            $tabla1.created_at,
                            $tabla1.updated_at,

                            a2.Dep AS departamento_id,
                            UPPER(a2.Muni) AS municipio,

                            UPPER(a3.Dep) AS departamento
                        ";

                        $array_where = "TRUE";

                        set_time_limit(3600);
                        ini_set('memory_limit','-1');

                        $consulta1 = RecintoCarcelario::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.Muni_id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.Dep")
                            ->whereRaw($array_where)
                            ->select(DB::raw($select))
                            ->orderByRaw("a3.Dep ASC, a2.Muni ASC, $tabla1.nombre ASC")
                            ->get()
                            ->toArray();

                //=== EXCEL ===
                    if(count($consulta1) > 0)
                    {
                        Excel::create('recintos_carcelarios_' . date('Y-m-d_H-i-s'), function($excel) use($consulta1){
                            $excel->sheet('RECINTOS CARCELARIOS', function($sheet) use($consulta1){
                                $sheet->row(1, [
                                    'ESTADO',

                                    'TIPO DE RECINTO',
                                    'NOMBRE DEL RECINTO CARCELARIO',
                                    'MUNICIPIO',
                                    'DEPARTAMENTO'
                                ]);

                                $sheet->row(1, function($row){
                                    $row->setBackground('#CCCCCC');
                                    $row->setFontWeight('bold');
                                    $row->setAlignment('center');
                                });

                                $sheet->freezeFirstRow();
                                $sheet->setAutoFilter();

                                // $sheet->setColumnFormat([
                                //     'A' => 'yyyy-mm-dd hh:mm:ss'
                                // ]);

                                $sw = FALSE;
                                $c  = 1;

                                foreach($consulta1 as $index1 => $row1)
                                {
                                    $sheet->row($c+1, [
                                        $this->estado[$row1["estado"]],

                                        $this->tipo_recinto[$row1["tipo_recinto"]],
                                        $row1["nombre"],
                                        $row1["municipio"],
                                        $row1["departamento"]
                                    ]);
                                    $c++;

                                    if($sw)
                                    {
                                        $sheet->row($c, function($row){
                                            $row->setBackground('#deeaf6');
                                        });

                                        $sw = FALSE;
                                    }
                                    else
                                    {
                                        $sw = TRUE;
                                    }
                                }

                                // $sheet->cells('A2:A' . ($c), function($cells){
                                //     $cells->setAlignment('right');
                                // });

                                $sheet->cells('A1:B' . ($c), function($cells){
                                    $cells->setAlignment('center');
                                });

                                $sheet->cells('C2:E' . ($c), function($cells){
                                    $cells->setAlignment('left');
                                });

                                $sheet->setAutoSize(true);
                            });

                            $excel->setActiveSheetIndex(0);
                        })->export('xlsx');
                    }
                    else
                    {
                        return "No se encontraron resultados.";
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
                switch($valor['valor'])
                {
                    case '1':
                        $respuesta = '<span class="label label-primary font-sm">' . $this->estado[$valor['valor']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->estado[$valor['valor']] . '</span>';
                        return($respuesta);
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