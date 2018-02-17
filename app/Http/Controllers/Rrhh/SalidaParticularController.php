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
use App\Models\Institucion\InstAuo;
use App\Models\Institucion\InstTipoCargo;
use App\Models\Institucion\InstCargo;

use App\Models\Rrhh\RrhhPersona;
use App\Models\Rrhh\RrhhFuncionario;
use App\Models\Rrhh\RrhhFthc;
use App\Models\Rrhh\RrhhHorario;
use App\Models\Rrhh\RrhhTipoSalida;
use App\Models\Rrhh\RrhhSalida;

use Maatwebsite\Excel\Facades\Excel;
use PDF;

use Exception;

class SalidaParticularController extends Controller
{
    private $estado;
    private $tipo_salida;
    private $con_sin_retorno;
    private $periodo;
    private $no_si;
    private $public_dir;

    private $rol_id;
    private $permisos;

    private $reporte_1;
    private $reporte_data_1;

    public function __construct()
    {
        $this->middleware('auth');

        $this->estado = [
            '1' => 'HABILITADA',
            '2' => 'ANULADA',
            '3' => 'CERRADA'
        ];

        $this->tipo_salida = [
            '1' => 'LICENCIA OFICIAL',
            '2' => 'LICENCIA PARTICULAR',
            '3' => 'VACACIONES',
            '4' => 'CUMPLEAÑOS',
            '5' => 'LICENCIA SIN GOCE DE HABER'
        ];

        $this->con_sin_retorno = [
            '1' => 'CON RETORNO',
            '2' => 'SIN RETORNO'
        ];

        $this->periodo = [
            '1' => 'MAÑANA',
            '2' => 'TARDE'
        ];

        $this->no_si = [
            '1' => 'NO',
            '2' => 'SI'
        ];

        $this->sp_estado = [
            '1' => 'NO MARCADO',
            '2' => 'REGULARIZADA'
        ];

        $this->public_dir = '/image/logo';
        $this->public_url = 'storage/rrhh/salidas/solicitud_salida/';
    }

    public function index()
    {
        $this->rol_id   = Auth::user()->rol_id;
        $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
            ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
            ->select("seg_permisos.codigo")
            ->get()
            ->toArray();

        if(in_array(['codigo' => '1601'], $this->permisos))
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
                        $c_1_sw        = FALSE;
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
                'title'                   => 'Control de salida particular',
                'home'                    => 'Inicio',
                'sistema'                 => 'Recursos humanos',
                'modulo'                  => 'Control de salida particular',
                'title_table'             => 'Control de salida particular',
                'public_url'              => $this->public_url,
                'estado_array'            => $this->estado,
                'tipo_salida_array'       => $this->tipo_salida,
                'con_sin_retorno_array'   => $this->con_sin_retorno,
                'periodo_array'           => $this->periodo,
                'no_si_array'             => $this->no_si,
                'sp_estado_array'         => $this->sp_estado,
                'lugar_dependencia_array' => InstLugarDependencia::whereRaw($array_where)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray()
            ];
            return view('rrhh.salida_particular.salida_particular')->with($data);
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

                $tabla1 = "rrhh_salidas";
                $tabla2 = "rrhh_personas";
                $tabla3 = "inst_unidades_desconcentradas";
                $tabla4 = "inst_lugares_dependencia";

                $select = "
                    $tabla1.id,
                    $tabla1.persona_id,
                    $tabla1.tipo_salida_id,
                    $tabla1.persona_id_superior,
                    $tabla1.persona_id_rrhh,

                    $tabla1.estado,
                    $tabla1.codigo,
                    $tabla1.destino,
                    $tabla1.motivo,
                    $tabla1.f_salida,
                    $tabla1.f_retorno,
                    $tabla1.h_salida,
                    $tabla1.h_retorno,

                    $tabla1.n_horas,
                    $tabla1.con_sin_retorno,

                    $tabla1.validar_superior,
                    $tabla1.f_validar_superior,

                    $tabla1.validar_rrhh,
                    $tabla1.f_validar_rrhh,

                    $tabla1.pdf,
                    $tabla1.papeleta_pdf,

                    a2.n_documento,
                    a2.nombre AS nombre_persona,
                    a2.ap_paterno,
                    a2.ap_materno,

                    a3.lugar_dependencia_id AS lugar_dependencia_id_funcionario,
                    a3.nombre AS ud_funcionario,

                    a4.nombre AS lugar_dependencia_funcionario
                ";

                $array_where = "a2.tipo_cronograma=1";

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

                $count = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.persona_id")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a6.unidad_desconcentrada_id")
                    ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a7.lugar_dependencia_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.tipo_salida_id")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.persona_id")
                    ->leftJoin("$tabla3 AS a4", "a4.id", "=", "$tabla1.persona_id_superior")
                    ->leftJoin("$tabla3 AS a5", "a5.id", "=", "$tabla1.persona_id_rrhh")
                    ->leftJoin("$tabla4 AS a6", "a3.id", "=", "a6.persona_id")
                    ->leftJoin("$tabla5 AS a7", "a7.id", "=", "a6.unidad_desconcentrada_id")
                    ->leftJoin("$tabla6 AS a8", "a8.id", "=", "a7.lugar_dependencia_id")
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
                        'persona_id'          => $row["persona_id"],
                        'tipo_salida_id'      => $row["tipo_salida_id"],
                        'persona_id_superior' => $row["persona_id_superior"],
                        'persona_id_rrhh'     => $row["persona_id_rrhh"],
                        'estado'              => $row["estado"],
                        'n_horas'             => $row["n_horas"],
                        'con_sin_retorno'     => $row["con_sin_retorno"],
                        'validar_superior'    => $row["validar_superior"],
                        'f_validar_superior'  => $row["f_validar_superior"],
                        'validar_rrhh'        => $row["validar_rrhh"],
                        'f_validar_rrhh'      => $row["f_validar_rrhh"],
                        'pdf'                 => $row["pdf"],
                        'papeleta_pdf'        => $row["papeleta_pdf"],
                        'tipo_cronograma'     => $row["tipo_cronograma"],
                        'tipo_salida'         => $row["tipo_salida"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',

                        $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                        $this->utilitarios(array('tipo' => '2', 'validar_superior' => $row["validar_superior"])),
                        $this->utilitarios(array('tipo' => '3', 'validar_rrhh' => $row["validar_rrhh"])),
                        $this->utilitarios(array('tipo' => '4', 'pdf' => $row["pdf"], 'id' => $row["id"], 'dia_hora' => 1)),

                        $row["papeleta_salida"],
                        ($row["tipo_salida"] == '')? '' : $this->tipo_salida[$row["tipo_salida"]],
                        $row["codigo"],

                        $row["n_documento"],
                        $row["nombre_persona"],
                        $row["ap_paterno"],
                        $row["ap_materno"],

                        $row["destino"],
                        $row["motivo"],

                        $row["f_salida"],
                        $row["h_salida"],
                        $row["h_retorno"],
                        ($row["con_sin_retorno"] == '')? '' : $this->con_sin_retorno[$row["con_sin_retorno"]],

                        $row["f_validar_superior"],
                        $row["n_documento_superior"],
                        $row["nombre_superior"],
                        $row["ap_paterno_superior"],
                        $row["ap_materno_superior"],

                        $row["f_validar_rrhh"],
                        $row["n_documento_rrhh"],
                        $row["nombre_rrhh"],
                        $row["ap_paterno_rrhh"],
                        $row["ap_materno_rrhh"],

                        $row["ud_funcionario"],
                        $row["lugar_dependencia_funcionario"],

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
            // === VALIDAR / INVALIDAR PAPELETA DE SALIDA ===
            case '1':
                // === SEGURIDAD ===
                    $this->rol_id   = Auth::user()->rol_id;
                    $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                                        ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                                        ->select("seg_permisos.codigo")
                                        ->get()
                                        ->toArray();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>Papeleta de Salida</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );
                    $opcion   = 'n';
                    $f_actual = date('Y-m-d H:i:s');

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if($id != '')
                    {
                        $opcion = 'e';
                        if(!in_array(['codigo' => '1203'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '1202'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                //=== OPERACION ===
                    $data1['validar_rrhh']   = trim($request->input('validar_rrhh'));
                    $data1['f_validar_rrhh'] = $f_actual;
                    $data1['dia_hora']       = trim($request->input('dia_hora'));
                    $persona_id              = Auth::user()->persona_id;

                // === MODIFICAR VALORES ===
                    if($persona_id == '')
                    {
                        $respuesta['respuesta'] .= "Usted no esta registrado en PERSONAS.";
                        return json_encode($respuesta);
                    }

                    $consulta1 = RrhhSalida::where('id', '=', $id)
                        ->where('validar_rrhh', '=', $data1['validar_rrhh'])
                        ->first();

                    if(!(count($consulta1) > 0))
                    {
                        $consulta2 = RrhhSalida::where('id', '=', $id)
                            ->first();

                        if($consulta2['estado'] == '1')
                        {
                            if($consulta2['validar_superior'] != '2')
                            {
                                $respuesta['respuesta'] .= "La PAPELETA DE SALIDA debe de ser validado por el INMEDIATO SUPERIOR.";
                                return json_encode($respuesta);
                            }

                            $iu                  = RrhhSalida::find($id);
                            $iu->validar_rrhh    = $data1['validar_rrhh'];
                            $iu->f_validar_rrhh  = $data1['f_validar_rrhh'];
                            $iu->persona_id_rrhh = $persona_id ;

                            $iu->save();

                            if($data1['validar_rrhh'] == '2')
                            {
                                $respuesta['respuesta'] .= "La PAPELETA DE SALIDA fue VALIDADO.";
                            }
                            else
                            {
                                $respuesta['respuesta'] .= "La PAPELETA DE SALIDA fue INVALIDADO.";
                            }
                            $respuesta['sw'] = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "La PAPELETA DE SALIDA fue ANULADA.";
                        }
                    }
                    else
                    {
                        if($data1['validar_rrhh'] == '2')
                        {
                            $respuesta['respuesta'] .= "La PAPELETA DE SALIDA ya fue VALIDADO.";
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "La PAPELETA DE SALIDA ya fue INVALIDADA.";
                        }
                    }

                    $respuesta['dia_hora']  = $data1['dia_hora'];
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
                    case '3':
                        $respuesta = '<span class="label label-success font-sm">' . $this->estado[$valor['estado']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
                        return($respuesta);
                        break;
                }
                break;
            case '2':
                switch($valor['validar_superior'])
                {
                    case '1':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->no_si[$valor['validar_superior']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-primary font-sm">' . $this->no_si[$valor['validar_superior']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
                        return($respuesta);
                        break;
                }
                break;
            case '3':
                switch($valor['validar_rrhh'])
                {
                    case '1':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->no_si[$valor['validar_rrhh']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-primary font-sm">' . $this->no_si[$valor['validar_rrhh']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
                        return($respuesta);
                        break;
                }
                break;
            case '4':
                switch($valor['pdf'])
                {
                    case '1':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->no_si[$valor['pdf']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<button class="btn btn-xs btn-primary" onclick="utilitarios([21, ' . $valor['id'] . ', ' . $valor['dia_hora'] . ']);" title="Clic ver el documento">
                            <i class="fa fa-cloud-download"></i>
                            <strong>' . $this->no_si[$valor['pdf']] . '</strong>
                        </button>';
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

    public function reportes(Request $request)
    {
        $tipo = $request->input('tipo');

        switch($tipo)
        {
            case '1':
                if($request->has('salida_id'))
                {
                    $salida_id = trim($request->input('salida_id'));

                    $fh_actual            = date("Y-m-d H-i-s");
                    $dir_logo_institucion = public_path($this->public_dir) . '/' . 'logo_fge_256.png';
                    $dir_logo_pais        = public_path($this->public_dir) . '/' . 'escudo_logo_300.png';
                    $dir_marca_agua       = public_path($this->public_dir) . '/' . 'marca_agua_500.png';

                    // === VALIDAR IMAGENES ===
                        if( ! file_exists($dir_logo_institucion))
                        {
                            return "No existe el logo de la institución " . $dir_logo_institucion;
                        }

                        if( ! file_exists($dir_logo_pais))
                        {
                            return "No existe el logo deL pais " . $dir_logo_pais;
                        }

                        if( ! file_exists($dir_marca_agua))
                        {
                            return "No existe la marca de agua " . $dir_marca_agua;
                        }

                    // === CONSULTA A LA BASE DE DATOS ===
                        $consulta1 = RrhhSalida::where('id', '=', $salida_id)
                            ->first();

                        if( ! (count($consulta1) > 0))
                        {
                            return "No existe la PAPELETA DE SALIDA";
                        }

                        if($consulta1['estado'] == '2')
                        {
                            return "La de PAPELETA DE SALIDA fue ANULADA";
                        }

                        $tabla1 = "rrhh_personas";
                        $tabla2 = "rrhh_funcionarios";

                        $tabla3 = "inst_unidades_desconcentradas";
                        $tabla4 = "inst_lugares_dependencia";

                        $tabla5 = "inst_cargos";
                        $tabla6 = "inst_tipos_cargo";
                        $tabla7 = "inst_auos";

                        $select = "
                            $tabla1.id,
                            $tabla1.n_documento,
                            $tabla1.nombre AS nombre_persona,
                            $tabla1.ap_paterno,
                            $tabla1.ap_materno,
                            $tabla1.sexo,
                            $tabla1.f_nacimiento,

                            a2.id AS funcionario_id,
                            a2.cargo_id,
                            a2.unidad_desconcentrada_id,
                            a2.horario_id_1,
                            a2.horario_id_2,
                            a2.situacion,
                            a2.documento_sw,
                            a2.f_ingreso,
                            a2.f_salida,
                            a2.sueldo,
                            a2.observaciones,
                            a2.documento_file,

                            a3.lugar_dependencia_id AS lugar_dependencia_id_funcionario,
                            a3.nombre AS ud_funcionario,

                            a4.nombre AS lugar_dependencia_funcionario,

                            a5.auo_id,
                            a5.tipo_cargo_id,
                            a5.item_contrato,
                            a5.acefalia,
                            a5.nombre AS cargo,

                            a6.nombre AS tipo_cargo,

                            a7.lugar_dependencia_id AS lugar_dependencia_id_cargo,
                            a7.nombre AS auo_cargo
                        ";

                        $consulta2 = RrhhPersona::leftJoin("$tabla2 AS a2", "a2.persona_id", "=", "$tabla1.id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.unidad_desconcentrada_id")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                            ->leftJoin("$tabla5 AS a5", "a5.id", "=", "a2.cargo_id")
                            ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.tipo_cargo_id")
                            ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a5.auo_id")
                            ->where("$tabla1.id", '=', $consulta1['persona_id'])
                            ->select(DB::raw($select))
                            ->first();

                        if( ! (count($consulta2) > 0))
                        {
                            return "No existe la PERSONA.";
                        }

                        $consulta3 = RrhhPersona::leftJoin("$tabla2 AS a2", "a2.persona_id", "=", "$tabla1.id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.unidad_desconcentrada_id")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                            ->leftJoin("$tabla5 AS a5", "a5.id", "=", "a2.cargo_id")
                            ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.tipo_cargo_id")
                            ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a5.auo_id")
                            ->where("$tabla1.id", '=', $consulta1['persona_id_superior'])
                            ->select(DB::raw($select))
                            ->first();

                        if( ! (count($consulta3) > 0))
                        {
                            return "No existe la INMEDIATO SUPERIOR.";
                        }

                        $persona_id = Auth::user()->persona_id;

                        $consulta4 = RrhhPersona::leftJoin("$tabla2 AS a2", "a2.persona_id", "=", "$tabla1.id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.unidad_desconcentrada_id")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.lugar_dependencia_id")
                            ->leftJoin("$tabla5 AS a5", "a5.id", "=", "a2.cargo_id")
                            ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.tipo_cargo_id")
                            ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a5.auo_id")
                            ->where("$tabla1.id", '=', $persona_id)
                            ->select(DB::raw($select))
                            ->first();

                        if( ! (count($consulta4) > 0))
                        {
                            return "Usted no es funcionario del MINISTERIO PUBLICO.";
                        }

                        $consulta5 = RrhhTipoSalida::where("id", "=", $consulta1['tipo_salida_id'])
                            ->select("id", "nombre", "tipo_salida", "tipo_cronograma", "hd_mes")
                            ->first();

                    // === CARGAR VALORES ===
                        $data1 = array(
                            'salida_id' => $salida_id,
                        );

                        $data2 = array(
                            'dir_logo_institucion' => $dir_logo_institucion,
                            'dir_logo_pais'        => $dir_logo_pais,
                            'dir_marca_agua'       => $dir_marca_agua,
                            'consulta4'            => $consulta4,
                            'consulta5'            => $consulta5
                        );

                        $data3 = array(
                            'consulta1' => $consulta1,
                            'consulta2' => $consulta2
                        );

                        $style_qrcode = array(
                            'border'        => 0,
                            'vpadding'      => 'auto',
                            'hpadding'      => 'auto',
                            'fgcolor'       => array(0, 0, 0),
                            'bgcolor'       => false, //array(255,255,255)
                            'module_width'  => 1, // width of a single module in points
                            'module_height' => 1 // height of a single module in points
                        );

                    set_time_limit(3600);
                    ini_set('memory_limit','-1');

                    // == HEADER ==
                        PDF::setHeaderCallback(function($pdf) use($data2){
                            $this->utilitarios(array(
                                'tipo'         => '101',
                                'x'            => 7,
                                'y'            => 7,
                                'w'            => 202,
                                'h'            => 126,
                                'style'        => '',
                                'border_style' => array(),
                                'fill_color'   => array()
                            ));

                            $this->utilitarios(array(
                                'tipo'  => '102',
                                'x1'    => 7,
                                'y1'    => 33,
                                'x2'    => 209,
                                'y2'    => 33,
                                'style' => array()
                            ));

                            $this->utilitarios(array(
                                'tipo'      => '100',
                                'file'      => $data2['dir_logo_institucion'],
                                'x'         => 10,
                                'y'         => 10,
                                'w'         => 0,
                                'h'         => 20,
                                'type'      => 'PNG',
                                'link'      => '',
                                'align'     => '',
                                'resize'    => FALSE,
                                'dpi'       => 300,
                                'palign'    => '',
                                'ismask'    => FALSE,
                                'imgsmask'  => FALSE,
                                'border'    => 0,
                                'fitbox'    => FALSE,
                                'hidden'    => FALSE,
                                'fitonpage' => FALSE
                            ));

                            $this->utilitarios(array(
                                'tipo'      => '100',
                                'file'      => $data2['dir_logo_pais'],
                                'x'         => 171,
                                'y'         => 10,
                                'w'         => 0,
                                'h'         => 20,
                                'type'      => 'PNG',
                                'link'      => '',
                                'align'     => '',
                                'resize'    => FALSE,
                                'dpi'       => 300,
                                'palign'    => '',
                                'ismask'    => FALSE,
                                'imgsmask'  => FALSE,
                                'border'    => 0,
                                'fitbox'    => FALSE,
                                'hidden'    => FALSE,
                                'fitonpage' => FALSE
                            ));

                            $this->utilitarios(array(
                                'tipo'      => '100',
                                'file'      => $data2['dir_marca_agua'],
                                'x'         => 63,
                                'y'         => 39,
                                'w'         => 0,
                                'h'         => 90,
                                'type'      => '',
                                'link'      => '',
                                'align'     => '',
                                'resize'    => TRUE,
                                'dpi'       => 140,
                                'palign'    => '',
                                'ismask'    => FALSE,
                                'imgsmask'  => FALSE,
                                'border'    => 0,
                                'fitbox'    => FALSE,
                                'hidden'    => FALSE,
                                'fitonpage' => FALSE
                            ));

                            $pdf->Ln(10);
                            // $pdf->SetFont('times', 'B', 14);
                            // $this->utilitarios(array(
                            //     'tipo'       => '110',
                            //     'h'          => 0,
                            //     'txt'        => 'MINISTERIO PÚBLICO',
                            //     'link'       => '',
                            //     'fill'       => FALSE,
                            //     'align'      => 'C',
                            //     'ln'         => TRUE,
                            //     'stretch'    => 0,
                            //     'firstline'  => FALSE,
                            //     'firstblock' => FALSE,
                            //     'maxh'       => 0
                            // ));

                            $pdf->SetFont('times', 'B', 12);
                            $this->utilitarios(array(
                                'tipo'       => '110',
                                'h'          => 0,
                                'txt'        => $data2['consulta4']['lugar_dependencia_funcionario'],
                                'link'       => '',
                                'fill'       => FALSE,
                                'align'      => 'C',
                                'ln'         => TRUE,
                                'stretch'    => 0,
                                'firstline'  => FALSE,
                                'firstblock' => FALSE,
                                'maxh'       => 0
                            ));

                            $pdf->SetFont('times', '', 10);
                            $this->utilitarios(array(
                                'tipo'       => '110',
                                'h'          => 0,
                                'txt'        => $data2['consulta4']['ud_funcionario'],
                                'link'       => '',
                                'fill'       => FALSE,
                                'align'      => 'C',
                                'ln'         => TRUE,
                                'stretch'    => 0,
                                'firstline'  => FALSE,
                                'firstblock' => FALSE,
                                'maxh'       => 0
                            ));

                            $pdf->Ln(4);

                            $pdf->SetFont('times', 'B', 12);
                            $this->utilitarios(array(
                                'tipo'       => '110',
                                'h'          => 0,
                                'txt'        => $data2['consulta5']['nombre'],
                                'link'       => '',
                                'fill'       => FALSE,
                                'align'      => 'C',
                                'ln'         => TRUE,
                                'stretch'    => 0,
                                'firstline'  => FALSE,
                                'firstblock' => FALSE,
                                'maxh'       => 0
                            ));
                        });

                    // == FOOTER ==
                        PDF::setFooterCallback(function($pdf) use($data3){
                            $y_n = 139.7;
                            // == FIRMAS ==
                                $pdf->SetY(-(43.5 + $y_n));

                                if($data3['consulta2']['lugar_dependencia_id_funcionario'] == '6')
                                {
                                    $fill = FALSE;
                                    $x1   = 49;
                                    $x2   = 49;
                                    $y1   = 4;

                                    $pdf->SetFont('times', 'B', 6);

                                    $this->utilitarios(array(
                                        'tipo'    => '111',
                                        'x1'      => $x1,
                                        'y1'      => $y1,
                                        'txt'     => "SOLICITANTE",
                                        'border'  => 'LRTB',
                                        'align'   => 'C',
                                        'fill'    => $fill,
                                        'ln'      => 0,
                                        'stretch' => 0,
                                        'ishtml'  => FALSE,
                                        'fitcell' => FALSE,
                                        'valign'  => 'M'
                                    ));

                                    $this->utilitarios(array(
                                        'tipo'    => '111',
                                        'x1'      => $x1,
                                        'y1'      => $y1,
                                        'txt'     => "INMEDIATO SUPERIOR",
                                        'border'  => 'LRTB',
                                        'align'   => 'C',
                                        'fill'    => $fill,
                                        'ln'      => 0,
                                        'stretch' => 0,
                                        'ishtml'  => FALSE,
                                        'fitcell' => FALSE,
                                        'valign'  => 'M'
                                    ));

                                    $this->utilitarios(array(
                                        'tipo'    => '111',
                                        'x1'      => $x1,
                                        'y1'      => $y1,
                                        'txt'     => "AUTORIZADO POR FISCAL DEPARAMETAL",
                                        'border'  => 'LRTB',
                                        'align'   => 'C',
                                        'fill'    => $fill,
                                        'ln'      => 0,
                                        'stretch' => 0,
                                        'ishtml'  => FALSE,
                                        'fitcell' => FALSE,
                                        'valign'  => 'M'
                                    ));

                                    $this->utilitarios(array(
                                        'tipo'    => '111',
                                        'x1'      => $x2,
                                        'y1'      => $y1,
                                        'txt'     => "AUTORIZADO POR R.R.H.H.",
                                        'border'  => 'LRTB',
                                        'align'   => 'C',
                                        'fill'    => $fill,
                                        'ln'      => 0,
                                        'stretch' => 0,
                                        'ishtml'  => FALSE,
                                        'fitcell' => FALSE,
                                        'valign'  => 'M'
                                    ));

                                    $pdf->Ln();

                                    $fill = FALSE;
                                    $y1   = 29;

                                    $pdf->SetFont('times', 'B', 7);

                                    $this->utilitarios(array(
                                        'tipo'    => '111',
                                        'x1'      => $x1,
                                        'y1'      => $y1,
                                        'txt'     => "",
                                        'border'  => 'LRTB',
                                        'align'   => 'C',
                                        'fill'    => $fill,
                                        'ln'      => 0,
                                        'stretch' => 0,
                                        'ishtml'  => FALSE,
                                        'fitcell' => FALSE,
                                        'valign'  => 'M'
                                    ));

                                    $this->utilitarios(array(
                                        'tipo'    => '111',
                                        'x1'      => $x1,
                                        'y1'      => $y1,
                                        'txt'     => "",
                                        'border'  => 'LRTB',
                                        'align'   => 'C',
                                        'fill'    => $fill,
                                        'ln'      => 0,
                                        'stretch' => 0,
                                        'ishtml'  => FALSE,
                                        'fitcell' => FALSE,
                                        'valign'  => 'M'
                                    ));

                                    $this->utilitarios(array(
                                        'tipo'    => '111',
                                        'x1'      => $x1,
                                        'y1'      => $y1,
                                        'txt'     => "",
                                        'border'  => 'LRTB',
                                        'align'   => 'C',
                                        'fill'    => $fill,
                                        'ln'      => 0,
                                        'stretch' => 0,
                                        'ishtml'  => FALSE,
                                        'fitcell' => FALSE,
                                        'valign'  => 'M'
                                    ));

                                    $this->utilitarios(array(
                                        'tipo'    => '111',
                                        'x1'      => $x2,
                                        'y1'      => $y1,
                                        'txt'     => "",
                                        'border'  => 'LRTB',
                                        'align'   => 'C',
                                        'fill'    => $fill,
                                        'ln'      => 0,
                                        'stretch' => 0,
                                        'ishtml'  => FALSE,
                                        'fitcell' => FALSE,
                                        'valign'  => 'M'
                                    ));

                                    $pdf->Ln();
                                }
                                else
                                {
                                    $fill = FALSE;
                                    $x1   = 65;
                                    $x2   = 66;
                                    $y1   = 4;

                                    $pdf->SetFont('times', 'B', 7);

                                    $this->utilitarios(array(
                                        'tipo'    => '111',
                                        'x1'      => $x1,
                                        'y1'      => $y1,
                                        'txt'     => "SOLICITANTE",
                                        'border'  => 'LRTB',
                                        'align'   => 'C',
                                        'fill'    => $fill,
                                        'ln'      => 0,
                                        'stretch' => 0,
                                        'ishtml'  => FALSE,
                                        'fitcell' => FALSE,
                                        'valign'  => 'M'
                                    ));

                                    $this->utilitarios(array(
                                        'tipo'    => '111',
                                        'x1'      => $x1,
                                        'y1'      => $y1,
                                        'txt'     => "INMEDIATO SUPERIOR",
                                        'border'  => 'LRTB',
                                        'align'   => 'C',
                                        'fill'    => $fill,
                                        'ln'      => 0,
                                        'stretch' => 0,
                                        'ishtml'  => FALSE,
                                        'fitcell' => FALSE,
                                        'valign'  => 'M'
                                    ));

                                    $this->utilitarios(array(
                                        'tipo'    => '111',
                                        'x1'      => $x2,
                                        'y1'      => $y1,
                                        'txt'     => "AUTORIZADO POR R.R.H.H.",
                                        'border'  => 'LRTB',
                                        'align'   => 'C',
                                        'fill'    => $fill,
                                        'ln'      => 0,
                                        'stretch' => 0,
                                        'ishtml'  => FALSE,
                                        'fitcell' => FALSE,
                                        'valign'  => 'M'
                                    ));

                                    $pdf->Ln();

                                    $fill = FALSE;
                                    $y1   = 29;

                                    $pdf->SetFont('times', 'B', 7);

                                    $this->utilitarios(array(
                                        'tipo'    => '111',
                                        'x1'      => $x1,
                                        'y1'      => $y1,
                                        'txt'     => "",
                                        'border'  => 'LRTB',
                                        'align'   => 'C',
                                        'fill'    => $fill,
                                        'ln'      => 0,
                                        'stretch' => 0,
                                        'ishtml'  => FALSE,
                                        'fitcell' => FALSE,
                                        'valign'  => 'M'
                                    ));

                                    $this->utilitarios(array(
                                        'tipo'    => '111',
                                        'x1'      => $x1,
                                        'y1'      => $y1,
                                        'txt'     => "",
                                        'border'  => 'LRTB',
                                        'align'   => 'C',
                                        'fill'    => $fill,
                                        'ln'      => 0,
                                        'stretch' => 0,
                                        'ishtml'  => FALSE,
                                        'fitcell' => FALSE,
                                        'valign'  => 'M'
                                    ));

                                    $this->utilitarios(array(
                                        'tipo'    => '111',
                                        'x1'      => $x2,
                                        'y1'      => $y1,
                                        'txt'     => "",
                                        'border'  => 'LRTB',
                                        'align'   => 'C',
                                        'fill'    => $fill,
                                        'ln'      => 0,
                                        'stretch' => 0,
                                        'ishtml'  => FALSE,
                                        'fitcell' => FALSE,
                                        'valign'  => 'M'
                                    ));

                                    $pdf->Ln();
                                }

                            // == LEYENDA ==
                                $pdf->SetY(-(10.5 + $y_n));

                                $fill = FALSE;
                                $x1   = 98;
                                $y1   = 4;

                                $pdf->SetFont('times', 'I', 7);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "NOTA: El presente formulario no debe tener borrones, enmiendas y/o correcciones.",
                                    'border'  => '',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Fecha de solicitud: " . date("d/m/Y H:i", strtotime($data3['consulta1']['created_at'])),
                                    'border'  => '',
                                    'align'   => 'R',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));
                        });

                    PDF::setPageUnit('mm');

                    PDF::SetMargins(10, 36, 10);
                    PDF::getAliasNbPages();
                    PDF::SetCreator('MINISTERIO PUBLICO');
                    PDF::SetAuthor('TRITON');
                    PDF::SetTitle('PAPELETA DE SALIDA');
                    PDF::SetSubject('DOCUMENTO');
                    PDF::SetKeywords('PAPELETA DE SALIDA');

                    // PDF::SetFontSubsetting(false);

                    PDF::SetAutoPageBreak(TRUE, 10);
                    // PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                    // === BODY ===
                        // PDF::AddPage('L', 'MEMO');
                        PDF::AddPage('P', 'LETTER');

                        // === FUNCIONARIO E INMEDIATO SUPERIOR ===
                            $fill = FALSE;
                            $tt2  = 10;
                            $x1   = 85;
                            $x2   = 26;
                            $y1   = 4;

                            PDF::SetFont('times', 'B', 8);

                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x1,
                                'y1'      => $y1,
                                'txt'     => "Nombre del funcionario:",
                                'border'  => 'LRT',
                                'align'   => 'L',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x1,
                                'y1'      => $y1,
                                'txt'     => "Inmediato superior:",
                                'border'  => 'LRT',
                                'align'   => 'L',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x2,
                                'y1'      => $y1,
                                'txt'     => "",
                                'border'  => 'LRT',
                                'align'   => 'L',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            PDF::Ln();

                            $fill = FALSE;
                            $x1   = 85;
                            $x2   = 26;
                            $y1   = 8;

                            PDF::SetFont('times', '', 9);

                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x1,
                                'y1'      => $y1,
                                'txt'     => $consulta2['n_documento'] . " - " . $consulta2['nombre_persona'] . " " . trim($consulta2['ap_paterno'] . " " . $consulta2['ap_materno']),
                                'border'  => 'LRB',
                                'align'   => 'C',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            $this->utilitarios(array(
                                'tipo'      => '111',
                                'x1'        => $x1,
                                'y1'        => $y1,
                                'txt'       => $consulta3['n_documento'] . " - " . $consulta3['nombre_persona'] . " " . trim($consulta3['ap_paterno'] . " " . $consulta3['ap_materno']),
                                'border'    => 'LRB',
                                'align'     => 'C',
                                'fill'      => $fill,
                                'ln'        => 0,
                                'stretch'   => 0,
                                'ishtml'    => FALSE,
                                'fitcell'   => FALSE,
                                'valign'    => 'M'
                            ));

                            $this->utilitarios(array(
                                'tipo'      => '111',
                                'x1'        => $x2,
                                'y1'        => $y1,
                                'txt'       => "",
                                'border'    => 'LR',
                                'align'     => 'C',
                                'fill'      => $fill,
                                'ln'        => 0,
                                'stretch'   => 0,
                                'ishtml'    => FALSE,
                                'fitcell'   => FALSE,
                                'valign'    => 'M'
                            ));

                            PDF::Ln();

                        if($consulta5['tipo_cronograma'] == '1')
                        {
                            // === FECHA DE SALIDA, HORA DE SALIDA, HORA DE RETORNO Y SU SALIDA ES CON ===
                                $fill = FALSE;
                                $tt2  = 10;
                                $x1   = 42.5;
                                $x2   = 26;
                                $y1   = 4;

                                PDF::SetFont('times', 'B', 8);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Fecha de salida:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Hora de salida:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Hora de retorno:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Su salida es:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x2,
                                    'y1'      => $y1,
                                    'txt'     => "",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                PDF::Ln();

                                $fill = FALSE;
                                $x1   = 42.5;
                                $x2   = 26;
                                $y1   = 8;

                                PDF::SetFont('times', '', 9);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => date("d/m/Y", strtotime($consulta1["f_salida"])),
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => date("H:i", strtotime($consulta1["h_salida"])),
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                if($consulta1["h_retorno"] == '')
                                {
                                    $h_retorno = '';
                                }
                                else
                                {
                                    $h_retorno = date("H:i", strtotime($consulta1["h_retorno"]));
                                }

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => $h_retorno,
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => ($consulta1["con_sin_retorno"] == '')? '' : $this->con_sin_retorno[$consulta1["con_sin_retorno"]],
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'      => '111',
                                    'x1'        => $x2,
                                    'y1'        => $y1,
                                    'txt'       => "",
                                    'border'    => 'LR',
                                    'align'     => 'C',
                                    'fill'      => $fill,
                                    'ln'        => 0,
                                    'stretch'   => 0,
                                    'ishtml'    => FALSE,
                                    'fitcell'   => FALSE,
                                    'valign'    => 'M'
                                ));

                                PDF::Ln();
                        }
                        else
                        {
                            // === FECHA DE SALIDA, PERIODO, FECHA DE RETORNO Y PERIODO ===
                                $fill = FALSE;
                                $tt2  = 10;
                                $x1   = 42.5;
                                $x2   = 26;
                                $y1   = 4;

                                PDF::SetFont('times', 'B', 8);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Fecha de salida:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Periodo:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Fecha de retorno:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Periodo:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x2,
                                    'y1'      => $y1,
                                    'txt'     => "",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                PDF::Ln();

                                $fill = FALSE;
                                $x1   = 42.5;
                                $x2   = 26;
                                $y1   = 8;

                                PDF::SetFont('times', '', 9);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => date("d/m/Y", strtotime($consulta1["f_salida"])),
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => ($consulta1["periodo_salida"] == '')? '' : $this->periodo[$consulta1["periodo_salida"]],
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => date("d/m/Y", strtotime($consulta1["f_retorno"])),
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => ($consulta1["periodo_retorno"] == '')? '' : $this->periodo[$consulta1["periodo_retorno"]],
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                $this->utilitarios(array(
                                    'tipo'      => '111',
                                    'x1'        => $x2,
                                    'y1'        => $y1,
                                    'txt'       => "",
                                    'border'    => 'LR',
                                    'align'     => 'C',
                                    'fill'      => $fill,
                                    'ln'        => 0,
                                    'stretch'   => 0,
                                    'ishtml'    => FALSE,
                                    'fitcell'   => FALSE,
                                    'valign'    => 'M'
                                ));

                                PDF::Ln();
                        }

                        // === TIPO DE SALIDA, MINUTOS O MINUTOS DE SALIDA Y CODIGO ===
                            $fill = FALSE;
                            $tt2  = 10;
                            $x1   = 85;
                            $x2   = 26;
                            $y1   = 4;

                            PDF::SetFont('times', 'B', 8);

                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x1,
                                'y1'      => $y1,
                                'txt'     => "Tipo de salida:",
                                'border'  => 'LR',
                                'align'   => 'L',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            if($consulta5['tipo_cronograma'] == '1')
                            {
                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Minutos de salida:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));
                            }
                            else
                            {
                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Número de días:",
                                    'border'  => 'LR',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));
                            }

                            if($consulta1['pdf'] == '1')
                            {
                                $txt     = $consulta1['codigo'];
                                $ishtml  = FALSE;
                            }
                            else
                            {
                                $url_pdf = url("storage/rrhh/salidas/solicitud_salida/" . $consulta1['papeleta_pdf']);
                                $txt    = '&nbsp;<a href="' . $url_pdf . '" style="text-decoration: none;" target="_blank">' . $consulta1['codigo'] . "</a>";
                                $ishtml = TRUE;
                            }

                            PDF::SetFont('times', 'B', 9);

                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x2,
                                'y1'      => $y1,
                                'txt'     => $txt,
                                'border'  => 'LRB',
                                'align'   => 'C',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => $ishtml,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            PDF::Ln();

                            $fill = FALSE;
                            $x1   = 85;
                            $x2   = 111;
                            $y1   = 8;

                            PDF::SetFont('times', '', 9);

                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x1,
                                'y1'      => $y1,
                                'txt'     => ($consulta5["tipo_salida"] == '')? '' : $this->tipo_salida[$consulta5["tipo_salida"]],
                                'border'  => 'LRB',
                                'align'   => 'C',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            if($consulta5['tipo_cronograma'] == '1')
                            {
                                $txt = '';

                                if($consulta5['tipo_salida'] == '2')
                                {
                                    $txt = round($consulta1['n_horas'] * 60, 0);
                                }
                            }
                            else
                            {
                                $txt = $consulta1['n_dias'];
                            }

                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x2,
                                'y1'      => $y1,
                                'txt'     => $txt,
                                'border'  => 'LRB',
                                'align'   => 'C',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            PDF::Ln();

                        // === DESTINO ===
                            if($consulta1['destino'] != '')
                            {
                                $fill = FALSE;
                                $tt2  = 10;
                                $x1   = 196;
                                $y1   = 4;

                                PDF::SetFont('times', 'B', 8);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Destino:",
                                    'border'  => 'LRT',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                PDF::Ln();

                                $fill = FALSE;
                                $x1   = 196;
                                $y1   = 8;

                                PDF::SetFont('times', '', 9);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => $consulta1['destino'],
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                PDF::Ln();
                            }

                        // === MOTIVO ===
                            if($consulta1['destino'] != '')
                            {
                                $fill = FALSE;
                                $tt2  = 10;
                                $x1   = 196;
                                $y1   = 4;

                                PDF::SetFont('times', 'B', 8);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => "Motivo:",
                                    'border'  => 'LRT',
                                    'align'   => 'L',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                PDF::Ln();

                                $fill = FALSE;
                                $x1   = 196;
                                $y1   = 8;

                                PDF::SetFont('times', '', 9);

                                $this->utilitarios(array(
                                    'tipo'    => '111',
                                    'x1'      => $x1,
                                    'y1'      => $y1,
                                    'txt'     => $consulta1['motivo'],
                                    'border'  => 'LRB',
                                    'align'   => 'C',
                                    'fill'    => $fill,
                                    'ln'      => 0,
                                    'stretch' => 0,
                                    'ishtml'  => FALSE,
                                    'fitcell' => FALSE,
                                    'valign'  => 'M'
                                ));

                                PDF::Ln();
                            }

                        // === CODIGO QR ===
                            $url_reporte = url("solicitud_salida/reportes?tipo=1&salida_id=" . $salida_id);
                            $this->utilitarios(array(
                                'tipo'    => '112',
                                'code'    => $url_reporte,
                                'type'    => 'QRCODE,L',
                                'x'       => 180.5,
                                'y'       => 35.5,
                                'w'       => 25,
                                'h'       => 25,
                                'style'   => $style_qrcode,
                                'align'   => '',
                                'distort' => FALSE
                            ));

                        // PDF::lastPage();

                    PDF::Output('papeleta_salida_' . date("YmdHis") . '.pdf', 'I');;
                }
                else
                {
                    return "La BOLETA DE SALIDA no existe";
                }
                break;
            default:
                break;
        }
    }
}