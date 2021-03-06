<?php

namespace App\Http\Controllers\Dpvt;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;
use App\Libraries\UtilClass;
use App\Models\Seguridad\SegPermisoRol;
use App\Models\UbicacionGeografica\UbgeMunicipio;
use App\Models\Institucion\InstInstitucion;
use App\Models\Dpvt\PvtDerivacion;
use App\Models\Rrhh\RrhhVisitante;
use App\Models\Rrhh\RrhhPersona;

use nusoap_client;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use PDF;
use function GuzzleHttp\json_encode;
use App\Models\UbicacionGeografica\UbgeDepartamento;

class DerivacionController extends Controller
{
    private $estado;
    private $estado_civil;
    private $sexo;
    private $tipo_reporte;

    private $rol_id;
    private $permisos;
    private $user_id;
    private $reporte_data_1;
    private $consulta_reporte_adm;

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

        $this->estado_civil = [
            '1' => 'CASADO(A)',
            '2' => 'DIVORCIADO(A)',
            '3' => 'SOLTERO(A)',
            '4' => 'UNION LIBRE',
            '5' => 'VIUDO(A)'
        ];

        $this->sexo = [
            'F' => 'FEMENINO',
            'M' => 'MASCULINO'
        ];

        $this->tipo_reporte = [
            '1' => 'REPORTE DERIVACIONES ATENDIDAS',
            '2' => 'REPORTE OLAP'
        ];

        $this->public_dir = '/image/logo';
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
        if(in_array(['codigo' => '2601'], $this->permisos))
        {
            $data = [
                'rol_id'                => $this->rol_id,
                'permisos'              => $this->permisos,
                'title'                 => 'Registro de derivacion',
                'home'                  => 'Inicio',
                'sistema'               => 'Derivación',
                'modulo'                => 'Registro de derivaciones',
                'title_table'           => 'Registro de personas atendidas, orientadas y derivadas a otras unidades',
                'estado_array'          => $this->estado,
                'estado_civil_array'    => $this->estado_civil,
                'sexo_array'            => $this->sexo,
                'tipo_reporte_array'    => $this->tipo_reporte
            ];
            return view('dpvt.derivacion.derivacion')->with($data);
        }
        else
        {
            return back()->withInput();
        }
    }

    public function view_jqgrid(Request $request)
    {
        if(!$request->ajax())
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
            // === TABLAS DE LA BD PARA CONSULTAR INFO PARA EL GRID ===
            $derivacion  = "pvt_derivaciones";
            $visitante   = "rrhh_visitantes";
            $persona     = "rrhh_personas";
            $institucion = "inst_instituciones";
            // === COLUMNAS DE LA CONSULTA ===
            $select = "
            $derivacion.id,
            $derivacion.estado,
            $derivacion.fecha,
            CONCAT_WS(' ', p.ap_paterno, p.ap_materno, p.nombre) as nombre,
            $derivacion.motivo,
            i.nombre as oficina,
            $derivacion.codigo,
            $derivacion.relato,
            i.id as idoficina";
            // === CONDICION POR DEFECTO ===
            $array_where = "$derivacion.user_id = " . Auth::user()->id;
            $array_where .= $jqgrid->getWhere();

            $count = PvtDerivacion::leftJoin("$visitante AS v", "v.id", "=", "$derivacion.visitante_id")
                ->leftJoin("$persona AS p", "p.id", "=", "v.persona_id")
                ->leftJoin("$institucion AS i", "i.id", "=", "$derivacion.institucion_id")
                ->whereRaw($array_where)
                ->count();

            $limit_offset = $jqgrid->getLimitOffset($count);
            // === CONSULTA ===
            $query = PvtDerivacion::leftJoin("$visitante AS v", "v.id", "=", "$derivacion.visitante_id")
                ->leftJoin("$persona AS p", "p.id", "=", "v.persona_id")
                ->leftJoin("$institucion AS i", "i.id", "=", "$derivacion.institucion_id")
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
                    'estado'     => $row["estado"],
                    'relato'     => $row["relato"],
                    'oficina_id' => $row["idoficina"]
                );

                $respuesta['rows'][$i]['id'] = $row["id"];
                $respuesta['rows'][$i]['cell'] = array(
                    '',
                    //$this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                    'MP-'.$row["codigo"],
                    $row["fecha"],
                    $row["nombre"],
                    $row["motivo"],
                    $row["oficina"],
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
                'titulo'    => 'REGISTROS DE DERIVACIONES',
                'respuesta' => 'No es solicitud AJAX.'
            ];
            return json_encode($respuesta);
        }

        $tipo = $request->input('tipo');

        switch($tipo)
        {
            // === INSERT UPDATE DERIVACIONES===
            case '1':
                $this->user_id  = Auth::user()->id;
                $this->rol_id   = Auth::user()->rol_id;
                $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                    ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                    ->select("seg_permisos.codigo")
                    ->get()
                    ->toArray();
                // === LIBRERIAS ===
                $util = new UtilClass();

                // === INICIALIZACION DE VARIABLES ===
                $respuesta = array(
                    'sw'         => 0,
                    'titulo'     => '<div class="text-center"><strong>REGISTRO DE DERIVACIONES</strong></div>',
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
                    if(!in_array(['codigo' => '2603'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                        return json_encode($respuesta);
                    }
                }
                else
                {
                    if(!in_array(['codigo' => '2602'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                        return json_encode($respuesta);
                    }
                }
                // === VALIDATE ===
                try
                {
                    $validator = $this->validate($request,[
                        'n_documento'             => 'required|max:20',
                        'n_documento_1'           => 'min:2|max:2',
                        'nombre'                  => 'required|max:50',
                        'ap_paterno'              => 'max:50',
                        'ap_materno'              => 'max:50',
                        'f_nacimiento'            => 'required|date',
                        'domicilio'               => 'max:500',
                        'celular'                 => 'required|max:15',
                        'municipio_id_nacimiento' => 'required',
                        'motivo'                  => 'required',
                        'relato'                  => 'required',
                        'institucion'             => 'required'
                    ],
                    [
                        'n_documento.required'             => 'El campo CEDULA DE IDENTIDAD es obligatorio',
                        'n_documento.max'                  => 'El campo CEDULA DE IDENTIDAD debe ser :max caracteres como máximo.',
                        'nombre.required'                  => 'El campo NOMBRE es obligatorio',
                        'nombre.max'                       => 'El campo NOMBRE debe tener :max caracteres como máximo',
                        'ap_paterno.max'                   => 'El campo APELLIDO PATERNO debe tener :max caracteres como máximo',
                        'ap_materno.max'                   => 'El campo APELLIDO MATERNO debe tener :max caracteres como máximo',
                        'f_nacimiento.required'            => 'El campo FECHA DE NACIMIENTO es obligatorio',
                        'celular.required'                 => 'El campo CELULAR es obligatorio',
                        'celular.max'                      => 'El campo CELULAR debe tener :max caracteres como máximo',
                        'municipio_id_nacimiento.required' => 'El campo LUGAR DE NACIMIENTO es obligatorio.',
                        'motivo.required'                  => 'El campo MOTIVO es obligatorio.',
                        'relato.required'                  => 'El campo RELATO es obligatorio.',
                        'institucion.required'             => 'El campo INSTITUCION es obligatorio.'
                    ]);
                }
                catch (Exception $e)
                {
                    $respuesta['error_sw'] = 2;
                    $respuesta['error']    = $e;
                    return json_encode($respuesta);
                }
                //=== OPERACION ===
                $data                            = [];
                $data['estado']                  = 1;
                $data['nombre']                  = strtoupper($util->getNoAcentoNoComilla(trim($request->input('nombre'))));
                $data['ap_paterno']              = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ap_paterno'))));
                $data['ap_materno']              = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ap_materno'))));
                $data['ap_esposo']               = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ap_esposo'))));
                $data['f_nacimiento']            = trim($request->input('f_nacimiento'));
                $data['estado_civil']            = trim($request->input('estado_civil'));
                $data['sexo']                    = trim($request->input('sexo'));
                $data['domicilio']               = strtoupper($util->getNoAcentoNoComilla(trim($request->input('domicilio'))));
                $data['telefono']                = trim($request->input('telefono'));
                $data['celular']                 = trim($request->input('celular'));
                $data['email']                   = strtolower($util->getNoAcentoNoComilla(trim($request->input('email'))));
                $data['municipio_id_nacimiento'] = trim($request->input('municipio_id_nacimiento'));
                $data['municipio_id_residencia'] = trim($request->input('municipio_id_residencia'));
                $data['motivo']                  = strtoupper($util->getNoAcentoNoComilla(trim($request->input('motivo'))));
                $data['relato']                  = $util->getNoAcentoNoComillaTextoLargo(trim($request->input('relato')));
                $data['institucion_id']          = trim($request->input('institucion'));

                $n_documento                     = trim($request->input('n_documento'));
                $n_documento_1                   = strtoupper($util->getNoAcentoNoComilla(trim($request->input('n_documento_1'))));

                if($n_documento_1 != '')
                    $n_documento .= '-' . $n_documento_1;

                $data['n_documento']             = $n_documento;

                // === CONVERTIR VALORES VACIOS A NULL ===
                foreach ($data as $llave => $valor)
                {
                    if ($valor == '')
                        $data[$llave] = NULL;
                }

                if($opcion == 'n')
                {
                    $c_n_documento = RrhhPersona::where('n_documento', '=', $data['n_documento'])->count();
                    if($c_n_documento < 1)
                    {
                        //======= INSERTAR PERSONA ========
                        $persona                          = new RrhhPersona;
                        $persona->municipio_id_nacimiento = $data['municipio_id_nacimiento'];
                        $persona->municipio_id_residencia = $data['municipio_id_residencia'];
                        $persona->estado                  = $data['estado'];
                        $persona->n_documento             = $data['n_documento'];
                        $persona->nombre                  = $data['nombre'];
                        $persona->ap_paterno              = $data['ap_paterno'];
                        $persona->ap_materno              = $data['ap_materno'];
                        $persona->ap_esposo               = $data['ap_esposo'];
                        $persona->f_nacimiento            = $data['f_nacimiento'];
                        $persona->estado_civil            = $data['estado_civil'];
                        $persona->sexo                    = $data['sexo'];
                        $persona->domicilio               = $data['domicilio'];
                        $persona->telefono                = $data['telefono'];
                        $persona->celular                 = $data['celular'];
                        $persona->save();
                        //======== INSERTAR VISITANTE =======
                        $visitante              = new RrhhVisitante;
                        $visitante->estado      = $data['estado'];
                        $visitante->email       = $data['email'];
                        $visitante->persona_id  = $persona->id;
                        $visitante->save();
                        //======== INSERTAR DERIVACION =======
                        $derivacion = new PvtDerivacion;
                        $derivacion->estado         = $data['estado'];
                        $derivacion->motivo         = $data['motivo'];
                        $derivacion->relato         = $data['relato'];
                        $derivacion->fecha          = date("Y-m-d");
                        $derivacion->institucion_id = $data['institucion_id'];
                        $derivacion->visitante_id   = $visitante->id;
                        $derivacion->user_id        = $this->user_id;
                        $ultimoCodigo               = DB::table('pvt_derivaciones')->max('codigo');
                        $derivacion->codigo         = $ultimoCodigo+1;
                        $derivacion->save();

                        $respuesta['respuesta'] .= "La DERIVACION fue registrada con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['der_id']     = $derivacion->id;
                    }
                    else
                    {
                        //$respuesta['respuesta'] .= "La CEDULA DE IDENTIDAD ya fue registrada.";
                    }
                }
                //=== respuesta ===
                // sleep(5);
                return json_encode($respuesta);
                break;
            // === INSERT SOLO VISITANTE Y DERIVACION ===
            case '2':
                $this->user_id  = Auth::user()->id;
                // === LIBRERIAS ===
                $util = new UtilClass();
                // === INICIALIZACION DE VARIABLES ===
                $respuesta = array(
                    'sw'         => 0,
                    'titulo'     => '<div class="text-center"><strong>ACTUALIZACIÓN DE DERIVACIÓN</strong></div>',
                    'respuesta'  => '',
                    'tipo'       => $tipo,
                    'iu'         => 1
                );
                // === VALIDATE ===
                try
                {
                    $validator = $this->validate($request,[
                        'n_documento'             => 'required|max:20',
                        'n_documento_1'           => 'min:2|max:2',
                        'nombre'                  => 'required|max:50',
                        'ap_paterno'              => 'max:50',
                        'ap_materno'              => 'max:50',
                        'f_nacimiento'            => 'required|date',
                        'domicilio'               => 'max:500',
                        'celular'                 => 'required|max:15',
                        'municipio_id_nacimiento' => 'required',
                        'motivo'                  => 'required',
                        'relato'                  => 'required',
                        'institucion'             => 'required'
                    ],
                    [
                        'n_documento.required' => 'El campo CEDULA DE IDENTIDAD es obligatorio',
                        'n_documento.max' => 'El campo CEDULA DE IDENTIDAD debe ser :max caracteres como máximo.',
                        'nombre.required' => 'El campo NOMBRE es obligatorio',
                        'nombre.max' => 'El campo NOMBRE debe tener :max caracteres como máximo',
                        'ap_paterno.max' => 'El campo APELLIDO PATERNO debe tener :max caracteres como máximo',
                        'ap_materno.max' => 'El campo APELLIDO MATERNO debe tener :max caracteres como máximo',
                        'f_nacimiento.required' => 'El campo FECHA DE NACIMIENTO es obligatorio',
                        'celular.required' => 'El campo CELULAR es obligatorio',
                        'celular.max' => 'El campo CELULAR debe tener :max caracteres como máximo',
                        'municipio_id_nacimiento.required' => 'El campo LUGAR DE NACIMIENTO es obligatorio.',
                        'motivo.required' => 'El campo MOTIVO es obligatorio.',
                        'relato.required' => 'El campo RELATO es obligatorio.',
                        'institucion.required' => 'El campo INSTITUCION es obligatorio.'
                    ]);
                }
                catch (Exception $e)
                {
                    $respuesta['error_sw'] = 2;
                    $respuesta['error']    = $e;
                    return json_encode($respuesta);
                }
                //=== OPERACION ===
                $data                   = [];
                $data['estado']         = 1;
                $data['motivo']         = strtoupper($util->getNoAcentoNoComilla(trim($request->input('motivo'))));
                $data['relato']         = $util->getNoAcentoNoComillaTextoLargo(trim($request->input('relato')));
                $data['institucion_id'] = trim($request->input('institucion'));
                $data['persona_id']     = trim($request->input('id'));

                $visitante = RrhhVisitante::where('persona_id', '=', $data['persona_id'])->first();
                $idvisitante = $visitante['id'];
                if ($visitante === null) {
                    $data['email']          = strtolower($util->getNoAcentoNoComilla(trim($request->input('email'))));
                    //======== INSERTAR VISITANTE =======
                    $visitante              = new RrhhVisitante;
                    $visitante->estado      = $data['estado'];
                    $visitante->email       = $data['email'];
                    $visitante->persona_id  = $data['persona_id'];
                    $visitante->save();
                    $idvisitante = $visitante->id;
                }

                //======== INSERTAR DERIVACION =======
                $derivacion = new PvtDerivacion;
                $derivacion->estado         = $data['estado'];
                $derivacion->motivo         = $data['motivo'];
                $derivacion->relato         = $data['relato'];
                $derivacion->fecha          = date("Y-m-d");
                $derivacion->institucion_id = $data['institucion_id'];
                $derivacion->visitante_id   = $idvisitante;
                $derivacion->user_id        = $this->user_id;
                $ultimoCodigo               = DB::table('pvt_derivaciones')->max('codigo');
                $derivacion->codigo         = $ultimoCodigo+1;
                $derivacion->save();

                $respuesta['respuesta'] .= "La DERIVACION fue registrada con éxito.";
                $respuesta['sw']         = 1;
                $respuesta['der_id']     = $derivacion->id;
                //=== respuesta ===
                return json_encode($respuesta);
                break;
            case '3':
                $this->rol_id   = Auth::user()->rol_id;
                $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                    ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                    ->select("seg_permisos.codigo")
                    ->get()
                    ->toArray();
                $id = trim($request->input('ed_derivacion_id'));
                // === LIBRERIAS ===
                $util = new UtilClass();
                if($id != '')
                {
                    if(!in_array(['codigo' => '2403'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                        return json_encode($respuesta);
                    }
                }
                // === INICIALIZACION DE VARIABLES ===
                $respuesta = array(
                    'sw'         => 0,
                    'titulo'     => '<div class="text-center"><strong>REGISTRO DE DERIVACIONES</strong></div>',
                    'respuesta'  => '',
                    'tipo'       => $tipo,
                    'iu'         => 1
                );
                // === VALIDATE ===
                try
                {
                    $validator = $this->validate($request,[
                        'ed_motivo'      => 'required',
                        'ed_relato'      => 'required',
                        'ed_institucion_id' => 'required'
                    ],
                    [
                        'ed_motivo.required' => 'El campo MOTIVO es obligatorio.',
                        'ed_motivo.required' => 'El campo RELATO es obligatorio.',
                        'ed_institucion_id.required' => 'El campo OFICINA es obligatorio.'
                    ]);
                }
                catch (Exception $e)
                {
                    $respuesta['error_sw'] = 2;
                    $respuesta['error']    = $e;
                    return json_encode($respuesta);
                }
                // === OPERACION ===
                $data               = [];
                $data['motivo']     = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ed_motivo'))));
                $data['relato']     = $util->getNoAcentoNoComillaTextoLargo(trim($request->input('ed_relato')));
                $data['oficina_id'] = trim($request->input('ed_institucion_id'));
                // === EDITAR DERIVACION ====
                $derivacion = PvtDerivacion::find($id);
                $derivacion->motivo         = $data['motivo'];
                $derivacion->relato         = $data['relato'];
                $derivacion->institucion_id = $data['oficina_id'];
                $derivacion->save();

                $respuesta['respuesta'] .= "La DERIVACIÓN se editó con éxito.";
                $respuesta['sw']         = 1;
                $respuesta['iu']         = 2;
                //=== respuesta ===
                return json_encode($respuesta);
                break;
            case '100':
                if($request->has('q'))
                {
                    $nombre     = $request->input('q');
                    $estado     = trim($request->input('estado'));
                    $page_limit = trim($request->input('page_limit'));

                    $query = UbgeMunicipio::leftJoin("ubge_provincias", "ubge_provincias.id", "=", "ubge_municipios.provincia_id")
                        ->leftJoin("ubge_departamentos", "ubge_departamentos.id", "=", "ubge_provincias.departamento_id")
                        ->whereRaw("CONCAT_WS(', ', ubge_departamentos.nombre, ubge_provincias.nombre, ubge_municipios.nombre) ilike '%$nombre%'")
                        ->where("ubge_municipios.estado", "=", $estado)
                        ->select(DB::raw("ubge_municipios.id, CONCAT_WS(', ', ubge_departamentos.nombre, ubge_provincias.nombre, ubge_municipios.nombre) AS text"))
                        ->orderByRaw("ubge_municipios.codigo ASC")
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
            case '200':
                if($request->has('q'))
                {
                    $nombre     = $request->input('q');
                    $estado     = trim($request->input('estado'));
                    $page_limit = trim($request->input('page_limit'));

                    $query = InstInstitucion::leftJoin("ubge_municipios AS m", "m.id", "=", "inst_instituciones.ubge_municipios_id")
                        ->leftJoin("ubge_provincias AS p", "p.id", "=", "m.provincia_id")
                        ->leftJoin("ubge_departamentos AS d", "d.id", "=", "p.departamento_id")
                        ->leftJoin("inst_lugares_dependencia AS l", "l.ubge_departamentos_id", "=", "d.id")
                        ->leftJoin("seg_ld_users AS s", "s.lugar_dependencia_id", "=", "l.id")
                        ->whereRaw("inst_instituciones.institucion_id is not null and inst_instituciones.nombre ilike '%$nombre%' and s.user_id = " . Auth::user()->id)
                        ->select(DB::raw("inst_instituciones.id, inst_instituciones.nombre AS text"))
                        ->orderByRaw("inst_instituciones.nombre ASC")
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
            case '300':
                $respuesta = array(
                    'sw'         => 0,
                    'titulo'     => '<div class="text-center"><strong>REGISTRO DE DERIVACIONES</strong></div>',
                    'respuesta'  => '',
                    'tipo'       => $tipo
                );
                if ($request->has('q'))
                {
                    $ndocumento = $request->input('q');
                    $persona_array = RrhhPersona::leftJoin("rrhh_visitantes AS v", "v.persona_id", "=", "rrhh_personas.id")
                        ->where('n_documento', '=', $ndocumento)
                        ->select("rrhh_personas.id","nombre","ap_paterno","ap_materno","ap_esposo","sexo","f_nacimiento","estado_civil","domicilio","telefono","celular","municipio_id_nacimiento","municipio_id_residencia","v.email")
                        ->first();
                    if (!($persona_array === null))
                    {
                        $respuesta["results"] = $persona_array;
                        $respuesta["sw"] = 1;
                    }
                    else
                    {
                        $respuesta["respuesta"] = "La CEDULA DE IDENTIDAD no se encontró.<br>Por favor registre la nueva información.";
                    }
                }
                else
                {
                    $respuesta["respuesta"] = "El campo CEDULA DE IDENTIDAD es obligatorio";
                }
                return json_encode($respuesta);
                break;
            case '400':
                if($request->has('q'))
                {
                    $nombre     = $request->input('q');
                    $page_limit = trim($request->input('page_limit'));

                    $query = InstInstitucion::whereRaw("inst_instituciones.institucion_id is not null and inst_instituciones.nombre ilike '%$nombre%'")
                        ->select(DB::raw("inst_instituciones.id, inst_instituciones.nombre AS text"))
                        ->orderByRaw("inst_instituciones.nombre ASC")
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
            case '500':
                if($request->has('q'))
                {
                    $nombre     = $request->input('q');
                    $estado     = trim($request->input('estado'));
                    $page_limit = trim($request->input('page_limit'));

                    $query = UbgeDepartamento::whereRaw("ubge_departamentos.nombre ilike '%$nombre%'")
                        ->where("ubge_departamentos.estado", "=", $estado)
                        ->select(DB::raw("ubge_departamentos.id, ubge_departamentos.nombre AS text"))
                        ->orderByRaw("ubge_departamentos.nombre ASC")
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
                $idderivacion = $request->input('id');
                $fh_actual            = date("Y-m-d H:i:s");
                $dir_logo_institucion = public_path($this->public_dir) . '/' . 'logo_fge_256_2018_3.png';
                // === VALIDAR IMAGENES ===
                if(!file_exists($dir_logo_institucion))
                {
                    return "No existe el logo de la institución " . $dir_logo_institucion;
                }
                // === CONSULTA A LA BASE DE DATOS ===
                // === TABLAS PARA LA CONSULTA ===
                $derivacion  = "pvt_derivaciones";
                $visitante   = "rrhh_visitantes";
                $persona     = "rrhh_personas";
                $institucion = "inst_instituciones";
                $municipio   = "ubge_municipios";
                // === COLUMNAS DE LA CONSULTA ===
                $select = "$persona.n_documento,$persona.nombre,$persona.ap_paterno,$persona.ap_materno,$persona.sexo,$persona.domicilio,$persona.telefono as telfpersona,$persona.celular as celpersona,d.codigo,d.motivo,d.relato,d.fecha,i.nombre as oficina,i.respcontacto,i.telefono as telfinst,i.celular as celinst,i.direccion,i.zona,i.email,m.nombre as municipio";
                // === CONSULTA ===
                $consulta = RrhhPersona::join("$visitante AS v", "v.persona_id", "=", "$persona.id")
                    ->join("$derivacion AS d", "d.visitante_id", "=", "v.id")
                    ->join("$institucion AS i", "i.id", "=", "d.institucion_id")
                    ->join("$municipio AS m", "m.id", "=", "i.ubge_municipios_id")
                    ->where("d.id", "=", $idderivacion)
                    ->select(DB::raw($select))
                    ->first();

                // === CARGAR VALORES ===
                $x1_array = [8,50,50,50,20,32,20,50,100,32,32,20,32,25,20,32];

                $data1 = array(
                    'dir_logo_institucion' => $dir_logo_institucion,
                    'x1_array'             => $x1_array,
                );

                $data2 = array(
                    'fh_actual' => $fh_actual
                );

                // === HEADER ===
                PDF::setHeaderCallback(function($pdf) use($data1, $consulta) {
                    $pdf->Image($data1['dir_logo_institucion'], 180, 3, 0, 23, 'PNG');

                    $pdf->Ln(7);
                    $pdf->SetFont('times', 'B', 22);
                    $pdf->Write(0, 'MINISTERIO PÚBLICO', '', 0, 'C', true, 0, false, false, 0);

                    $pdf->SetFont('times', 'B', 18);
                    $pdf->Write(0, 'REPORTE DERIVACIÓN', '', 0, 'C', true, 0, false, false, 0);

                    $pdf->SetFont('times', 'B', 12);
                    $pdf->Write(0, 'CÓDIGO: MP - '.$consulta['codigo'].' | FECHA: '.date("d/m/Y", strtotime($consulta['fecha'])), '', 0, 'C', true, 0, false, false, 0);
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

                    $pdf->Line(10, 268, 206, 268, $style1);
                    $pdf->SetY(-11);
                    $pdf->SetFont("times", "I", 7);
                    $pdf->Cell(65.3, 4, 'Fecha de emisión: ' . date("d/m/Y H:i:s", strtotime($data2['fh_actual'])), 0, 0, "L");
                    $pdf->Cell(65.3, 4, 'Usuario: ' . substr(Auth::user()->email, 0, strpos(Auth::user()->email, '@')), 0, 0, "C");
                    $pdf->Cell(65.4, 4, "Página " . $pdf->getAliasNumPage() . "/" . $pdf->getAliasNbPages(), 0, 0, "R");
                });

                PDF::setPageUnit('mm');

                PDF::SetMargins(10, 25, 10);
                PDF::getAliasNbPages();
                PDF::SetCreator('MINISTERIO PUBLICO');
                PDF::SetAuthor('TRITON');
                PDF::SetTitle('REPORTE DE DERIVACION');
                PDF::SetSubject('DOCUMENTO');

                PDF::SetAutoPageBreak(FALSE, 10);

                // === BODY ===
                PDF::AddPage('P', 'LETTER');

                PDF::SetFillColor(204, 239, 252);
                $ta1 = 12;
                PDF::Ln(10);
                PDF::SetFont("times", "B", $ta1);
                PDF::Cell(0, 0, 'DATOS PERSONALES', 1, false, 'L', 0, '', 0, false, 'M', 'M');
                PDF::Ln(8);
                PDF::SetFont("times", "", $ta1);
                PDF::Cell(40, 0, 'CI: '.$consulta['n_documento'], 0, false, 'L', 0, '', 0, false, 'M', 'M');
                PDF::Cell(146, 0, 'Nombre Completo: '.$consulta['nombre'].' '.$consulta['ap_paterno'].' '.$consulta['ap_materno'], 0, false, 'L', 0, '', 0, false, 'M', 'M');
                PDF::Ln(6);
                if ($consulta['sexo'] == 'M') PDF::Cell(40, 0, 'Sexo: Masculino', 0, false, 'L', 0, '', 0, false, 'M', 'M');
                else PDF::Cell(40, 0, 'Sexo: Femenino', 0, false, 'L', 0, '', 0, false, 'M', 'M');
                PDF::Cell(90, 0, 'Domicilio: '.$consulta['domicilio'], 0, false, 'L', 0, '', 0, false, 'M', 'M');
                PDF::Cell(36, 0, 'Telf.: '.$consulta['telfpersona'], 0, false, 'L', 0, '', 0, false, 'M', 'M');
                PDF::Cell(20, 0, 'Cel.: '.$consulta['celpersona'], 0, false, 'L', 0, '', 0, false, 'M', 'M');
                PDF::Ln(10);
                PDF::SetFont("times", "B", $ta1);
                PDF::Cell(0, 0, 'MOTIVO DE CONSULTA', 1, false, 'L', 0, '', 0, false, 'M', 'M');
                PDF::SetFont("times", "", $ta1);
                PDF::writeHTML('MOTIVO: '.$consulta['motivo'], true, false, true, true, '');
                PDF::writeHTML('RELATO: '.$consulta['relato'].'<br>', true, false, true, true, 'J');
                PDF::SetFont("times", "B", $ta1);
                PDF::Cell(0, 0, 'OFICINA DERIVADA', 1, false, 'L', 0, '', 0, false, 'M', 'M');
                PDF::Ln(8);
                PDF::SetFont("times", "", $ta1);
                PDF::Cell(196, 0, 'OFICINA: '.$consulta['oficina'], 0, false, 'L', 0, '', 0, false, 'M', 'M');
                PDF::Ln(6);
                PDF::Cell(136, 0, 'RESPONSABLE: '.$consulta['respcontacto'], 0, false, 'L', 0, '', 0, false, 'M', 'M');
                PDF::Ln(6);
                PDF::Cell(98, 0, 'Telf.: '.$consulta['telfinst'], 0, false, 'L', 0, '', 0, false, 'M', 'M');
                PDF::Cell(98, 0, 'Cel.: '.$consulta['celinst'], 0, false, 'L', 0, '', 0, false, 'M', 'M');
                PDF::Ln(6);
                PDF::Cell(196, 0, 'Correo Electrónico: '.$consulta['email'], 0, false, 'L', 0, '', 0, false, 'M', 'M');
                PDF::Ln(6);
                PDF::Cell(196, 0, 'Dirección: '.$consulta['direccion'], 0, false, 'L', 0, '', 0, false, 'M', 'M');
                PDF::Ln(6);
                PDF::Cell(120, 0, 'Zona: '.$consulta['zona'], 0, false, 'L', 0, '', 0, false, 'M', 'M');
                PDF::Cell(76, 0, 'Municipio: '.$consulta['municipio'], 0, false, 'L', 0, '', 0, false, 'M', 'M');

                PDF::Output('reporte_derivacion_' . date("YmdHis") . '.pdf', 'I');
                break;
            case '2':
                $tipo_reporte = $request->input('tipo_reporte_id');
                switch($tipo_reporte)
                {
                    case '1':
                        $oficina_derivada_id  = $request->input('oficina_derivada_id') != Null ? $request->input('oficina_derivada_id') : 0;
                        if ($request->input('fecha_del') == Null) {
                            $primera_derivacion = PvtDerivacion::orderBy('id', 'asc')->limit(1)->first();
                            $fecha_inicio = $primera_derivacion->fecha;
                        } else $fecha_inicio = $request->input('fecha_del');
                        $fecha_del            = $fecha_inicio;
                        if ($request->input('fecha_al') == Null) {
                            $ultima_derivacion = PvtDerivacion::orderBy('id', 'desc')->limit(1)->first();
                            $fecha_fin = $ultima_derivacion->fecha;
                        } else $fecha_fin = $request->input('fecha_al');
                        $fecha_al             = $fecha_fin;
                        $fh_actual            = date("Y-m-d H:i:s");
                        $dir_logo_institucion = public_path($this->public_dir) . '/' . 'logo_fge_256_2018_3.png';
                        // === VALIDAR IMAGENES ===
                        if(!file_exists($dir_logo_institucion))
                        {
                            return "No existe el logo de la institución " . $dir_logo_institucion;
                        }
                        // === CONSULTA A LA BASE DE DATOS ===
                        // === TABLAS PARA LA CONSULTA ===
                        $derivacion  = "pvt_derivaciones";
                        $visitante   = "rrhh_visitantes";
                        $persona     = "rrhh_personas";
                        $institucion = "inst_instituciones";
                        // === COLUMNAS DE LA CONSULTA ===
                        $select = "
                        $derivacion.codigo,
                        $derivacion.fecha,
                        CONCAT_WS(' ', p.ap_paterno, p.ap_materno, p.nombre) as nombre,
                        $derivacion.motivo,
                        i.nombre as oficina";
                        // === CONDICION POR DEFECTO ===
                        $array_where = "$derivacion.user_id = " . Auth::user()->id;
                        // === CONSULTA ===
                        $consulta = PvtDerivacion::join("$institucion AS i", "i.id", "=", "$derivacion.institucion_id")
                            ->join("$visitante AS v", "v.id", "=", "$derivacion.visitante_id")
                            ->join("$persona AS p", "p.id", "=", "v.persona_id")
                            ->whereRaw($array_where)
                            ->whereRaw("case when ".$oficina_derivada_id."<>0 then i.id=".$oficina_derivada_id." else $derivacion.institucion_id in (select id from $institucion where institucion_id is not null) end and $derivacion.fecha between '".$fecha_del."'::date and '".$fecha_al."'::date")
                            ->select(DB::raw($select))
                            ->get();
                        // === CARGAR VALORES ===
                        $x1_array = [15,30,30,70,70,44];

                        $data1 = array(
                            'dir_logo_institucion' => $dir_logo_institucion,
                            'x1_array'             => $x1_array,
                        );

                        $data2 = array(
                            'fh_actual' => $fh_actual
                        );
                        // === HEADER ===
                        PDF::setHeaderCallback(function($pdf) use($data1) {
                            $pdf->Image($data1['dir_logo_institucion'], 230, 3, 0, 23, 'PNG');

                            $pdf->Ln(7);
                            $pdf->SetFont('times', 'B', 22);
                            $pdf->Write(0, 'MINISTERIO PÚBLICO', '', 0, 'C', true, 0, false, false, 0);

                            $pdf->SetFont('times', 'B', 18);
                            $pdf->Write(0, 'DERIVACIONES', '', 0, 'C', true, 0, false, false, 0);
                            $pdf->Ln(2.5);
                            $pdf->SetFillColor(211, 200, 206);
                            $pdf->SetFont("times", "B", 7);
                            $y=8;
                            $i= 0;
                            $pdf->MultiCell($data1['x1_array'][$i++], $y, "N°", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                            $pdf->MultiCell($data1['x1_array'][$i++], $y, "CÓDIGO", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                            $pdf->MultiCell($data1['x1_array'][$i++], $y, "FECHA", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                            $pdf->MultiCell($data1['x1_array'][$i++], $y, "NOMBRE", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                            $pdf->MultiCell($data1['x1_array'][$i++], $y, "MOTIVO", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
                            $pdf->MultiCell($data1['x1_array'][$i++], $y, "OFICINA", 1, "C", 1, 0, "", "", true, 0, false, true, $y, "M");
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

                            $pdf->Line(10, 268, 206, 268, $style1);
                            $pdf->SetY(-11);
                            $pdf->SetFont("times", "I", 7);
                            $pdf->Cell(86.3, 4, 'Fecha de emisión: ' . date("d/m/Y H:i:s", strtotime($data2['fh_actual'])), 0, 0, "L");
                            $pdf->Cell(86.3, 4, 'Usuario: ' . substr(Auth::user()->email, 0, strpos(Auth::user()->email, '@')), 0, 0, "C");
                            $pdf->Cell(86.4, 4, "Página " . $pdf->getAliasNumPage() . "/" . $pdf->getAliasNbPages(), 0, 0, "R");
                        });
                        PDF::setPageUnit('mm');

                        PDF::SetMargins(10, 35, 10);
                        PDF::getAliasNbPages();
                        PDF::SetCreator('MINISTERIO PUBLICO');
                        PDF::SetAuthor('TRITON');
                        PDF::SetTitle('DERIVACIONES');
                        PDF::SetSubject('DOCUMENTO');
                        PDF::SetAutoPageBreak(FALSE, 10);
                        // === BODY ===
                        PDF::AddPage('L', 'LETTER');
                        $c    = 1;
                        $y    = 10;
                        $fill = FALSE;
                        PDF::SetFillColor(204, 239, 252);

                        $ta1 = 8;
                        PDF::SetFont("times", "", $ta1);
                        foreach($consulta->toArray() AS $row)
                        {
                            $i  = 0;
                            $y1 = PDF::GetY();
                            if ($y + $y1 > 201)
                            {
                                PDF::Cell(310, 1, "", "T", 0, "L");
                                PDF::AddPage('L', 'LETTER');
                            }
                            PDF::MultiCell($x1_array[$i++], $y, $c++, 1, "L", $fill, 0, "", "", true, 0, false, true, $y, "M");
                            PDF::MultiCell($x1_array[$i++], $y, "MP-".$row['codigo'] . "\n", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");
                            PDF::MultiCell($x1_array[$i++], $y, $row['fecha'] . "\n", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");
                            PDF::MultiCell($x1_array[$i++], $y, $row['nombre'] . "\n", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");
                            PDF::MultiCell($x1_array[$i++], $y, $row['motivo'] . "\n", 1, "L", $fill, 0, "", "", true, 0, false, true, $y, "M");
                            PDF::MultiCell($x1_array[$i++], $y, $row['oficina'] . "\n", 1, "C", $fill, 0, "", "", true, 0, false, true, $y, "M");
                            PDF::Ln();
                            $fill = !$fill;
                        }
                        PDF::Output('derivaciones_' . date("YmdHis") . '.pdf', 'I');
                        break;
                    case '2':
                        if ($request->input('rep_fecha_del') == Null) {
                            $primera_derivacion = PvtDerivacion::orderBy('id', 'asc')->limit(1)->first();
                            $fecha_inicio = $primera_derivacion->fecha;
                        } else $fecha_inicio = $request->input('rep_fecha_del');
                        $fecha_del = $fecha_inicio;
                        if ($request->input('rep_fecha_al') == Null) {
                            $ultima_derivacion = PvtDerivacion::orderBy('id', 'desc')->limit(1)->first();
                            $fecha_fin = $ultima_derivacion->fecha;
                        } else $fecha_fin = $request->input('rep_fecha_al');
                        $fecha_al  = $fecha_fin;
                        $this->reporte_data_1 = [
                            'departamento_id'     => $request->input('rep_departamento_id')     != Null ? $request->input('rep_departamento_id') : 0,
                            'municipio_id'        => $request->input('rep_municipio_id')        != Null ? $request->input('rep_municipio_id') : 0,
                            'oficina_derivada_id' => $request->input('rep_oficina_derivada_id') != Null ? $request->input('rep_oficina_derivada_id') : 0,
                            'fecha_del'           => $fecha_del,
                            'fecha_al'            => $fecha_al
                        ];
                        Excel::create('Derivaciones_' . date('Y-m-d_H-i-s'), function($excel){
                            // === CONSULTA A LA BASE DE DATOS ===
                            // === TABLAS PARA LA CONSULTA ===
                            $derivacion   = "pvt_derivaciones";
                            $institucion  = "inst_instituciones";
                            $municipio    = "ubge_municipios";
                            $provincia    = "ubge_provincias";
                            $departamento = "ubge_departamentos";
                            $visitante    = "rrhh_visitantes";
                            $persona      = "rrhh_personas";
                            // === COLUMNAS DE LA CONSULTA ===
                            $select = "
                            $derivacion.codigo,
                            $derivacion.fecha,
                            CONCAT_WS(' ', r.ap_paterno, r.ap_materno, r.nombre) as nombre,
                            $derivacion.motivo,
                            i.nombre as oficina";
                            // === CONDICION POR DEFECTO ===
                            $array_where = "$derivacion.user_id = " . Auth::user()->id;
                            // === CONSULTA ===
                            $this->consulta_reporte_adm = PvtDerivacion::join("$institucion AS i", "i.id", "=", "$derivacion.institucion_id")
                                ->join("$municipio AS m", "m.id", "=", "i.ubge_municipios_id")
                                ->join("$provincia AS p", "p.id", "=", "m.provincia_id")
                                ->join("$departamento AS e", "e.id", "=", "p.departamento_id")
                                ->join("$visitante AS v", "v.id", "=", "$derivacion.visitante_id")
                                ->join("$persona AS r", "r.id", "=", "v.persona_id")
                                ->whereRaw($array_where)
                                ->whereRaw("(case when ".$this->reporte_data_1['departamento_id']."<>0 then e.id = ".$this->reporte_data_1['departamento_id']." else e.id is not null end) and (case when ".$this->reporte_data_1['municipio_id']."<> 0 then m.id = ".$this->reporte_data_1['municipio_id']." else m.id is not null end) and (case when ".$this->reporte_data_1['oficina_derivada_id']."<>0 then i.id = ".$this->reporte_data_1['oficina_derivada_id']." else i.id is not null end) and $derivacion.fecha between '".$this->reporte_data_1['fecha_del']."'::date and '".$this->reporte_data_1['fecha_al']."'::date")
                                ->select(DB::raw($select))
                                ->orderBy("$derivacion.fecha", 'ASC')
                                ->get()
                                ->toArray();
                            $excel->sheet('Derivaciones', function($sheet){
                                $sheet->row(1, []);
                                $sheet->row(2, [
                                    'CODIGO',
                                    'FECHA',
                                    'NOMBRE',
                                    'MOTIVO',
                                    'OFICINA DERIVADA'
                                ]);
                                $sheet->row(1, function($row){
                                    $row->setBackground('#CCCCCC');
                                    $row->setFontWeight('bold');
                                    $row->setAlignment('center');
                                });
                                $sheet->freezeFirstRow();
                                $sheet->setAutoFilter();

                                $sheet->setColumnFormat([
                                    'B' => 'dd-mm-yyyy'
                                ]);

                                $sw = FALSE;

                                foreach($this->consulta_reporte_adm as $index => $row1)
                                {
                                    $sheet->row($index+2, [
                                        "MP-".$row1["codigo"],
                                        $row1["fecha"],
                                        $row1["nombre"],
                                        $row1["motivo"],
                                        $row1["oficina"]
                                    ]);
                                    if($sw)
                                    {
                                        $sheet->row($index+2, function($row){
                                            $row->setBackground('#deeaf6');
                                        });
                                        $sw = FALSE;
                                    }
                                    else
                                    {
                                        $sw = TRUE;
                                    }
                                }
                                $sheet->setAutoSize(true);
                            });
                        })->export('xlsx');
                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }
    }
}
