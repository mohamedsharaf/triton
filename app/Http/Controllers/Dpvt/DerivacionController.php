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
use Exception;
use function GuzzleHttp\json_encode;

class DerivacionController extends Controller
{
    private $estado;
    private $estado_civil;
    private $sexo;

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
        if($this->rol_id == 1)
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
                'sexo_array'            => $this->sexo
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
            $derivacion = "pvt_derivaciones";
            $visitante = "rrhh_visitantes";
            $persona = "rrhh_personas";
            $institucion = "inst_instituciones";
            // === COLUMNAS DE LA CONSULTA ===
            $select = "
            $derivacion.id,
            $derivacion.estado,
            $derivacion.fecha,
            p.ap_paterno||' '||case when p.ap_materno is null then '' else p.ap_materno end||' '||p.nombre as nombre,
            $derivacion.motivo,
            i.nombre as oficina
            ";
            // === CONDICION POR DEFECTO ===
            $array_where = "TRUE";
            $array_where .= $jqgrid->getWhere();

            $count = PvtDerivacion::whereRaw($array_where)->count();

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
                    'estado'=> $row["estado"]
                );

                $respuesta['rows'][$i]['id'] = $row["id"];
                $respuesta['rows'][$i]['cell'] = array(
                    '',
                    //$this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
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
                    //$data1['updated_at'] = $f_modificacion;
                }
                else
                {
                // $data1['created_at'] = $f_modificacion;
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
                        'email'                   => 'required|email',
                        //'password'          => 'min:6|max:16',
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
                        'email.required' => 'El campo CORREO ELECTRONICO es obligatorio.',
                        'email.email'    => 'El campo CORREO ELECTRONICO no corresponde con una dirección de e-mail válida.',
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
                $data['relato']                  = strtoupper($util->getNoAcentoNoComilla(trim($request->input('relato'))));
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
                        $derivacion->visitante_id = $visitante->id;
                        $derivacion->save();

                        $respuesta['respuesta'] .= "La DERIVACION fue registrada con éxito.";
                        $respuesta['sw']         = 1;
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
                        'email'                   => 'required|email',
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
                        'email.required' => 'El campo CORREO ELECTRONICO es obligatorio.',
                        'email.email'    => 'El campo CORREO ELECTRONICO no corresponde con una dirección de e-mail válida.',
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
                $data['relato']         = strtoupper($util->getNoAcentoNoComilla(trim($request->input('relato'))));
                $data['institucion_id'] = trim($request->input('institucion'));
                $data['persona_id']     = trim($request->input('id'));

                $visitante = RrhhVisitante::where('persona_id', '=', $data['persona_id'])->first();

                //======== INSERTAR DERIVACION =======
                $derivacion = new PvtDerivacion;
                $derivacion->estado         = $data['estado'];
                $derivacion->motivo         = $data['motivo'];
                $derivacion->relato         = $data['relato'];
                $derivacion->fecha          = date("Y-m-d");
                $derivacion->institucion_id = $data['institucion_id'];
                $derivacion->visitante_id   = $visitante['id'];
                $derivacion->save();

                $respuesta['respuesta'] .= "La DERIVACION fue registrada con éxito.";
                $respuesta['sw']         = 1;
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
