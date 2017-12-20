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
use App\Models\Rrhh\RrhhHorario;

use Maatwebsite\Excel\Facades\Excel;

use Exception;

class HorarioController extends Controller
{
    private $estado;
    private $defecto;
    private $tipo_horario;
    private $dias;

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

        $this->defecto = [
            '1' => 'NO',
            '2' => 'SI'
        ];

        $this->tipo_horario = [
            '1' => 'MAÑANA',
            '2' => 'TARDE',
            '3' => 'NOCHE'
        ];

        $this->dias = [
            '1' => 'NO',
            '2' => 'SI'
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

        if(in_array(['codigo' => '1401'], $this->permisos))
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
                'title'                   => 'Horarios',
                'home'                    => 'Inicio',
                'sistema'                 => 'Recursos humanos',
                'modulo'                  => 'Horarios',
                'title_table'             => 'Horarios',
                'estado_array'            => $this->estado,
                'defecto_array'           => $this->defecto,
                'tipo_horario_array'      => $this->tipo_horario,
                'dias_array'              => $this->dias,
                'lugar_dependencia_array' => InstLugarDependencia::whereRaw($array_where)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray()
            ];
            return view('rrhh.horario.horario')->with($data);
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

                $tabla1 = "rrhh_horarios";
                $tabla2 = "inst_lugares_dependencia";

                $select = "
                    $tabla1.id,
                    $tabla1.lugar_dependencia_id,
                    $tabla1.estado,
                    $tabla1.defecto,
                    $tabla1.tipo_horario,
                    $tabla1.nombre,
                    $tabla1.h_ingreso,
                    $tabla1.h_salida,
                    $tabla1.tolerancia,
                    $tabla1.marcacion_ingreso_del,
                    $tabla1.marcacion_ingreso_al,
                    $tabla1.marcacion_salida_del,
                    $tabla1.marcacion_salida_al,
                    $tabla1.lunes,
                    $tabla1.martes,
                    $tabla1.miercoles,
                    $tabla1.jueves,
                    $tabla1.viernes,
                    $tabla1.sabado,
                    $tabla1.domingo,

                    a2.nombre AS lugar_dependencia
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

                $count = RrhhHorario::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.lugar_dependencia_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = RrhhHorario::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.lugar_dependencia_id")
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
                        'estado'               => $row["estado"],
                        'defecto'              => $row["defecto"],
                        'tipo_horario'         => $row["tipo_horario"],
                        'lunes'                => $row["lunes"],
                        'martes'               => $row["martes"],
                        'miercoles'            => $row["miercoles"],
                        'jueves'               => $row["jueves"],
                        'viernes'              => $row["viernes"],
                        'sabado'               => $row["sabado"],
                        'domingo'              => $row["domingo"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',

                        $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                        ($row["defecto"] == '')? '' : $this->defecto[$row["defecto"]],
                        ($row["tipo_horario"] == '')? '' : $this->tipo_horario[$row["tipo_horario"]],
                        $row["nombre"],

                        $row["h_ingreso"],
                        $row["h_salida"],
                        $row["tolerancia"],

                        $row["marcacion_ingreso_del"],
                        $row["marcacion_ingreso_al"],
                        $row["marcacion_salida_del"],
                        $row["marcacion_salida_al"],

                        $this->utilitarios(array('tipo' => '2', 'dias' => $row["lunes"])),
                        $this->utilitarios(array('tipo' => '2', 'dias' => $row["martes"])),
                        $this->utilitarios(array('tipo' => '2', 'dias' => $row["miercoles"])),
                        $this->utilitarios(array('tipo' => '2', 'dias' => $row["jueves"])),
                        $this->utilitarios(array('tipo' => '2', 'dias' => $row["viernes"])),
                        $this->utilitarios(array('tipo' => '2', 'dias' => $row["sabado"])),
                        $this->utilitarios(array('tipo' => '2', 'dias' => $row["domingo"])),

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
                        'titulo'     => '<div class="text-center"><strong>Horario</strong></div>',
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
                        if(!in_array(['codigo' => '1403'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '1402'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                // === VALIDATE ===
                    try
                    {
                        $validator = $this->validate($request,[
                            'lugar_dependencia_id'  => 'required',
                            'nombre'                => 'required|max:500',
                            'h_ingreso'             => 'required',
                            'h_salida'              => 'required',
                            'tolerancia'            => 'required|min:0',
                            'marcacion_ingreso_del' => 'required',
                            'marcacion_ingreso_al'  => 'required',
                            'marcacion_salida_del'  => 'required',
                            'marcacion_salida_al'   => 'required'
                        ],
                        [
                            'lugar_dependencia_id.required' => 'El campo LUGAR DE DEPENDENCIA es obligatorio.',

                            'nombre.required' => 'El campo NOMBRE es obligatorio.',
                            'nombre.max'     => 'El campo NOMBRE debe contener :max caracteres como máximo.',

                            'h_ingreso.required' => 'El campo HORA DE INGRESO es obligatorio.',

                            'h_salida.required' => 'El campo HORA DE SALIDA es obligatorio.',

                            'tolerancia.required' => 'El campo TOLERANCIA es obligatorio.',
                            'tolerancia.min'      => 'El campo TOLERANCIA debe tener al menos :min.',

                            'marcacion_ingreso_del.required' => 'El campo MARCACION DE INGRESO DEL es obligatorio.',

                            'marcacion_ingreso_al.required' => 'El campo MARCACION DE INGRESO AL es obligatorio.',

                            'marcacion_salida_del.required' => 'El campo MARCACION DE SALIDA DEL es obligatorio.',

                            'marcacion_salida_al.required' => 'El campo MARCACION DE SALIDA AL es obligatorio.'
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
                    $data1['defecto']              = trim($request->input('defecto'));
                    $data1['tipo_horario']         = trim($request->input('tipo_horario'));
                    $data1['lugar_dependencia_id'] = trim($request->input('lugar_dependencia_id'));
                    $data1['nombre']               = strtoupper($util->getNoAcentoNoComilla(trim($request->input('nombre'))));

                    $data1['h_ingreso']  = trim($request->input('h_ingreso'));
                    $data1['h_salida']   = trim($request->input('h_salida'));
                    $data1['tolerancia'] = trim($request->input('tolerancia'));

                    $data1['marcacion_ingreso_del'] = trim($request->input('marcacion_ingreso_del'));
                    $data1['marcacion_ingreso_al']  = trim($request->input('marcacion_ingreso_al'));
                    $data1['marcacion_salida_del']  = trim($request->input('marcacion_salida_del'));
                    $data1['marcacion_salida_al']   = trim($request->input('marcacion_salida_al'));

                    $data1['lunes']     = trim($request->input('lunes', 1));
                    $data1['martes']    = trim($request->input('martes', 1));
                    $data1['miercoles'] = trim($request->input('miercoles', 1));
                    $data1['jueves']    = trim($request->input('jueves', 1));
                    $data1['viernes']   = trim($request->input('viernes', 1));
                    $data1['sabado']    = trim($request->input('sabado', 1));
                    $data1['domingo']   = trim($request->input('domingo', 1));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === VALIDAR POR CAMPO ===
                    $sw_dias = TRUE;

                    if($data1['lunes'] == '2')
                    {
                        $sw_dias = FALSE;
                    }
                    if($data1['martes'] == '2')
                    {
                        $sw_dias = FALSE;
                    }
                    if($data1['miercoles'] == '2')
                    {
                        $sw_dias = FALSE;
                    }
                    if($data1['jueves'] == '2')
                    {
                        $sw_dias = FALSE;
                    }
                    if($data1['viernes'] == '2')
                    {
                        $sw_dias = FALSE;
                    }
                    if($data1['sabado'] == '2')
                    {
                        $sw_dias = FALSE;
                    }
                    if($data1['domingo'] == '2')
                    {
                        $sw_dias = FALSE;
                    }

                    if($sw_dias)
                    {
                        $respuesta['respuesta'] .= "¡Por lo menos seleccione un día!";
                        return json_encode($respuesta);
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                        $consulta1 = RrhhHorario::where('nombre', '=', $data1['nombre'])
                            ->where('lugar_dependencia_id', '=', $data1['lugar_dependencia_id'])
                            ->count();

                        if($consulta1 < 1)
                        {
                            $iu                       = new RrhhHorario;
                            $iu->estado               = $data1['estado'];
                            $iu->defecto              = $data1['defecto'];
                            $iu->tipo_horario         = $data1['tipo_horario'];
                            $iu->lugar_dependencia_id = $data1['lugar_dependencia_id'];
                            $iu->nombre               = $data1['nombre'];

                            $iu->h_ingreso  = $data1['h_ingreso'];
                            $iu->h_salida   = $data1['h_salida'];
                            $iu->tolerancia = $data1['tolerancia'];

                            $iu->marcacion_ingreso_del = $data1['marcacion_ingreso_del'];
                            $iu->marcacion_ingreso_al  = $data1['marcacion_ingreso_al'];
                            $iu->marcacion_salida_del  = $data1['marcacion_salida_del'];
                            $iu->marcacion_salida_al   = $data1['marcacion_salida_al'];

                            $iu->lunes     = $data1['lunes'];
                            $iu->martes    = $data1['martes'];
                            $iu->miercoles = $data1['miercoles'];
                            $iu->jueves    = $data1['jueves'];
                            $iu->viernes   = $data1['viernes'];
                            $iu->sabado    = $data1['sabado'];
                            $iu->domingo   = $data1['domingo'];

                            $iu->save();

                            $respuesta['respuesta'] .= "El HORARIO fue registrado con éxito.";
                            $respuesta['sw']         = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El NOMBRE del horario ya fue registrado.";
                        }
                    }
                    else
                    {
                        $consulta1 = RrhhHorario::where('nombre', '=', $data1['nombre'])
                            ->where('lugar_dependencia_id', '=', $data1['lugar_dependencia_id'])
                            ->where('id', '<>', $id)
                            ->count();

                        if($consulta1 < 1)
                        {
                            $iu                       = RrhhHorario::find($id);
                            $iu->estado               = $data1['estado'];
                            $iu->defecto              = $data1['defecto'];
                            $iu->tipo_horario         = $data1['tipo_horario'];
                            $iu->lugar_dependencia_id = $data1['lugar_dependencia_id'];
                            $iu->nombre               = $data1['nombre'];

                            $iu->h_ingreso  = $data1['h_ingreso'];
                            $iu->h_salida   = $data1['h_salida'];
                            $iu->tolerancia = $data1['tolerancia'];

                            $iu->marcacion_ingreso_del = $data1['marcacion_ingreso_del'];
                            $iu->marcacion_ingreso_al  = $data1['marcacion_ingreso_al'];
                            $iu->marcacion_salida_del  = $data1['marcacion_salida_del'];
                            $iu->marcacion_salida_al   = $data1['marcacion_salida_al'];

                            $iu->lunes     = $data1['lunes'];
                            $iu->martes    = $data1['martes'];
                            $iu->miercoles = $data1['miercoles'];
                            $iu->jueves    = $data1['jueves'];
                            $iu->viernes   = $data1['viernes'];
                            $iu->sabado    = $data1['sabado'];
                            $iu->domingo   = $data1['domingo'];

                            $iu->save();

                            $respuesta['respuesta'] .= "El HORARIO se edito con éxito.";
                            $respuesta['sw']         = 1;
                            $respuesta['iu']         = 2;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El NOMBRE del horario ya fue registrado.";
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
                switch($valor['dias'])
                {
                    case '1':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->dias[$valor['dias']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-primary font-sm">' . $this->dias[$valor['dias']] . '</span>';
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