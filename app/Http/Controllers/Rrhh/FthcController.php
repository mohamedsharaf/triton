<?php

namespace App\Http\Controllers\Rrhh;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;
use App\Libraries\UtilClass;

use App\Models\Seguridad\SegPermisoRol;
use App\Models\Seguridad\SegLdUser;
use App\Models\Institucion\InstLugarDependencia;
use App\Models\Institucion\InstUnidadDesconcentrada;
use App\Models\Rrhh\RrhhHorario;
use App\Models\Rrhh\RrhhFthc;

use Maatwebsite\Excel\Facades\Excel;

use Exception;

class FthcController extends Controller
{
    private $estado;
    private $fthc;
    private $tipo_horario;
    private $sexo;

    private $rol_id;
    private $permisos;

    private $reporte_1;
    private $reporte_data_1;

    public function __construct()
    {
        $this->middleware('auth');

        $this->estado = [
            '1' => 'HABILITADO',
            '2' => 'INHABILITADO'
        ];

        $this->fthc = [
            '1' => 'FERIADO',
            '2' => 'TOLERANCIA',
            '3' => 'HORARIO CONTINUO'
        ];

        $this->tipo_horario = [
            '1' => 'MAÑANA',
            '2' => 'TARDE'
        ];

        $this->sexo = [
            'F' => 'FEMENINO',
            'M' => 'MASCULINO'
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

        if(in_array(['codigo' => '1501'], $this->permisos))
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
                'title'                   => 'Feriado, tolerancia y horario continuo',
                'home'                    => 'Inicio',
                'sistema'                 => 'Recursos humanos',
                'modulo'                  => 'Feriados, tolerancias y horario continuos',
                'title_table'             => 'Feriados, tolerancias y horario continuos',
                'estado_array'            => $this->estado,
                'fthc_array'              => $this->fthc,
                'tipo_horario_array'      => $this->tipo_horario,
                'sexo_array'              => $this->sexo,
                'lugar_dependencia_array' => InstLugarDependencia::whereRaw($array_where)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray()
            ];
            return view('rrhh.fthc.fthc')->with($data);
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

                $tabla1 = "rrhh_fthc";
                $tabla2 = "inst_lugares_dependencia";
                $tabla3 = "inst_unidades_desconcentradas";
                $tabla4 = "rrhh_horarios";

                $select = "
                    $tabla1.id,
                    $tabla1.lugar_dependencia_id,
                    $tabla1.unidad_desconcentrada_id,
                    $tabla1.horario_id,

                    $tabla1.estado,
                    $tabla1.fecha,
                    $tabla1.nombre,
                    $tabla1.tipo_fthc,
                    $tabla1.tipo_horario,
                    $tabla1.sexo,

                    a2.nombre AS lugar_dependencia,

                    a3.nombre AS unidad_desconcentrada,

                    a4.nombre AS horario
                ";

                $array_where = "TRUE";

                $user_id = Auth::user()->id;
                $rol_id  = Auth::user()->rol_id;

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
                        if(($valor['lugar_dependencia_id'] == '1') && ($rol_id == '1' || $rol_id == '5'))
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

                $count = RrhhFthc::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.lugar_dependencia_id")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.unidad_desconcentrada_id")
                    ->leftJoin("$tabla4 AS a4", "a4.id", "=", "$tabla1.horario_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = RrhhFthc::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.lugar_dependencia_id")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.unidad_desconcentrada_id")
                    ->leftJoin("$tabla4 AS a4", "a4.id", "=", "$tabla1.horario_id")
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
                        'lugar_dependencia_id'     => $row["lugar_dependencia_id"],
                        'unidad_desconcentrada_id' => $row["unidad_desconcentrada_id"],
                        'horario_id'               => $row["horario_id"],
                        'estado'                   => $row["estado"],
                        'tipo_fthc'                => $row["tipo_fthc"],
                        'tipo_horario'             => $row["tipo_horario"],
                        'sexo'                     => $row["sexo"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',

                        $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                        $row["fecha"],
                        $row["nombre"],

                        ($row["tipo_fthc"] == '')? '' : $this->fthc[$row["tipo_fthc"]],
                        $row["lugar_dependencia"],
                        $row["unidad_desconcentrada"],

                        $row["horario"],

                        ($row["tipo_horario"] == '')? '' : $this->tipo_horario[$row["tipo_horario"]],
                        ($row["sexo"] == '')? '' : $this->sexo[$row["sexo"]],

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
                        'titulo'     => '<div class="text-center"><strong>FERIADO, TOLERANCIA Y HORARIO CONTINUO</strong></div>',
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
                        if(!in_array(['codigo' => '1503'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '1502'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                // === VALIDATE ===
                    try
                    {
                        $validator = $this->validate($request,[
                            'fecha'                => 'required|date',
                            'nombre'               => 'required|max:500',
                            'lugar_dependencia_id' => 'required'
                        ],
                        [
                            'fecha.required' => 'El campo FECHA es obligatorio.',
                            'fecha.max'     => 'El campo FECHA no corresponde con una fecha válida.',

                            'nombre.required' => 'El campo NOMBRE es obligatorio.',
                            'nombre.max'      => 'El campo NOMBRE debe contener :max caracteres como máximo.',

                            'lugar_dependencia_id.required' => 'El campo LUGAR DE DEPENDENCIA es obligatorio.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['estado']    = trim($request->input('estado'));
                    $data1['fecha']     = trim($request->input('fecha'));
                    $data1['nombre']    = strtoupper($util->getNoAcentoNoComilla(trim($request->input('nombre'))));
                    $data1['tipo_fthc'] = trim($request->input('tipo_fthc'));

                    $data1['lugar_dependencia_id']     = trim($request->input('lugar_dependencia_id'));
                    $data1['unidad_desconcentrada_id'] = trim($request->input('unidad_desconcentrada_id'));

                    $data1['horario_id'] = trim($request->input('horario_id'));

                    $data1['tipo_horario'] = trim($request->input('tipo_horario'));
                    $data1['sexo']         = trim($request->input('sexo'));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === VALIDAR POR CAMPO ===
                    switch($data1['tipo_fthc'])
                    {
                        case '1':
                            $data1['horario_id'] = NULL;

                            $data1['tipo_horario'] = NULL;
                            $data1['sexo']         = NULL;
                            break;
                        case '2':
                            $data1['horario_id'] = NULL;
                            break;
                        case '3':
                            $data1['tipo_horario'] = NULL;
                            $data1['sexo']         = NULL;

                            if($data1['horario_id'] == '')
                            {
                                $respuesta['respuesta'] .= "El campo HORARIO es obligatorio.";
                                return json_encode($respuesta);
                            }
                            break;
                        default:
                            $data1['horario_id'] = NULL;

                            $data1['tipo_horario'] = NULL;
                            $data1['sexo']         = NULL;
                            break;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $consulta1 = RrhhFthc::where('nombre', '=', $data1['nombre'])
                            ->where('lugar_dependencia_id', '=', $data1['lugar_dependencia_id'])
                            ->count();

                        if($consulta1 < 1)
                        {
                            $iu            = new RrhhFthc;
                            $iu->estado    = $data1['estado'];
                            $iu->fecha     = $data1['fecha'];
                            $iu->nombre    = $data1['nombre'];
                            $iu->tipo_fthc = $data1['tipo_fthc'];

                            $iu->lugar_dependencia_id     = $data1['lugar_dependencia_id'];
                            $iu->unidad_desconcentrada_id = $data1['unidad_desconcentrada_id'];

                            $iu->horario_id = $data1['horario_id'];

                            $iu->tipo_horario = $data1['tipo_horario'];
                            $iu->sexo         = $data1['sexo'];

                            $iu->save();

                            $respuesta['respuesta'] .= "El FERIADO o TOLERANCIA o HORARIO CONTINUO fue registrado con éxito.";
                            $respuesta['sw']         = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El NOMBRE del FERIADO o TOLERANCIA o HORARIO CONTINUO ya fue registrado.";
                        }
                    }
                    else
                    {
                        $consulta1 = RrhhFthc::where('nombre', '=', $data1['nombre'])
                            ->where('lugar_dependencia_id', '=', $data1['lugar_dependencia_id'])
                            ->where('id', '<>', $id)
                            ->count();

                        if($consulta1 < 1)
                        {
                            $iu            = RrhhFthc::find($id);
                            $iu->estado    = $data1['estado'];
                            $iu->fecha     = $data1['fecha'];
                            $iu->nombre    = $data1['nombre'];
                            $iu->tipo_fthc = $data1['tipo_fthc'];

                            $iu->lugar_dependencia_id     = $data1['lugar_dependencia_id'];
                            $iu->unidad_desconcentrada_id = $data1['unidad_desconcentrada_id'];

                            $iu->horario_id = $data1['horario_id'];

                            $iu->tipo_horario = $data1['tipo_horario'];
                            $iu->sexo         = $data1['sexo'];

                            $iu->save();

                            $respuesta['respuesta'] .= "El FERIADO o TOLERANCIA o HORARIO CONTINUO se edito con éxito.";
                            $respuesta['sw']         = 1;
                            $respuesta['iu']         = 2;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El NOMBRE del FERIADO o TOLERANCIA o HORARIO CONTINUO ya fue registrado.";
                        }
                    }
                return json_encode($respuesta);
                break;

            // === SELECT2 UNIDAD DESCONCENTRADA ===
            case '103':
                $respuesta = [
                    'tipo' => $tipo,
                    'sw'   => 1
                ];
                if($request->has('lugar_dependencia_id'))
                {
                    $lugar_dependencia_id = $request->input('lugar_dependencia_id');
                    $query = InstUnidadDesconcentrada::where("lugar_dependencia_id", "=", $lugar_dependencia_id)
                                ->select('id', 'nombre')
                                ->get()
                                ->toArray();

                    $horario_1 = RrhhHorario::where("lugar_dependencia_id", "=", $lugar_dependencia_id)
                        ->where("tipo_horario", "=", '4')
                        ->select('id', 'nombre')
                        ->get()
                        ->toArray();
                    if(count($query) > 0)
                    {
                        $respuesta['consulta'] = $query;
                        $respuesta['sw']       = 2;

                        $respuesta['sw_horario_1'] = 1;
                        if(count($horario_1) > 0)
                        {
                            $respuesta['horario_1']    = $horario_1;
                            $respuesta['sw_horario_1'] = 2;
                        }
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
                if($request->has('persona_id'))
                {
                    $this->reporte_data_1 = [
                        'persona_id'               => trim($request->input('persona_id')),
                        'f_marcacion_del'          => trim($request->input('f_marcacion_del')),
                        'f_marcacion_al'           => trim($request->input('f_marcacion_al')),
                        'lugar_dependencia_id'     => trim($request->input('lugar_dependencia_id')),
                        'unidad_desconcentrada_id' => trim($request->input('unidad_desconcentrada_id'))
                    ];
                    Excel::create('Marcaciones_' . date('Y-m-d_H-i-s'), function($excel){
                        $tabla1 = "rrhh_log_marcaciones";
                        $tabla2 = "rrhh_biometricos";
                        $tabla3 = "inst_unidades_desconcentradas";
                        $tabla4 = "inst_lugares_dependencia";

                        $select = "
                            $tabla1.id,
                            $tabla1.biometrico_id,
                            $tabla1.persona_id,
                            $tabla1.f_marcacion,

                            a2.unidad_desconcentrada_id,
                            a2.codigo_af,
                            a2.ip,

                            a3.lugar_dependencia_id,
                            a3.nombre AS unidad_desconcentrada,

                            a4.nombre AS lugar_dependencia
                        ";

                        $array_where = "$tabla1.persona_id=" . $this->reporte_data_1['persona_id'] . "";

                        if($this->reporte_data_1['f_marcacion_del'] != '')
                        {
                            $array_where .= " AND $tabla1.f_marcacion >= '" . $this->reporte_data_1['f_marcacion_del'] . "'";
                        }

                        if($this->reporte_data_1['f_marcacion_al'] != '')
                        {
                            $array_where .= " AND $tabla1.f_marcacion <= '" . $this->reporte_data_1['f_marcacion_al'] . " 23:59:59'";
                        }

                        if($this->reporte_data_1['lugar_dependencia_id'] != '')
                        {
                            $array_where .= " AND a3.lugar_dependencia_id = " . $this->reporte_data_1['lugar_dependencia_id'] . "";
                        }

                        if($this->reporte_data_1['unidad_desconcentrada_id'] != '')
                        {
                            $array_where .= " AND a2.unidad_desconcentrada_id = " . $this->reporte_data_1['unidad_desconcentrada_id'] . "";
                        }

                        $this->reporte_1 = RrhhLogMarcacion::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.biometrico_id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.unidad_desconcentrada_id")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                            ->whereRaw($array_where)
                            ->select(DB::raw($select))
                            ->orderBy("$tabla1.f_marcacion", 'ASC')
                            ->get()
                            ->toArray();

                        $excel->sheet('Marcaciones', function($sheet){
                            $sheet->row(1, [
                                'FECHA Y HORA',
                                'BIOMETRICO',
                                'UNIDAD DESCONCENTRADA',
                                'LUGAR DE DEPENDENCIA'
                            ]);

                            $sheet->row(1, function($row){
                                $row->setBackground('#CCCCCC');
                                $row->setFontWeight('bold');
                                $row->setAlignment('center');
                            });

                            $sheet->freezeFirstRow();
                            $sheet->setAutoFilter();

                            $sheet->setColumnFormat([
                                'A' => 'yyyy-mm-dd hh:mm:ss'
                            ]);

                            $sw = FALSE;

                            foreach($this->reporte_1 as $index => $row1)
                            {
                                $sheet->row($index+2, [
                                    $row1["f_marcacion"],
                                    "MP-" . $row1["codigo_af"],
                                    $row1["unidad_desconcentrada"],
                                    $row1["lugar_dependencia"]
                                ]);

                                if($sw)
                                {
                                    $sheet->row($index+2, function($row){
                                        $row->setBackground('#deeaf6');
                                        // $row->setFontColor('#9c0006');
                                    });

                                    $sw = FALSE;
                                }
                                else
                                {
                                    $sw = TRUE;
                                }
                            }

                            $sheet->cells('B1:D' . ($index + 2), function($cells){
                                $cells->setAlignment('center');
                            });

                            // $sheet->cells('A1:A' . ($index + 2), function($cells){
                            //     $cells->setAlignment('center');
                            // });

                            $sheet->setAutoSize(true);
                        });

                        // $excel->sheet('Cargos', function($sheet){
                        //     $sheet->row(1, [
                        //         'LUGAR DE DEPENDENCIA',
                        //         'AREA UNIDAD ORGANIZACIONAL',
                        //         '¿ACEFALO?',
                        //         'TIPO DE CARGO',
                        //         'NUMERO',
                        //         'CARGO'
                        //     ]);

                        //     $sheet->row(1, function($row){
                        //         $row->setBackground('#CCCCCC');
                        //         $row->setFontWeight('bold');
                        //         $row->setAlignment('center');
                        //     });

                        //     $sheet->freezeFirstRow();
                        //     $sheet->setAutoFilter();

                        //     foreach($this->reporte_1 as $index => $row1)
                        //     {
                        //         $sheet->row($index+2, [
                        //             $row1["lugar_dependencia"],
                        //             $row1["auo"],
                        //             $this->acefalia[$row1["acefalia"]],
                        //             $row1["tipo_cargo"],
                        //             $row1["item_contrato"],
                        //             $row1["nombre"]
                        //         ]);

                        //         if($row1["acefalia"] == 1)
                        //         {
                        //             $sheet->row($index+2, function($row){
                        //                 $row->setBackground('#ffc7ce');
                        //                 $row->setFontColor('#9c0006');
                        //             });
                        //         }
                        //     }

                        //     $sheet->cells('C1:D' . ($index + 2), function($cells){
                        //         $cells->setAlignment('center');
                        //     });

                        //     $sheet->setAutoSize(true);
                        // });
                    })->export('xlsx');
                }
                else
                {
                    return "SIN FUNCIONARIO";
                }
                break;
            default:
                break;
        }
    }
}