<?php

namespace App\Http\Controllers\Seguridad;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;
use App\Libraries\UtilClass;

use App\Models\Seguridad\SegPermisoRol;
use App\Models\Seguridad\SegRol;
use App\Models\Institucion\InstLugarDependencia;
use App\Models\Rrhh\RrhhPersona;
use App\User;

class UsuarioController extends Controller
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
        if(in_array(['codigo' => '0101'], $this->permisos))
        {
            $data = [
                'rol_id'                  => $this->rol_id,
                'permisos'                => $this->permisos,
                'title'                   => 'Usuarios',
                'home'                    => 'Inicio',
                'sistema'                 => 'Seguridad',
                'modulo'                  => 'Usuarios',
                'title_table'             => 'Usuarios',
                'estado_array'            => $this->estado,
                'lugar_dependencia_array' => InstLugarDependencia::where('estado', '=', 1)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray(),
                'rol_array'               => SegRol::where('estado', '=', 1)
                                            ->where('id', '<>', 1)
                                            ->select("id", "nombre")
                                            ->orderBy("nombre")
                                            ->get()
                                            ->toArray()
            ];
            return view('seguridad.usuario.usuario')->with($data);
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

                $tabla1 = "users";
                $tabla2 = "rrhh_personas";
                $tabla3 = "seg_roles";

                $select = "
                    $tabla1.id,
                    $tabla1.rol_id,
                    $tabla1.persona_id,
                    $tabla1.estado,
                    $tabla1.name,
                    $tabla1.imagen,
                    $tabla1.email,
                    $tabla1.lugar_dependencia,

                    a2.n_documento,
                    a2.nombre,
                    a2.ap_paterno,
                    a2.ap_materno,
                    a2.ap_esposo,

                    a3.nombre AS rol
                ";

                $array_where = "$tabla1.id <> 1";
                $array_where .= $jqgrid->getWhere();

                $count = User::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.persona_id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.rol_id")
                            ->whereRaw($array_where)
                            ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = User::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.persona_id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.rol_id")
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
                        'rol_id'     => $row["rol_id"],
                        'persona_id' => $row["persona_id"],
                        'persona_id' => $row["persona_id"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',
                        $row["imagen"],
                        $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                        $row["n_documento"],
                        $row["nombre"],
                        $row["ap_paterno"],
                        $row["ap_materno"],
                        $row["ap_esposo"],
                        $row["email"],
                        $row["rol"],
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
                        'titulo'     => '<div class="text-center"><strong>PERSONAS</strong></div>',
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
                        if(!in_array(['codigo' => '0103'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '0102'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                //=== OPERACION ===
                    $estado                  = trim($request->input('estado'));
                    $n_documento             = trim($request->input('n_documento'));
                    $n_documento_1           = strtoupper($util->getNoAcentoNoComilla(trim($request->input('n_documento_1'))));
                    $nombre                  = strtoupper($util->getNoAcentoNoComilla(trim($request->input('nombre'))));
                    $ap_paterno              = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ap_paterno'))));
                    $ap_materno              = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ap_materno'))));
                    $ap_esposo               = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ap_esposo'))));
                    $f_nacimiento            = trim($request->input('f_nacimiento'));
                    $estado_civil            = trim($request->input('estado_civil'));
                    $sexo                    = trim($request->input('sexo'));
                    $domicilio               = strtoupper($util->getNoAcentoNoComilla(trim($request->input('domicilio'))));
                    $telefono                = trim($request->input('telefono'));
                    $celular                 = trim($request->input('celular'));
                    $municipio_id_nacimiento = trim($request->input('municipio_id_nacimiento'));
                    $municipio_id_residencia = trim($request->input('municipio_id_residencia'));

                    if($n_documento_1 != '')
                    {
                        $n_documento .= '-' . $n_documento_1;
                    }

                    if($opcion == 'n')
                    {
                        $c_n_documento = RrhhPersona::where('n_documento', '=', $n_documento)->count();
                        if($c_n_documento < 1)
                        {
                            $iu                          = new RrhhPersona;
                            $iu->municipio_id_nacimiento = $municipio_id_nacimiento;
                            $iu->municipio_id_residencia = $municipio_id_residencia;
                            $iu->estado                  = $estado;
                            $iu->n_documento             = $n_documento;
                            $iu->nombre                  = $nombre;
                            $iu->ap_paterno              = $ap_paterno;
                            $iu->ap_materno              = $ap_materno;
                            $iu->ap_esposo               = $ap_esposo;
                            $iu->f_nacimiento            = $f_nacimiento;
                            $iu->estado_civil            = $estado_civil;
                            $iu->sexo                    = $sexo;
                            $iu->domicilio               = $domicilio;
                            $iu->telefono                = $telefono;
                            $iu->celular                 = $celular;
                            $iu->save();

                            $respuesta['respuesta'] .= "La PERSONA fue registrado con éxito.";
                            $respuesta['sw']         = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "La CEDULA DE IDENTIDAD ya fue registrada.";
                        }
                    }
                    else
                    {
                        $c_n_documento = RrhhPersona::where('n_documento', '=', $n_documento)->where('id', '<>', $id)->count();
                        if($c_n_documento < 1)
                        {
                            $iu                          = RrhhPersona::find($id);
                            $iu->municipio_id_nacimiento = $municipio_id_nacimiento;
                            $iu->municipio_id_residencia = $municipio_id_residencia;
                            $iu->estado                  = $estado;
                            $iu->n_documento             = $n_documento;
                            $iu->nombre                  = $nombre;
                            $iu->ap_paterno              = $ap_paterno;
                            $iu->ap_materno              = $ap_materno;
                            $iu->ap_esposo               = $ap_esposo;
                            $iu->f_nacimiento            = $f_nacimiento;
                            $iu->estado_civil            = $estado_civil;
                            $iu->sexo                    = $sexo;
                            $iu->domicilio               = $domicilio;
                            $iu->telefono                = $telefono;
                            $iu->celular                 = $celular;
                            $iu->save();

                            $respuesta['respuesta'] .= "La CEDULA DE IDENTIDAD se edito con éxito.";
                            $respuesta['sw']         = 1;
                            $respuesta['iu']         = 2;

                            $c_usuario = User::where('persona_id', '=', $id)->select("id")->first();
                            if(count($c_usuario) > 0)
                            {
                                $iu1       = User::find($c_usuario['id']);
                                $iu1->name = $nombre;
                                $iu1->save();
                            }
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "La CEDULA DE IDENTIDAD ya fue registrada.";
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
                    $estado     = trim($request->input('estado'));
                    $page_limit = trim($request->input('page_limit'));

                    $query = UbgeMunicipio::leftJoin("ubge_provincias", "ubge_provincias.id", "=", "ubge_municipios.provincia_id")
                                ->leftJoin("ubge_departamentos", "ubge_departamentos.id", "=", "ubge_provincias.departamento_id")
                                ->whereRaw("CONCAT_WS(', ', ubge_departamentos.nombre, ubge_provincias.nombre, ubge_municipios.nombre) ilike '%$nombre%'")
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
            // === SELECT2 PERSONA ===
            case '101':

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
