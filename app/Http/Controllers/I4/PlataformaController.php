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
use App\Models\I4\Persona;

use App\Models\I4\Division;
use App\Models\I4\Dep;

use Maatwebsite\Excel\Facades\Excel;
use PDF;

use Exception;

class PlataformaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->tipo_reporte = [
            '1' => 'MEMORIALES',
            '2' => 'REPARTO DE CASO'
        ];

        $this->public_dir = '/image/logo';
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
                'tipo_reporte_array'   => $this->tipo_reporte,
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
                        UPPER($tabla1.Actividad) AS Actividad,
                        $tabla1.estado_triton,

                        UPPER(a2.TipoActividad) AS TipoActividad
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
                        'sw_3'      => 0,
                        'sw_4'      => 0,
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
                    //=== CONSULTA 1 ===
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

                    //=== CONSULTA 3 ===
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
                            $respuesta['sw_1']     = 1;
                            $respuesta['cosulta2'] = $cosulta2;
                        }

                    //=== CONSULTA 3 ===
                        $tabla1 = "Actividad";
                        $tabla2 = "TipoActividad";

                        $select3 = "
                            $tabla1.id,
                            $tabla1.Fecha,
                            UPPER($tabla1.Actividad) AS Actividad,
                            $tabla1.estado_triton,

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

                    //=== CONSULTA 4 ===
                        $tabla1 = "Persona";

                        $select4 = "
                            $tabla1.Caso AS caso_id,

                            UPPER(GROUP_CONCAT(DISTINCT $tabla1.Persona ORDER BY $tabla1.Persona ASC SEPARATOR ', ')) AS denunciante
                        ";

                        $group_by_4 = "
                            $tabla1.Caso
                        ";

                        $where4 = "$tabla1.Caso=" . $cosulta1['id'] . " AND $tabla1.EsDenunciante=1";

                        $cosulta4 = Persona::whereRaw($where4)
                            ->select(DB::raw($select4))
                            ->groupBy(DB::raw($group_by_4))
                            ->first();

                        if( ! ($cosulta4 === null))
                        {
                            $respuesta['sw_3']     = 1;
                            $respuesta['cosulta4'] = $cosulta4;
                        }

                    //=== CONSULTA 5 ===
                        $tabla1 = "Persona";

                        $select5 = "
                            $tabla1.Caso AS caso_id,

                            UPPER(GROUP_CONCAT(DISTINCT $tabla1.Persona ORDER BY $tabla1.Persona ASC SEPARATOR ', ')) AS denunciado
                        ";

                        $group_by_5 = "
                            $tabla1.Caso
                        ";

                        $where5 = "$tabla1.Caso=" . $cosulta1['id'] . " AND $tabla1.EsDenunciado=1";

                        $cosulta5 = Persona::whereRaw($where5)
                            ->select(DB::raw($select5))
                            ->groupBy(DB::raw($group_by_5))
                            ->first();

                        if( ! ($cosulta5 === null))
                        {
                            $respuesta['sw_4']     = 1;
                            $respuesta['cosulta5'] = $cosulta5;
                        }

                    $respuesta['cosulta1'] = $cosulta1;

                    $respuesta['respuesta'] .= "El CASO fue encontrado.";
                    $respuesta['sw']         = 1;

                return json_encode($respuesta);
                break;
            // === SELECT2 DIVISION ===
            case '101':
                if($request->has('q'))
                {
                    $i4_funcionario_id = Auth::user()->i4_funcionario_id;
                    $where1            = " AND Division.Activo=1";
                    if($i4_funcionario_id != "")
                    {
                        $consulta1 = Funcionario::leftJoin("Division", "Division.id", "=", "Funcionario.Division")
                                        ->leftJoin("Oficina", "Oficina.id", "=", "Division.Oficina")
                                        ->leftJoin("Muni", "Muni.id", "=", "Oficina.Muni")
                                        ->whereRaw("Funcionario.id=" . $i4_funcionario_id)
                                        ->select(DB::raw("Muni.Dep AS departamento_id"))
                                        ->first();

                        if(!($consulta1 === null))
                        {
                            $where1 = " AND Dep.id=" . $consulta1["departamento_id"];
                        }
                    }

                    $nombre     = $request->input('q');
                    $page_limit = trim($request->input('page_limit'));

                    $query = Division::leftJoin("Oficina", "Oficina.id", "=", "Division.Oficina")
                                ->leftJoin("Muni", "Muni.id", "=", "Oficina.Muni")
                                ->leftJoin("Dep", "Dep.id", "=", "Muni.Dep")
                                ->whereRaw("CONCAT_WS(', ', Dep.Dep, Muni.Muni, Oficina.Oficina, Division.Division) LIKE '%$nombre%'" . $where1)
                                ->select(DB::raw("Division.id, UPPER(CONCAT_WS(', ', Dep.Dep, Muni.Muni, Oficina.Oficina, Division.Division)) AS text"))
                                ->orderByRaw("Division.Division ASC, Oficina.Oficina ASC")
                                ->limit($page_limit)
                                ->get();

                    if( ! $query->isEmpty())
                    {
                        $respuesta = [
                            "results"  => $query->toArray(),
                            "paginate" => [
                                "more" =>true
                            ]
                        ];
                        return json_encode($respuesta);
                    }
                }
                break;
            // === SELECT2 RELLENAR FUNCIONARIO DEL I4 ===
            case '102':
                if($request->has('q'))
                {
                    $nombre     = $request->input('q');
                    $estado     = trim($request->input('estado'));
                    $page_limit = trim($request->input('page_limit'));

                    $query = Funcionario::whereRaw("CONCAT_WS(' - ', NumDocId, CONCAT_WS(' ', ApPat, ApMat, Nombres)) LIKE '%$nombre%' AND CuentaActiva=1")
                                ->select(DB::raw("id, UPPER(CONCAT_WS(' - ', NumDocId, CONCAT_WS(' ', ApPat, ApMat, Nombres))) AS text"))
                                ->orderByRaw("CONCAT_WS(' ', ApPat, ApMat, Nombres) ASC")
                                ->limit($page_limit)
                                ->get();

                    if( ! $query->isEmpty())
                    {
                        $respuesta = [
                            "results"  => $query->toArray(),
                            "paginate" => [
                                "more" =>true
                            ]
                        ];
                        return json_encode($respuesta);
                    }
                }
                break;
            // === SELECT2 DEPARTAMENTO, MUNICIPIO ===
            case '103':
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
            // === REPORTE PDF - GENERAR RECIBIDO ===
            case '1':
                if($request->has('id'))
                {
                    $actividad_id = trim($request->input('id'));

                    $dir_logo_institucion = public_path($this->public_dir) . '/' . 'logo_fge_256_2018_3.png';

                    // === VALIDAR IMAGENES ===
                        if( ! file_exists($dir_logo_institucion))
                        {
                            return "No existe el logo de la institución " . $dir_logo_institucion;
                        }

                    // === CONSULTA A LA BASE DE DATOS ===
                        $tabla1 = "Actividad";
                        $tabla2 = "Caso";
                        $tabla3 = "TipoActividad";

                        $select1 = "
                            $tabla1.id,
                            $tabla1.CreationDate,
                            $tabla1.Caso AS caso_id,

                            a2.Caso AS numero_caso,

                            UPPER(a3.TipoActividad) AS TipoActividad
                        ";

                        $where1 = "$tabla1.id=" . $actividad_id;

                        $cosulta1 = Actividad::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.Caso")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.TipoActividad")
                            ->whereRaw($where1)
                            ->select(DB::raw($select1))
                            ->first();

                        $tabla1 = "CasoFuncionario";
                        $tabla2 = "Funcionario";

                        $select2 = "
                            $tabla1.Caso AS caso_id,

                            UPPER(GROUP_CONCAT(DISTINCT a2.Funcionario ORDER BY a2.Funcionario ASC SEPARATOR ', ')) AS funcionario
                        ";

                        $group_by_2 = "
                            $tabla1.Caso
                        ";

                        $where2 = "$tabla1.Caso=" . $cosulta1['caso_id'] . " AND $tabla1.FechaBaja IS NULL";

                        $cosulta2 = CasoFuncionario::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.Funcionario")
                            ->whereRaw($where2)
                            ->select(DB::raw($select2))
                            ->groupBy(DB::raw($group_by_2))
                            ->first();

                    PDF::setPageUnit('mm');

                    PDF::SetMargins(1, 1, 1);
                    PDF::getAliasNbPages();
                    PDF::SetCreator('MINISTERIO PUBLICO');
                    PDF::SetAuthor('TRITON');
                    PDF::SetTitle('RECIBO');
                    PDF::SetSubject('DOCUMENTO');
                    PDF::SetKeywords('RECIBO');

                    PDF::SetAutoPageBreak(FALSE, 0);

                    // === BODY ===
                        PDF::AddPage('P', array(62,30));

                            $this->utilitarios(array(
                                'tipo'      => '100',
                                'file'      => $dir_logo_institucion ,
                                'x'         => 5,
                                'y'         => 0,
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

                            PDF::Ln(17);

                            $fill = FALSE;
                            $x1   = 28;
                            $x2   = 28;
                            $y1   = 3;

                            PDF::SetFont('times', 'B', 6);
                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x1,
                                'y1'      => $y1,
                                'txt'     => "MINISTERIO PÚBLICO",
                                'border'  => 0,
                                'align'   => 'C',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            PDF::Ln();

                            $fill = FALSE;
                            $x1   = 28;
                            $x2   = 28;
                            $y1   = 7;

                            PDF::SetFont('times', '', 6);
                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x1,
                                'y1'      => $y1,
                                'txt'     => $cosulta1['TipoActividad'],
                                'border'  => 0,
                                'align'   => 'C',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            PDF::Ln();

                            $fill = FALSE;
                            $x1   = 28;
                            $x2   = 28;
                            $y1   = 7;

                            PDF::SetFont('times', '', 6);
                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x1,
                                'y1'      => $y1,
                                'txt'     => $cosulta2['funcionario'],
                                'border'  => 0,
                                'align'   => 'C',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            PDF::Ln();

                            $fill = FALSE;
                            $x1   = 28;
                            $x2   = 28;
                            $y1   = 3;

                            PDF::SetFont('times', 'B', 6);
                            $this->utilitarios(array(
                                'tipo'    => '111',
                                'x1'      => $x1,
                                'y1'      => $y1,
                                'txt'     => $cosulta1['numero_caso'],
                                'border'  => 0,
                                'align'   => 'C',
                                'fill'    => $fill,
                                'ln'      => 0,
                                'stretch' => 0,
                                'ishtml'  => FALSE,
                                'fitcell' => FALSE,
                                'valign'  => 'M'
                            ));

                            PDF::Ln();

                            PDF::SetFont('times', '', 8);
                            $this->utilitarios(array(
                                'tipo'       => '110',
                                'h'          => 44,
                                'txt'        => date('d/m/Y H:i:s', strtotime($cosulta1['CreationDate'])),
                                'link'       => '',
                                'fill'       => FALSE,
                                'align'      => 'C',
                                'ln'         => TRUE,
                                'stretch'    => 0,
                                'firstline'  => FALSE,
                                'firstblock' => FALSE,
                                'maxh'       => 0
                            ));

                        // === CODIGO QR ===
                            $style_qrcode = array(
                                'border'        => 0,
                                'vpadding'      => 'auto',
                                'hpadding'      => 'auto',
                                'fgcolor'       => array(0, 0, 0),
                                'bgcolor'       => false, //array(255,255,255)
                                'module_width'  => 1, // width of a single module in points
                                'module_height' => 1 // height of a single module in points
                            );

                            $url_reporte = url("plataforma/reportes?tipo=1&id=" . $actividad_id);

                            $this->utilitarios(array(
                                'tipo'    => '112',
                                'code'    => $url_reporte,
                                'type'    => 'QRCODE,L',
                                'x'       => 2.4,
                                'y'       => 36,
                                'w'       => 25,
                                'h'       => 25,
                                'style'   => $style_qrcode,
                                'align'   => '',
                                'distort' => FALSE
                            ));

                    PDF::Output('recibido_' . date("YmdHis") . '.pdf', 'I');
                }
                else
                {
                    return "La ACTIVIDAD no existe";
                }
                break;
            // === REPORTE PDF - REPORTES ===
            case '2':
                if($request->has('tipo_reporte'))
                {
                    switch($request->input('tipo_reporte'))
                    {
                        // === MEMORIALES ===
                        case '1':
                            $division_id    = trim($request->input('division_id'));
                            $funcionario_id = trim($request->input('funcionario_id'));
                            $fecha_del      = trim($request->input('fecha_del'));
                            $hora_del       = trim($request->input('hora_del'));
                            $fecha_al       = trim($request->input('fecha_al'));
                            $hora_al        = trim($request->input('hora_al'));

                            $fh_actual            = date("Y-m-d H:i:s");
                            $dir_logo_institucion = public_path($this->public_dir) . '/' . 'logo_fge_256_2018_3.png';

                            // === VALIDAR IMAGENES ===
                                if( ! file_exists($dir_logo_institucion))
                                {
                                    return "No existe el logo de la institución " . $dir_logo_institucion;
                                }

                            // === CONSULTA A LA BASE DE DATOS ===
                                //=== CONSULTA 1 ===
                                    $tabla1 = "Caso";
                                    $tabla2 = "Actividad";
                                    $tabla3 = "TipoActividad";
                                    $tabla4 = "Division";
                                    $tabla5 = "Oficina";
                                    $tabla6 = "Muni";
                                    $tabla7 = "CasoFuncionario";

                                    $select1 = "
                                        $tabla1.id,
                                        $tabla1.Caso,
                                        $tabla1.DivisionFis,

                                        a2.CreationDate,
                                        UPPER(a2.Actividad) AS actividad,
                                        a2.Fecha AS fecha,

                                        UPPER(a3.TipoActividad) AS tipo_actividad,

                                        UPPER(a4.Division) AS division,
                                        (
                                            SELECT UPPER(GROUP_CONCAT(DISTINCT a61.Funcionario ORDER BY a61.Funcionario ASC SEPARATOR ', ')) AS fiscale
                                            FROM CasoFuncionario AS a60
                                            INNER JOIN Funcionario AS a61 ON a61.id=a60.Funcionario
                                            WHERE a60.FechaBaja IS NULL AND a60.Caso=$tabla1.id
                                        ) AS fiscales
                                    ";


                                    $group_by_1 = "
                                        $tabla1.id,
                                        $tabla1.Caso,
                                        $tabla1.DivisionFis,

                                        a2.CreationDate,
                                        a2.Actividad,
                                        a2.Fecha,

                                        a3.TipoActividad,

                                        a4.Division
                                    ";

                                    $where1 = "a2.estado_triton=1 AND a2.CreationDate >= '" . $fecha_del . " " . $hora_del . "' AND a2.CreationDate <= '" . $fecha_al . " " . $hora_al . "'";

                                    if($request->has('funcionario_id'))
                                    {
                                        $where1_1             = "";
                                        $where1_1_sw          = TRUE;
                                        $funcionario_id_array = explode(",", $funcionario_id);
                                        foreach ($funcionario_id_array as $valor1)
                                        {
                                            if($where1_1_sw)
                                            {
                                                $where1_1    .= " AND (a7.Funcionario=" . $valor1;
                                                $where1_1_sw = FALSE;
                                            }
                                            else
                                            {
                                                $where1_1 .= " OR a7.Funcionario=" . $valor1;
                                            }
                                        }
                                        $where1_1 .= ") AND a7.FechaBaja IS NULL";
                                        $where1   .= $where1_1;
                                    }

                                    if($request->has('division_id'))
                                    {
                                        $where1_1          = "";
                                        $where1_1_sw       = TRUE;
                                        $division_id_array = explode(",", $division_id);
                                        foreach ($division_id_array as $valor1)
                                        {
                                            if($where1_1_sw)
                                            {
                                                $where1_1    .= " AND (a4.id=" . $valor1;
                                                $where1_1_sw = FALSE;
                                            }
                                            else
                                            {
                                                $where1_1 .= " OR a4.id=" . $valor1;
                                            }
                                        }
                                        $where1_1 .= ")";
                                        $where1   .= $where1_1;

                                        $consulta1 = Caso::join("$tabla2 AS a2", "a2.Caso", "=", "$tabla1.id")
                                                        ->join("$tabla3 AS a3", "a3.id", "=", "a2.TipoActividad")
                                                        ->join("$tabla4 AS a4", "a4.id", "=", "$tabla1.DivisionFis")
                                                        ->leftJoin("$tabla7 AS a7", "a7.Caso", "=", "$tabla1.id")
                                                        ->whereRaw($where1)
                                                        ->select(DB::raw($select1))
                                                        ->groupBy(DB::raw($group_by_1))
                                                        ->orderBy("a7.Funcionario", "ASC")
                                                        ->orderBy("a2.CreationDate", "ASC")
                                                        ->get();
                                    }
                                    else
                                    {
                                        $i4_funcionario_id = Auth::user()->i4_funcionario_id;

                                        if($i4_funcionario_id == "")
                                        {
                                            return dd("No tiene cuenta en el i4.");
                                        }

                                        $consulta2 = Funcionario::join("Division", "Division.id", "=", "Funcionario.Division")
                                                        ->join("Oficina", "Oficina.id", "=", "Division.Oficina")
                                                        ->join("Muni", "Muni.id", "=", "Oficina.Muni")
                                                        ->whereRaw("Funcionario.id=" . $i4_funcionario_id)
                                                        ->select(DB::raw("Muni.Dep AS departamento_id"))
                                                        ->first();

                                        if($consulta2 === null)
                                        {
                                            return dd("No tiene cuenta en el i4.");
                                        }

                                        $where1   .= " AND a6.Dep=" . $consulta2["departamento_id"];

                                        $consulta1 = Caso::join("$tabla2 AS a2", "a2.Caso", "=", "$tabla1.id")
                                                        ->join("$tabla3 AS a3", "a3.id", "=", "a2.TipoActividad")
                                                        ->join("$tabla4 AS a4", "a4.id", "=", "$tabla1.DivisionFis")
                                                        ->join("$tabla5 AS a5", "a5.id", "=", "a4.Oficina")
                                                        ->join("$tabla6 AS a6", "a6.id", "=", "a5.Muni")
                                                        ->leftJoin("$tabla7 AS a7", "a7.Caso", "=", "$tabla1.id")
                                                        ->whereRaw($where1)
                                                        ->select(DB::raw($select1))
                                                        ->groupBy(DB::raw($group_by_1))
                                                        ->orderBy("a7.Funcionario", "ASC")
                                                        ->orderBy("a2.CreationDate", "ASC")
                                                        ->get();
                                    }

                                    if($consulta1->isEmpty())
                                    {
                                        return dd("No se encontraron CASOS.");
                                    }

                            // === CARGAR VALORES ===
                                $x1_array = [
                                    10,
                                    30,
                                    20,
                                    50,
                                    50,
                                    50,
                                    50,
                                    50
                                ];

                                $data1 = array(
                                    'dir_logo_institucion' => $dir_logo_institucion,
                                    'x1_array'             => $x1_array,
                                    'url_pdf'              => url()->full()
                                );

                                $data2 = array(
                                    'fh_actual' => $fh_actual
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

                            // === HEADER ===
                                PDF::setHeaderCallback(function($pdf) use($data1){
                                    $pdf->Image($data1['dir_logo_institucion'], 297, 6, 0, 23, 'PNG');

                                    $pdf->Ln(7);
                                    $pdf->SetFont('times', 'B', 22);
                                    $pdf->Write(0, 'MINISTERIO PÚBLICO', '', 0, 'C', true, 0, false, false, 0);

                                    $pdf->SetFont('times', 'B', 18);
                                    $pdf->Write(0, $this->tipo_reporte['1'], '', 0, 'C', true, 0, false, false, 0);

                                    $pdf->Ln(2.5);

                                    $pdf->SetFillColor(211, 200, 206);
                                    $pdf->SetFont("times", "B", 7);

                                    $y=8;
                                    $i= 0;

                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "No", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "NUMERO CASO", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "FECHA INGRESO", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "TIPO DE ACTIVIDAD", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "ACTIVIDAD", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "DIVISION", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "FISCAL", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "FIRMA", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");

                                    $style_qrcode = array(
                                        'border'        => 0,
                                        'vpadding'      => 'auto',
                                        'hpadding'      => 'auto',
                                        'fgcolor'       => array(0, 0, 0),
                                        'bgcolor'       => false, //array(255,255,255)
                                        'module_width'  => 1, // width of a single module in points
                                        'module_height' => 1 // height of a single module in points
                                    );

                                    $this->utilitarios(array(
                                        'tipo'    => '112',
                                        'code'    => $data1['url_pdf'],
                                        'type'    => 'QRCODE,L',
                                        'x'       => 8.2,
                                        'y'       => 3,
                                        'w'       => 25,
                                        'h'       => 25,
                                        'style'   => $style_qrcode,
                                        'align'   => '',
                                        'distort' => FALSE
                                    ));
                                });
                            // === FOOTER ===
                                PDF::setFooterCallback(function($pdf) use($data2){
                                    $style1 = array(
                                        'width' => 0.5,
                                        'cap'   => 'butt',
                                        'join'  => 'miter',
                                        'dash'  => '0',
                                        'phase' => 10,
                                        'color' => array(0, 0, 0)
                                    );

                                    $pdf->Line(10, 204, 320, 204, $style1);
                                    $pdf->SetY(-11);
                                    $pdf->SetFont("times", "I", 7);
                                    $pdf->Cell(155, 4, 'Fecha de emisión: ' . date("d/m/Y H:i:s", strtotime($data2['fh_actual'])), 0, 0, "L");
                                    $pdf->Cell(155, 4, "Página " . $pdf->getAliasNumPage() . "/" . $pdf->getAliasNbPages(), 0, 0, "R");
                                });

                            PDF::setPageUnit('mm');

                            PDF::SetMargins(10, 35.3, 10);
                            PDF::getAliasNbPages();
                            PDF::SetCreator('MINISTERIO PUBLICO');
                            PDF::SetAuthor('TRITON');
                            PDF::SetTitle($this->tipo_reporte['1']);
                            PDF::SetSubject('DOCUMENTO');
                            PDF::SetKeywords($this->tipo_reporte['1']);

                            PDF::SetAutoPageBreak(FALSE, 10);

                            // === BODY ===
                                PDF::AddPage('L', 'FOLIO');

                                $c    = 1;
                                $y    = 16.85;
                                $fill = FALSE;
                                PDF::SetFont("times", "", 2);
                                PDF::SetFillColor(204, 239, 252);

                                $ta1 = 7;
                                PDF::SetFont("times", "", $ta1);

                                foreach($consulta1->toArray() AS $row1)
                                {
                                    $i  = 0;
                                    $y1 = PDF::GetY();
                                    if ($y + $y1 > 204)
                                    {
                                        PDF::Cell(310, 1, "", "T", 0, "L");
                                        PDF::AddPage('L', 'FOLIO');
                                    }

                                    PDF::MultiCell($x1_array[$i++], $y, $c++, 1, "R", $fill, 0, "", "", true, 0, false, true, $y, "M");
                                    PDF::MultiCell($x1_array[$i++], $y, $row1['Caso'] . "\n", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");
                                    PDF::MultiCell($x1_array[$i++], $y, $row1['fecha'] . "\n", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");
                                    PDF::MultiCell($x1_array[$i++], $y, $row1['tipo_actividad'] . "\n", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");
                                    PDF::MultiCell($x1_array[$i++], $y, $row1['actividad'] . "\n", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");
                                    PDF::MultiCell($x1_array[$i++], $y, $row1['division'] . "\n", 1, "J", $fill, 0, "", "", true, 0, false, true, $y, "M");
                                    PDF::MultiCell($x1_array[$i++], $y, $row1['fiscales'] . "\n", 1, "J", $fill, 0, "", "", true, 0, false, true, $y, "M");
                                    PDF::MultiCell($x1_array[$i++], $y, "", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");

                                    PDF::Ln();
                                    $fill = !$fill;
                                }

                            PDF::Output('memoriales_' . date("YmdHis") . '.pdf', 'I');
                            break;
                        // === REPARTO DE CASO ===
                        case '2':
                            $division_id    = trim($request->input('division_id'));
                            $funcionario_id = trim($request->input('funcionario_id'));
                            $fecha_del      = trim($request->input('fecha_del'));
                            $hora_del       = trim($request->input('hora_del'));
                            $fecha_al       = trim($request->input('fecha_al'));
                            $hora_al        = trim($request->input('hora_al'));

                            $fh_actual            = date("Y-m-d H:i:s");
                            $dir_logo_institucion = public_path($this->public_dir) . '/' . 'logo_fge_256_2018_3.png';

                            // === VALIDAR IMAGENES ===
                                if( ! file_exists($dir_logo_institucion))
                                {
                                    return "No existe el logo de la institución " . $dir_logo_institucion;
                                }

                            // === CONSULTA A LA BASE DE DATOS ===
                                //=== CONSULTA 1 ===
                                    $tabla1 = "Caso";
                                    $tabla2 = "Delito";
                                    $tabla3 = "Division";
                                    $tabla4 = "Oficina";
                                    $tabla5 = "Muni";
                                    $tabla6 = "CasoFuncionario";

                                    $select1 = "
                                        $tabla1.id,
                                        $tabla1.Caso,
                                        $tabla1.FechaDenuncia,
                                        $tabla1.DivisionFis,

                                        UPPER(a2.Delito) AS delito_principal,

                                        UPPER(a3.Division) AS division,

                                        a6.CreationDate,
                                        a6.FechaAlta AS f_reparto,

                                        (
                                            SELECT UPPER(GROUP_CONCAT(DISTINCT a40.Persona ORDER BY a40.Persona ASC SEPARATOR ', ')) AS denunciante
                                            FROM Persona AS a40
                                            WHERE a40.EsDenunciante=1 AND a40.Caso=$tabla1.id
                                        ) AS denunciantes,

                                        (
                                            SELECT UPPER(GROUP_CONCAT(DISTINCT a50.Persona ORDER BY a50.Persona ASC SEPARATOR ', ')) AS denunciado
                                            FROM Persona AS a50
                                            WHERE a50.EsDenunciado=1 AND a50.Caso=$tabla1.id
                                        ) AS denunciados,

                                        (
                                            SELECT UPPER(GROUP_CONCAT(DISTINCT a61.Funcionario ORDER BY a61.Funcionario ASC SEPARATOR ', ')) AS fiscale
                                            FROM CasoFuncionario AS a60
                                            INNER JOIN Funcionario AS a61 ON a61.id=a60.Funcionario
                                            WHERE a60.FechaBaja IS NULL AND a60.Caso=$tabla1.id
                                        ) AS fiscales,

                                        (
                                            SELECT UPPER(GROUP_CONCAT(DISTINCT a71.Funcionario ORDER BY a71.FechaAlta DESC SEPARATOR ', ')) AS anterior_fiscale
                                            FROM CasoFuncionario AS a70
                                            INNER JOIN Funcionario AS a71 ON a71.id=a70.Funcionario
                                            WHERE a70.FechaBaja IS NOT NULL AND a70.Caso=$tabla1.id
                                        ) AS anteriores_fiscales
                                    ";


                                    $group_by_1 = "
                                        $tabla1.id,
                                        $tabla1.Caso,
                                        $tabla1.FechaDenuncia,
                                        $tabla1.DivisionFis,

                                        a2.Delito,

                                        a3.Division,

                                        a6.CreationDate,
                                        a6.FechaAlta
                                    ";

                                    $where1 = "a6.CreationDate >= '" . $fecha_del . " " . $hora_del . "' AND a6.CreationDate <= ' " . $fecha_al . " " . $hora_al . "' AND a6.FechaBaja IS NULL";

                                    if($request->has('funcionario_id'))
                                    {
                                        $where1_1             = "";
                                        $where1_1_sw          = TRUE;
                                        $funcionario_id_array = explode(",", $funcionario_id);
                                        foreach ($funcionario_id_array as $valor1)
                                        {
                                            if($where1_1_sw)
                                            {
                                                $where1_1    .= " AND (a6.Funcionario=" . $valor1;
                                                $where1_1_sw = FALSE;
                                            }
                                            else
                                            {
                                                $where1_1 .= " OR a6.Funcionario=" . $valor1;
                                            }
                                        }
                                        $where1_1 .= ")";
                                        $where1   .= $where1_1;
                                    }

                                    if($request->has('division_id'))
                                    {
                                        $where1_1          = "";
                                        $where1_1_sw       = TRUE;
                                        $division_id_array = explode(",", $division_id);
                                        foreach ($division_id_array as $valor1)
                                        {
                                            if($where1_1_sw)
                                            {
                                                $where1_1    .= " AND (a3.id=" . $valor1;
                                                $where1_1_sw = FALSE;
                                            }
                                            else
                                            {
                                                $where1_1 .= " OR a3.id=" . $valor1;
                                            }
                                        }
                                        $where1_1 .= ")";
                                        $where1   .= $where1_1;

                                        $consulta1 = Caso::join("$tabla2 AS a2", "a2.id", "=", "$tabla1.DelitoPrincipal")
                                                        ->join("$tabla3 AS a3", "a3.id", "=", "$tabla1.DivisionFis")
                                                        ->leftJoin("$tabla6 AS a6", "a6.Caso", "=", "$tabla1.id")
                                                        ->whereRaw($where1)
                                                        ->select(DB::raw($select1))
                                                        ->groupBy(DB::raw($group_by_1))
                                                        ->orderBy("a6.CreationDate", "ASC")
                                                        ->get();
                                    }
                                    else
                                    {
                                        $i4_funcionario_id = Auth::user()->i4_funcionario_id;

                                        if($i4_funcionario_id == "")
                                        {
                                            return dd("No tiene cuenta en el i4.");
                                        }

                                        $consulta2 = Funcionario::join("Division", "Division.id", "=", "Funcionario.Division")
                                                        ->join("Oficina", "Oficina.id", "=", "Division.Oficina")
                                                        ->join("Muni", "Muni.id", "=", "Oficina.Muni")
                                                        ->whereRaw("Funcionario.id=" . $i4_funcionario_id)
                                                        ->select(DB::raw("Muni.Dep AS departamento_id"))
                                                        ->first();

                                        if($consulta2 === null)
                                        {
                                            return dd("No tiene cuenta en el i4.");
                                        }

                                        $where1   .= " AND a5.Dep=" . $consulta2["departamento_id"];

                                        $consulta1 = Caso::join("$tabla2 AS a2", "a2.id", "=", "$tabla1.DelitoPrincipal")
                                                        ->join("$tabla3 AS a3", "a3.id", "=", "$tabla1.DivisionFis")
                                                        ->join("$tabla4 AS a4", "a4.id", "=", "a3.Oficina")
                                                        ->join("$tabla5 AS a5", "a5.id", "=", "a4.Muni")
                                                        ->leftJoin("$tabla6 AS a6", "a6.Caso", "=", "$tabla1.id")
                                                        ->whereRaw($where1)
                                                        ->select(DB::raw($select1))
                                                        ->groupBy(DB::raw($group_by_1))
                                                        ->orderBy("a6.CreationDate", "ASC")
                                                        ->get();
                                    }

                                    if($consulta1->isEmpty())
                                    {
                                        return dd("No se encontraron CASOS.");
                                    }

                            // === CARGAR VALORES ===
                                $x1_array = [
                                    8,
                                    20,
                                    15,
                                    50,
                                    50,
                                    35,
                                    50,
                                    50,
                                    32
                                ];

                                $data1 = array(
                                    'dir_logo_institucion' => $dir_logo_institucion,
                                    'x1_array'             => $x1_array,
                                    'url_pdf'              => url()->full()
                                );

                                $data2 = array(
                                    'fh_actual' => $fh_actual
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

                            // === HEADER ===
                                PDF::setHeaderCallback(function($pdf) use($data1){
                                    $pdf->Image($data1['dir_logo_institucion'], 297, 6, 0, 23, 'PNG');

                                    $pdf->Ln(7);
                                    $pdf->SetFont('times', 'B', 22);
                                    $pdf->Write(0, 'MINISTERIO PÚBLICO', '', 0, 'C', true, 0, false, false, 0);

                                    $pdf->SetFont('times', 'B', 18);
                                    $pdf->Write(0, $this->tipo_reporte['2'], '', 0, 'C', true, 0, false, false, 0);

                                    $pdf->Ln(2.5);

                                    $pdf->SetFillColor(211, 200, 206);
                                    $pdf->SetFont("times", "B", 7);

                                    $y=8;
                                    $i= 0;

                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "No", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "NUMERO CASO", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "FECHA DENUNCIA", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "DENUNCIANTE", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "DENUNCIADO", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "DELITO PRINCIPAL", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "ANTERIORES FISCALES", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "FECHA DE REPARTO\nFISCAL ASIGNADO", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                                    $pdf->MultiCell($data1['x1_array'][$i++], $y, "FIRMA", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");

                                    $style_qrcode = array(
                                        'border'        => 0,
                                        'vpadding'      => 'auto',
                                        'hpadding'      => 'auto',
                                        'fgcolor'       => array(0, 0, 0),
                                        'bgcolor'       => false, //array(255,255,255)
                                        'module_width'  => 1, // width of a single module in points
                                        'module_height' => 1 // height of a single module in points
                                    );

                                    $this->utilitarios(array(
                                        'tipo'    => '112',
                                        'code'    => $data1['url_pdf'],
                                        'type'    => 'QRCODE,L',
                                        'x'       => 8.2,
                                        'y'       => 3,
                                        'w'       => 25,
                                        'h'       => 25,
                                        'style'   => $style_qrcode,
                                        'align'   => '',
                                        'distort' => FALSE
                                    ));
                                });
                            // === FOOTER ===
                                PDF::setFooterCallback(function($pdf) use($data2){
                                    $style1 = array(
                                        'width' => 0.5,
                                        'cap'   => 'butt',
                                        'join'  => 'miter',
                                        'dash'  => '0',
                                        'phase' => 10,
                                        'color' => array(0, 0, 0)
                                    );

                                    $pdf->Line(10, 204, 320, 204, $style1);
                                    $pdf->SetY(-11);
                                    $pdf->SetFont("times", "I", 7);
                                    $pdf->Cell(155, 4, 'Fecha de emisión: ' . date("d/m/Y H:i:s", strtotime($data2['fh_actual'])), 0, 0, "L");
                                    $pdf->Cell(155, 4, "Página " . $pdf->getAliasNumPage() . "/" . $pdf->getAliasNbPages(), 0, 0, "R");
                                });

                            PDF::setPageUnit('mm');

                            PDF::SetMargins(10, 35.3, 10);
                            PDF::getAliasNbPages();
                            PDF::SetCreator('MINISTERIO PUBLICO');
                            PDF::SetAuthor('TRITON');
                            PDF::SetTitle($this->tipo_reporte['2']);
                            PDF::SetSubject('DOCUMENTO');
                            PDF::SetKeywords($this->tipo_reporte['2']);

                            PDF::SetAutoPageBreak(FALSE, 10);

                            // === BODY ===
                                PDF::AddPage('L', 'FOLIO');

                                $c    = 1;
                                $y    = 16.85;
                                $fill = FALSE;
                                PDF::SetFont("times", "", 2);
                                PDF::SetFillColor(204, 239, 252);

                                $ta1 = 6;
                                PDF::SetFont("times", "", $ta1);

                                foreach($consulta1->toArray() AS $row1)
                                {
                                    $i  = 0;
                                    $y1 = PDF::GetY();
                                    if ($y + $y1 > 204)
                                    {
                                        PDF::Cell(310, 1, "", "T", 0, "L");
                                        PDF::AddPage('L', 'FOLIO');
                                    }

                                    PDF::MultiCell($x1_array[$i++], $y, $c++, 1, "R", $fill, 0, "", "", true, 0, false, true, $y, "M");
                                    PDF::MultiCell($x1_array[$i++], $y, $row1['Caso'] . "\n", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");
                                    PDF::MultiCell($x1_array[$i++], $y, $row1['FechaDenuncia'] . "\n", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");
                                    PDF::MultiCell($x1_array[$i++], $y, $row1['denunciantes'] . "\n", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");
                                    PDF::MultiCell($x1_array[$i++], $y, $row1['denunciados'] . "\n", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");
                                    PDF::MultiCell($x1_array[$i++], $y, $row1['delito_principal'] . "\n", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");
                                    PDF::MultiCell($x1_array[$i++], $y, $row1['anteriores_fiscales'] . "\n", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");
                                    PDF::MultiCell($x1_array[$i++], $y, $row1['f_reparto'] . "\n" . $row1['fiscales'] . "\n", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");
                                    PDF::MultiCell($x1_array[$i++], $y, "", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");

                                    PDF::Ln();
                                    $fill = !$fill;
                                }

                            PDF::Output('reparto_caso_' . date("YmdHis") . '.pdf', 'I');
                            break;
                    }
                }
                else
                {
                    return "TIPO DE REPORTE no existe";
                }
                break;
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