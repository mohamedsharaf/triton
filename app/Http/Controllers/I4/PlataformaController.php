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

use App\Models\I4\Caso;
use App\Models\I4\CasoFuncionario;
use App\Models\I4\TipoActividad;
use App\Models\I4\Actividad;
use App\Models\I4\Funcionario;
use App\Models\I4\Calendario;

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
    }

    public function index()
    {
        $this->rol_id            = Auth::user()->rol_id;
        $this->i4_funcionario_id = Auth::user()->i4_funcionario_id;

        $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
            ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
            ->select("seg_permisos.codigo")
            ->get()
            ->toArray();

        if(in_array(['codigo' => '2201'], $this->permisos))
        {
            $data = [
                'rol_id'               => $this->rol_id,
                'i4_funcionario_id'    => $this->i4_funcionario_id,
                'permisos'             => $this->permisos,
                'title'                => 'Plataforma',
                'home'                 => 'Inicio',
                'sistema'              => 'i4',
                'modulo'               => 'Plataforma',
                'title_table'          => 'Plataforma',
                'estado_array'         => $this->estado,
                'tipo_actividad_array' => TipoActividad::select(DB::raw("id, UPPER(TipoActividad) AS nombre"))
                                            ->where("estado_plataforma", "=", 1)
                                            ->orderBy("TipoActividad")
                                            ->get()
                                            ->toArray()
            ];
            return view('i4.plataforma.plataforma')->with($data);
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
            // === INSERT UPDATE UPLOAD ===
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
                        'sw_1'       => 0,
                        'titulo'     => '<div class="text-center"><strong>Añadir actividad</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'error_sw'   => 1
                    );
                    $opcion = 'n';

                // === PERMISOS ===
                    if(!in_array(['codigo' => '2202'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para AÑADIR ACTIVIDAD.";
                        return json_encode($respuesta);
                    }

                // === VALIDATE ===
                    try
                    {
                        $validator = $this->validate($request,[
                            'caso_id'           => 'required',
                            'tipo_actividad_id' => 'required',
                            'actvidad'          => 'max           : 120',
                            'file'              => 'required|mimes:pdf|max: 5120'
                        ],
                        [
                            'caso_id.required' => 'El campo ID DEL CASO es obligatorio.',

                            'tipo_actividad_id.required' => 'El campo TIPO DE ACTIVIDAD es obligatorio.',

                            'actvidad.max' => 'El campo ACTIVIDAD debe contener :max caracteres como máximo.',

                            'file.required' => 'El archivo PDF es obligatorio.',
                            'file.mimes'    => 'El archivo subido debe de ser de tipo PDF.',
                            'file.max'      => 'El archivo debe pesar 5120 kilobytes como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data1['caso_id']           = trim($request->input('caso_id'));
                    $data1['tipo_actividad_id'] = trim($request->input('tipo_actividad_id'));
                    $data1['actvidad']          = strtoupper($util->getNoAcentoNoComilla(trim($request->input('actvidad'))));

                    if($request->hasFile('file'))
                    {
                        $documento        = $request->file('file');
                        $documento_base64 = file_get_contents($documento->getRealPath());

                        $documento_name = $documento->getClientOriginalName();

                        // $respuesta['respuesta']    = $documento_name;
                        // return json_encode($respuesta);
                    }

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    $f_actual          = date("Y-m-d");
                    $fd_actual         = date("Y-m-d H:i:s");
                    $fd_actual_1       = date("Y-m-d_H-i-s");
                    $ip                = $request->ip();
                    $i4_funcionario_id = Auth::user()->i4_funcionario_id;

                    $consulta1 = Funcionario::where("id", "=", $i4_funcionario_id)
                                    ->select("Funcionario", "UserId")
                                    ->first();

                    $consulta2 = Calendario::where("Calendario", "=", $f_actual)
                                    ->select("id")
                                    ->first();

                    $iu                = new Actividad;
                    $iu->Caso          = $data1['caso_id'];
                    $iu->TipoActividad = $data1['tipo_actividad_id'];
                    $iu->Actividad     = $data1['actvidad'];

                    $iu->version              = 1;
                    $iu->Fecha                = $f_actual;
                    $iu->AllanamientoPositivo = 0;
                    $iu->RequisaPositiva      = 0;

                    $iu->Documento  = $documento_base64;
                    $iu->_Documento = $documento_name;

                    $iu->CreatorUser                  = $consulta1["UserId"];
                    $iu->CreatorFullName              = strtoupper($consulta1["Funcionario"]);
                    $iu->CreationDate                 = $fd_actual;
                    $iu->CreationIP                   = $ip;
                    $iu->UpdaterUser                  = $consulta1["UserId"];
                    $iu->UpdaterFullName              = strtoupper($consulta1["Funcionario"]);
                    $iu->UpdaterDate                  = $fd_actual;
                    $iu->UpdaterIP                    = $ip;
                    $iu->EstadoDocumento              = 2;
                    $iu->CalFecha                     = $consulta2["id"];
                    $iu->Asignado                     = $i4_funcionario_id;
                    $iu->FechaIni                     = $f_actual;
                    $iu->FechaFin                     = $f_actual;
                    $iu->estado_triton                = 1;
                    $iu->ActividadActualizaEstadoCaso = 0;

                    $iu->timestamps = false;

                    $iu->save();

                    $tabla1 = "Actividad";
                    $tabla2 = "TipoActividad";

                    $select3 = "
                        $tabla1.id,
                        $tabla1.Fecha,
                        $tabla1.Actividad,

                        a2.TipoActividad
                    ";

                    $where3 = "$tabla1.Caso=" . $data1['caso_id'];

                    $cosulta3 = Actividad::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.TipoActividad")
                        ->whereRaw($where3)
                        ->select(DB::raw($select3))
                        ->orderBy("$tabla1.CreationDate", "DESC")
                        ->get()
                        ->toArray();

                    if( ! ($cosulta3 === null))
                    {
                        $respuesta['sw_1'] = 1;
                        $respuesta['cosulta3'] = $cosulta3;
                    }

                    $respuesta['respuesta'] .= "El RECINTO CARCELARIO fue registrado con éxito.";
                    $respuesta['sw']         = 1;
                return json_encode($respuesta);
                break;

            // === BUSCANDO CASO ===
            case '100':
                // === LIBRERIAS ===
                    $util = new UtilClass();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'        => 0,
                        'sw_1'      => 0,
                        'sw_2'      => 0,
                        'titulo'    => '<div class = "text-center"><strong>BUSQUEDA DEL CASO</strong></div>',
                        'respuesta' => '',
                        'tipo'      => $tipo
                    );

                //=== CAMPOS ENVIADOS ===
                    $data1['caso'] = strtoupper($util->getNoAcentoNoComilla(trim($request->input('caso'))));

                    if($data1['caso'] == "")
                    {
                        $respuesta['respuesta'] .= "El campo CASO está vacio.";
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $tabla1  = "Caso";
                    $tabla2  = "Delito";
                    $tabla3  = "EtapaCaso";
                    $tabla4  = "EstadoCaso";
                    $tabla5  = "OrigenCaso";

                    $select1 = "
                        $tabla1.id,
                        $tabla1.Caso,
                        $tabla1.CodCasoJuz,
                        $tabla1.FechaDenuncia,
                        $tabla1.DelitoPrincipal,
                        $tabla1.EtapaCaso,
                        $tabla1.EstadoCaso,
                        $tabla1.OrigenCaso,
                        $tabla1.triton_modificado,
                        $tabla1.n_detenidos,
                        $tabla1.DivisionFis AS division_id,

                        UPPER(a2.Delito) AS delito_principal,

                        UPPER(a3.EtapaCaso) AS etapa_caso,

                        UPPER(a4.EstadoCaso) AS estado_caso,

                        UPPER(a5.OrigenCaso) AS origen_caso
                    ";

                    $where1 = "$tabla1.Caso='" . $data1['caso'] . "'";

                    $cosulta1 = Caso::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.DelitoPrincipal")
                        ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.EtapaCaso")
                        ->leftJoin("$tabla4 AS a4", "a4.id", "=", "$tabla1.EstadoCaso")
                        ->leftJoin("$tabla5 AS a5", "a5.id", "=", "$tabla1.OrigenCaso")
                        ->whereRaw($where1)
                        ->select(DB::raw($select1))
                        ->first();

                    if($cosulta1 === null)
                    {
                        $respuesta['respuesta'] .= "No se encontró el CASO.";
                        return json_encode($respuesta);
                    }

                    $tabla1 = "CasoFuncionario";
                    $tabla2 = "Funcionario";

                    $select2 = "
                        $tabla1.Caso AS caso_id,

                        UPPER(GROUP_CONCAT(DISTINCT a2.Funcionario ORDER BY a2.Funcionario ASC SEPARATOR ', ')) AS funcionario
                    ";

                    $group_by_2 = "
                        $tabla1.Caso
                    ";

                    $where2 = "$tabla1.Caso=" . $cosulta1['id'] . " AND $tabla1.FechaBaja IS NULL";

                    $cosulta2 = CasoFuncionario::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.Funcionario")
                        ->whereRaw($where2)
                        ->select(DB::raw($select2))
                        ->groupBy(DB::raw($group_by_2))
                        ->first();

                    if( ! ($cosulta2 === null))
                    {
                        $respuesta['sw_1'] = 1;
                        $respuesta['cosulta2'] = $cosulta2;
                    }

                    $tabla1 = "Actividad";
                    $tabla2 = "TipoActividad";

                    $select3 = "
                        $tabla1.id,
                        $tabla1.Fecha,
                        UPPER($tabla1.Actividad) AS Actividad,

                        UPPER(a2.TipoActividad) AS TipoActividad
                    ";

                    $where3 = "$tabla1.Caso=" . $cosulta1['id'];

                    $cosulta3 = Actividad::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.TipoActividad")
                        ->whereRaw($where3)
                        ->select(DB::raw($select3))
                        ->orderBy("$tabla1.CreationDate", "DESC")
                        ->get()
                        ->toArray();

                    if( ! ($cosulta3 === null))
                    {
                        $respuesta['sw_2'] = 1;
                        $respuesta['cosulta3'] = $cosulta3;
                    }

                    $respuesta['cosulta1'] = $cosulta1;

                    $respuesta['respuesta'] .= "El CASO fue encontrado.";
                    $respuesta['sw']         = 1;

                return json_encode($respuesta);
                break;
            // === SELECT2 DEPARTAMENTO, MUNICIPIO ===
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