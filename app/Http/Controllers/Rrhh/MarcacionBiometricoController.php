<?php

namespace App\Http\Controllers\Rrhh;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;

use App\Models\Seguridad\SegPermisoRol;
use App\Models\Seguridad\SegLdUser;
use App\Models\Institucion\InstLugarDependencia;

use App\Models\Rrhh\RrhhPersona;
use App\Models\Rrhh\RrhhLogMarcacion;
use App\Models\Rrhh\RrhhLogMarcacionBackup;
use App\Models\Rrhh\RrhhBiometrico;
use App\Models\Rrhh\RrhhPersonaBiometrico;
use App\User;

use Maatwebsite\Excel\Facades\Excel;

class MarcacionBiometricoController extends Controller
{
    private $rol_id;
    private $permisos;

    private $estado;
    private $tipo_marcacion;

    public function __construct()
    {
        $this->middleware('auth');

        $this->estado = [
            '1' => 'SIN USO',
            '2' => 'USADO'
        ];

        $this->tipo_marcacion = [
            '1' => 'POR RED MEDIANTE CRON',
            '2' => 'POR RED PULSANDO BOTON',
            '3' => 'DESDE ARCHIVO SUBIDO',
            '4' => 'POR DIGITAL PERSONA',
            '5' => 'DESDE BASE DE DATOS'
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

        if(in_array(['codigo' => '1801'], $this->permisos))
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
                'title'                   => 'Marcación de los Biometricos',
                'home'                    => 'Inicio',
                'sistema'                 => 'Biometricos',
                'modulo'                  => 'Marcación de los Biometricos',
                'title_table'             => 'Marcación de los Biometricos',
                'estado_array'            => $this->estado,
                'tipo_marcacion_array'    => $this->tipo_marcacion,
                'lugar_dependencia_array' => InstLugarDependencia::whereRaw($array_where)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray()
            ];

            return view('rrhh.marcacion_biometrico.marcacion_biometrico')->with($data);
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
                $where_concatenar = "";
                if($request->has('fecha'))
                {
                    $where_concatenar = " AND f_marcacion::date = date '" . $request->input('fecha') . "'";
                }

                $jqgrid = new JqgridClass($request);

                $tabla1 = "rrhh_log_marcaciones_backup";
                $tabla2 = "rrhh_biometricos";
                $tabla3 = "inst_unidades_desconcentradas";
                $tabla4 = "inst_lugares_dependencia";

                $select = "
                    $tabla1.id,
                    $tabla1.biometrico_id,
                    $tabla1.tipo_marcacion,
                    $tabla1.n_documento_biometrico,
                    $tabla1.f_marcacion,
                    $tabla1.estado,

                    a2.unidad_desconcentrada_id,
                    a2.codigo_af,
                    a2.ip,

                    a3.lugar_dependencia_id,
                    a3.nombre AS unidad_desconcentrada,

                    a4.nombre AS lugar_dependencia
                ";

                $array_where = "TRUE" . $where_concatenar;

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
                            $array_where_1 .= " AND (a3.lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
                            $c_1_sw        = FALSE;
                        }
                        else
                        {
                            $array_where_1 .= " OR a3.lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
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
                    $array_where .= " AND a3.lugar_dependencia_id=0 AND ";
                }

                $array_where .= $jqgrid->getWhere();

                $count = RrhhLogMarcacionBackup::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.biometrico_id")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.unidad_desconcentrada_id")
                    ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = RrhhLogMarcacionBackup::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.biometrico_id")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.unidad_desconcentrada_id")
                    ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
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
                        'biometrico_id'            => $row["biometrico_id"],
                        'tipo_marcacion'           => $row["tipo_marcacion"],
                        'estado'                   => $row["estado"],
                        'unidad_desconcentrada_id' => $row["unidad_desconcentrada_id"],
                        'lugar_dependencia_id'     => $row["lugar_dependencia_id"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',
                        $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                        $this->tipo_marcacion[$row["tipo_marcacion"]],
                        // $this->utilitarios(array('tipo' => '2', 'tipo_marcacion' => $row["tipo_marcacion"])),

                        $row["f_marcacion"],
                        $row["n_documento_biometrico"],

                        "MP-" . $row["codigo_af"],
                        $row["ip"],
                        $row["unidad_desconcentrada"],
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
            // === OBTENER ASISTENCIA ===
            case '1':
                // === SEGURIDAD ===
                    $this->rol_id   = Auth::user()->rol_id;
                    $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                                        ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                                        ->select("seg_permisos.codigo")
                                        ->get()
                                        ->toArray();

                // === INICIALIZACION DE VARIABLES ===
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>ALERTA</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo
                    );

                // === PERMISOS ===
                    $persona_id = trim($request->input('persona_id'));
                    if(!in_array(['codigo' => '1802'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para OBTENER ASISTENCIAS.";
                        return json_encode($respuesta);
                    }

                // === VALORES DEL POST ===
                    $persona_id = trim($request->input('persona_id'));
                    $fecha_del  = trim($request->input('fecha_del'));
                    $fecha_al   = trim($request->input('fecha_al'));

                // === OPERACION ===
                    $consulta1 = RrhhPersona::where('id', '=', $persona_id)
                                    ->select("n_documento")
                                    ->first();

                    if(count($consulta1) > 0)
                    {
                        $where_concatenar = "";
                        $where_concatenar .= "n_documento_biometrico=" . $consulta1['n_documento'];
                        $where_concatenar .= " AND f_marcacion::date >= date '" . $fecha_del . "'";
                        $where_concatenar .= " AND f_marcacion::date <= date '" . $fecha_al . "'";

                        $consulta2 = RrhhLogMarcacionBackup::whereRaw($where_concatenar)
                            ->select("id", "f_marcacion", "biometrico_id")
                            ->orderBy('f_marcacion', 'asc')
                            ->get()
                            ->toArray();

                        if(count($consulta2) > 0)
                        {
                            $cantidad = 0;
                            foreach($consulta2 as $row2)
                            {
                                $consulta3 = RrhhLogMarcacion::where("persona_id", "=", $persona_id)
                                    ->where("f_marcacion", "=", $row2['f_marcacion'])
                                    ->select('id')
                                    ->first();

                                if(count($consulta3) < 1)
                                {
                                    $iu                         = new RrhhLogMarcacion;
                                    $iu->biometrico_id          = $row2['biometrico_id'];
                                    $iu->persona_id             = $persona_id;
                                    $iu->tipo_marcacion         = 5;
                                    $iu->n_documento_biometrico = $consulta1['n_documento'];
                                    $iu->f_marcacion            = $row2['f_marcacion'];
                                    $iu->save();

                                    $iu_2         = RrhhLogMarcacionBackup::find($row2['id']);
                                    $iu_2->estado = 2;
                                    $iu_2->save();

                                    $cantidad++;
                                }
                            }

                            if($cantidad == 0)
                            {
                                $respuesta['respuesta'] .= "No se logró registrar ninguna asistencia de la base de datos.";
                            }
                            else
                            {
                                $respuesta['respuesta'] .= "Se registro " . $cantidad . " asistencia(s) de la base de datos.";
                                $respuesta['sw']        = 1;
                            }
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "La persona no tiene maraciones en el rango de fecha.";
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "La persona no se encuentra registrado.";
                    }

                //=== RESPUESTA ===
                    return json_encode($respuesta);
                break;

            // === SELECT2 PERSONA ===
            case '100':
                if($request->has('q'))
                {
                    $nombre     = $request->input('q');
                    $estado     = trim($request->input('estado'));
                    $page_limit = trim($request->input('page_limit'));

                    $query = RrhhPersona::whereRaw("CONCAT_WS(' - ', n_documento, CONCAT_WS(' ', ap_paterno, ap_materno, nombre)) ilike '%$nombre%'")
                                ->where("estado", "=", $estado)
                                ->select(DB::raw("id, CONCAT_WS(' - ', n_documento, CONCAT_WS(' ', ap_paterno, ap_materno, nombre)) AS text"))
                                ->orderByRaw("CONCAT_WS(' ', ap_paterno, ap_materno, nombre) ASC")
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
                    $data1 = array();

                // === PERMISOS ===
                    if(!in_array(['codigo' => '1803'], $this->permisos))
                    {
                        return "No tiene permiso para GENERAR REPORTES.";
                    }

                // === ANALISIS DE LAS VARIABLES ===
                    if( ! (($request->has('fecha_del') && $request->has('fecha_al') && $request->has('persona_id'))))
                    {
                        return "La FECHA DEL, FECHA AL y la PERSONA son obligatorios.";
                    }

                //=== CARGAR VARIABLES ===
                    $data1['fecha_del']  = trim($request->input('fecha_del'));
                    $data1['fecha_al']   = trim($request->input('fecha_al'));
                    $data1['persona_id'] = trim($request->input('persona_id'));

                //=== CONSULTA BASE DE DATOS ===
                    $consulta1 = RrhhPersona::where('id', '=', $data1['persona_id'])
                                    ->select("n_documento", "ap_paterno", "ap_materno", "nombre")
                                    ->first();
                    if(count($consulta1) > 0)
                    {
                        $nombre_persona = trim($consulta1["ap_paterno"] . " " . $consulta1["ap_materno"]) . " " . trim($consulta1["nombre"]);

                        $tabla1 = "rrhh_log_marcaciones_backup";
                        $tabla2 = "rrhh_biometricos";
                        $tabla3 = "inst_unidades_desconcentradas";
                        $tabla4 = "inst_lugares_dependencia";

                        $where_concatenar = "TRUE ";
                        if($request->has('fecha_del'))
                        {
                            $where_concatenar .= " AND f_marcacion::date >= date '" . $request->input('fecha_del') . "'";
                        }

                        if($request->has('fecha_al'))
                        {
                            $where_concatenar .= " AND f_marcacion::date <= date '" . $request->input('fecha_al') . "'";
                        }

                        if($request->has('persona_id'))
                        {
                            $where_concatenar .= " AND $tabla1.n_documento_biometrico=" . $consulta1['n_documento'];
                        }

                        $select = "
                            $tabla1.id,
                            $tabla1.biometrico_id,
                            $tabla1.tipo_marcacion,
                            $tabla1.n_documento_biometrico,
                            $tabla1.f_marcacion,
                            $tabla1.estado,

                            a2.unidad_desconcentrada_id,
                            a2.codigo_af,
                            a2.ip,

                            a3.lugar_dependencia_id,
                            a3.nombre AS unidad_desconcentrada,

                            a4.nombre AS lugar_dependencia
                        ";

                        $consulta2 = RrhhLogMarcacionBackup::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.biometrico_id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.unidad_desconcentrada_id")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                            ->whereRaw($where_concatenar)
                            ->select(DB::raw($select))
                            ->orderByRaw("$tabla1.n_documento_biometrico ASC")
                            ->get()
                            ->toArray();

                        //=== EXCEL ===
                            if(count($consulta1) > 0)
                            {
                                set_time_limit(3600);
                                ini_set('memory_limit','-1');
                                Excel::create('marcaciones_' . date('Y-m-d_H-i-s'), function($excel) use($consulta2, $nombre_persona){
                                    $excel->sheet('Marcaciones', function($sheet) use($consulta2, $nombre_persona){
                                        $sheet->row(1, [
                                            'No',
                                            'ESTADO',
                                            'TIPO DE MARCACION',
                                            'FECHA DE MARCACION',
                                            'C.I.',
                                            'PERSONA',

                                            'CODIGO AF',
                                            'IP',
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

                                        $sw = FALSE;
                                        $c  = 1;

                                        foreach($consulta2 as $index2 => $row2)
                                        {
                                            $sheet->row($c+1, [
                                                $c++,
                                                $this->estado[$row2["estado"]],
                                                $this->tipo_marcacion[$row2["tipo_marcacion"]],
                                                $row2["f_marcacion"],
                                                $row2["n_documento_biometrico"],
                                                $nombre_persona,

                                                'MP-' . $row2["codigo_af"],
                                                $row2["ip"],
                                                $row2["unidad_desconcentrada"],
                                                $row2["lugar_dependencia"]
                                            ]);

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

                                        $sheet->cells('A1:J' . ($c), function($cells){
                                            $cells->setAlignment('center');
                                        });

                                        $sheet->setAutoSize(true);
                                    });
                                })->export('xlsx');
                            }
                            else
                            {
                                return "No se encontraron resultados.";
                            }
                    }
                    else
                    {
                        return "La PERSONA no esta registrada.";
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
                        $respuesta = '<span class="label label-success font-sm">' . $this->estado[$valor['estado']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
                        return($respuesta);
                        break;
                }
                break;

            case '100':
                PDF::Image(
                    $valor['file'],     // file: nombre del archivo
                    $valor['x'],        // x: abscisa de la esquina superior izquierda LTR, esquina superior derecha RTL
                    $valor['y'],        // y: ordenada de la esquina superior izquierda LTR, esquina superior derecha RTL
                    $valor['w'],        // w: ancho de la imagen, 0=se calcula automaticamente
                    $valor['h'],        // w: altura de la imagen, 0=se calcula automaticamente
                    $valor['type'],     // type: formato de la imagen, JPEG, PNG, GIF  y otros. Si no se especifica, el tipo se infiere de la extensión del archivo
                    $valor['link'],     // link: URL o enlace
                    $valor['align'],    // align: indica la alineacion del puntero junto a la insercion de imagenes en relacion de su altura. T=parte superior derecha LTR o de arriba a la izquierda para RTL, M=de mediana adecuado para LTR o media izquierda para RTL, B=para inferior derecha de LTR o de abajo hacia la izquierda para RTL, N=linea siguiente
                    $valor['resize'],   // resize: TRUE=reduce al tamaño de x-y, FALSE=no reduce nada
                    $valor['dpi'],      // dpi: puntos por pulgada de resolucion utilizado en redimensionamiento
                    $valor['palign'],   // palign: permite centra y alinear. L=alinear a la izquierda, C=centro, R=Alinear a la derecha, ''=cadena vacia, LTR o RTL
                    $valor['ismask'],   // ismask: TRUE=es mascara, FALSE=no es mascara
                    $valor['imgsmask'], // imgsmask: imagen objeto, FALSE=contrario
                    $valor['border'],   // border: borde de la celda 0,1 o L=Left, T=Top, R= Rigth, B=Bottom
                    $valor['fitbox'],   // fitbox: borde de la celda 0,1 o L=Left, T=Top, R= Rigth, B=Bottom
                    $valor['hidden'],   // hidden: TRUE=no muestra la imagen, FALSE=muestra la imagen
                    $valor['fitonpage'] // fitonpage: TRUE=la imagen se redimensiona para no exceder las dimensiones de la pagina, FALSE=no pasa nada
                );
                break;
            case '101':
                PDF::Rect(
                    $valor['x'],        // x: abscisa de la esquina superior izquierda LTR, esquina superior derecha RTL
                    $valor['y'],        // y: ordenada de la esquina superior izquierda LTR, esquina superior derecha RTL
                    $valor['w'],        // w: ancho
                    $valor['h'],        // w: altura
                    $valor['style'],    // Estilo de renderizado Los valores posibles son:
                                        // D o cadena vacía: Dibujar (predeterminado).
                                        // F: llenar.
                                        // DF o FD: Dibujar y llenar.
                                        // CNZ: modo de recorte (usando la regla par impar para determinar qué regiones se encuentran dentro del trazado de recorte).
                                        // CEO: modo de recorte (utilizando la regla del número de devanado distinto de cero para determinar qué regiones se encuentran dentro del trazado de recorte)
                    $valor['border_style'], // Estilo del borde del rectángulo Arreglar como para SetLineStyle . Valor predeterminado: estilo de línea predeterminado (matriz vacía).
                    $valor['fill_color'] // Color de relleno. Formato: matriz (GRIS) o matriz (R, G, B) o matriz (C, M, Y, K). Valor predeterminado: color predeterminado (matriz vacía).
                );
                break;
            case '102':
                PDF::Line(
                    $valor['x1'],   // x1: Abscisa del primer punto.
                    $valor['y1'],   // y1: Ordenado del primer punto.
                    $valor['x2'],   // x2: Abscisa del segundo punto.
                    $valor['y2'],   // y2: Ordenado del segundo punto
                    $valor['style'] // Estilo de línea Arreglar como para SetLineStyle. Valor predeterminado: estilo de línea predeterminado (matriz vacía).
                );
                break;
            case '110':
                PDF::Write(
                    $valor['h'],        // Altura de la línea
                    $valor['txt'],      // Cadena para mostrar
                    $valor['link'],     // URL o identificador devuelto por AddLink()
                    $valor['fill'],     // Indica si el fondo debe estar pintado (1) o transparente (0). Valor predeterminado: 0.
                    $valor['align'],    // Permite centrar o alinear el texto. Los valores posibles son:
                                        // L o cadena vacía: alineación izquierda (valor predeterminado)
                                        // C: centro
                                        // R: alinear a la derecha
                                        // J: justificar
                    $valor['ln'],       // Si es verdadero, coloque el cursor en la parte inferior de la línea; de lo contrario, coloque el cursor en la parte superior de la línea. Si no se especifica, el tipo se infiere de la extensión del archivo
                    $valor['stretch'],  // estirar el modo los caracteres:
                                        // 0 = deshabilitado
                                        // 1 = escala horizontal solo si es necesario
                                        // 2 = escala horizontal forzada
                                        // 3 = espaciado de caracteres solo si es necesario
                                        // 4 = espaciado de caracteres forzado
                    $valor['firstline'],// Si es verdadero imprime solo la primera línea y devuelve la cadena restante.
                    $valor['firstblock'],// Si es verdadero, la cadena es el comienzo de una línea.
                    $valor['maxh']      // Altura máxima. El texto restante no impreso será devuelto. Debe se > = $ h y menos espacio restante en la parte inferior de la página, o 0 para desactivar esta función.
                );
                break;
            case '111':
                PDF::MultiCell(
                    $valor['x1'],       // Ancho celda
                    $valor['y1'],       // Alto celda
                    $valor['txt'],      // Texto a mostrar
                    $valor['border'],   // Border: 0,1 o L=Left, T=Top, R= Rigth, B=Bottom
                    $valor['align'],    // Align: L=Left, C=Center, R=Rigth, J=Justification
                    $valor['fill'],     // Relleno: TRUE, FALSE
                    $valor['ln'],       // Posicion: 0=a la derecha, 1=a la siguiente linea, 2=a continuacion
                    "",                 // X: Posición en unidades de usuario
                    "",                 // Y: Posición en unidades de usuario
                    true,               // reseth: restablece la altura de la ultima celda
                    $valor['stretch'],  // stretch: estiramiento de la fuente, 0=desactivado, 1=horizontal-ancho de la celda, 2=obligatorio horizontal-ancho de la celda, 3= espacio-ancho de la celda, 4=obligatorio espacio-ancho de la celda
                    $valor['ishtml'],   // ishtml: TRUE=texto HTML, FALSE=texto plano
                    true,               // autopadding: TRUE=ajuste interno automatico, FALSE=ajuste manual
                    $valor['y1'],       // maxh: Altura maxima, 0 si ishtml=TRUE.
                    $valor['valign'],   // valign: Alineación del texto T=Top, M=Middle, B=Bottom, si ishtml=TRUE no funciona
                    $valor['fitcell']   // fitcell: TRUE=intenta encajar en la celda. FALSE=desactivado, si ishtml=TRUE no funciona
                );
                break;
            case '112':
                PDF::write2DBarcode(
                    $valor['code'], // Código para imprimir
                    $valor['type'], // Tipo de código de barras
                    $valor['x'],    // x posición
                    $valor['y'],    // y posición
                    $valor['w'],    // Ancho
                    $valor['h'],    // Altura
                    $valor['style'],// conjunto de opciones:
                    $valor['align'],// Indica la alineación del puntero al lado de la inserción del código de barras con respecto a la altura del código de barras. El valor puede ser:
                                    // T: arriba a la derecha para LTR o arriba a la izquierda para RTL
                                    // M: medio-derecha para LTR o middle-left para RTL
                                    // B: abajo a la derecha para LTR o abajo a la izquierda para RTL
                                    // N: siguiente línea
                    $valor['distort']   // FALSE
                );
                break;
            default:
                break;
        }
    }
}