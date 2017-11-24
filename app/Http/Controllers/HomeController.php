<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;
use App\Libraries\UtilClass;

use App\Models\Seguridad\SegPermisoRol;
use App\Models\UbicacionGeografica\UbgeDepartamento;
use App\Models\UbicacionGeografica\UbgeMunicipio;
use App\Models\Rrhh\RrhhPersona;
use App\User;

class HomeController extends Controller
{
    private $estado_civil;
    private $sexo;

    private $rol_id;
    private $permisos;

    private $public_dir;
    private $public_url;

    public function __construct()
    {
        $this->middleware('auth');

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

        $this->public_dir = '/storage/seguridad/user/image';
        $this->public_url = 'storage/seguridad/user/image/';
    }

    public function index()
    {
        $this->rol_id   = Auth::user()->rol_id;
        $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                            ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                            ->select("seg_permisos.codigo")
                            ->get()
                            ->toArray();

        $id = Auth::user()->id;

        $tabla1 = "users";
        $tabla3 = "seg_roles";

        $select = "
            $tabla1.id,
            $tabla1.rol_id,
            $tabla1.persona_id,
            $tabla1.estado,
            $tabla1.name,
            $tabla1.imagen,
            $tabla1.email,

            a3.nombre AS rol
        ";

        $usuario_array = User::leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.rol_id")
                            ->where("$tabla1.id", "=", $id)
                            ->select(DB::raw($select))
                            ->first();

        if(($usuario_array['persona_id'] == NULL) || ($usuario_array['persona_id'] == ''))
        {
            $persona_array    = [];
            $persona_array_sw = FALSE;
        }
        else
        {
            $tabla1 = "rrhh_personas";
            $tabla2 = "ubge_municipios";
            $tabla3 = "ubge_provincias";
            $tabla4 = "ubge_departamentos";

            $select = "
                $tabla1.id,
                $tabla1.municipio_id_nacimiento,
                $tabla1.municipio_id_residencia,
                $tabla1.estado,
                $tabla1.n_documento,
                $tabla1.nombre,
                $tabla1.ap_paterno,
                $tabla1.ap_materno,
                $tabla1.ap_esposo,
                $tabla1.sexo,
                $tabla1.f_nacimiento,
                $tabla1.estado_civil,
                $tabla1.domicilio,
                $tabla1.telefono,
                $tabla1.celular,

                a2.nombre AS municipio_nacimiento,
                a2.provincia_id AS provincia_id_nacimiento,

                a3.nombre AS provincia_nacimiento,
                a3.departamento_id AS departamento_id_nacimiento,

                a4.nombre AS departamento_nacimiento,

                a5.nombre AS municipio_residencia,
                a5.provincia_id AS provincia_id_residencia,

                a6.nombre AS provincia_residencia,
                a6.departamento_id AS departamento_id_residencia,

                a7.nombre AS departamento_residencia
            ";
            $persona_array = RrhhPersona::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.municipio_id_nacimiento")
                                ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.provincia_id")
                                ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.departamento_id")
                                ->leftJoin("$tabla2 AS a5", "a5.id", "=", "$tabla1.municipio_id_residencia")
                                ->leftJoin("$tabla3 AS a6", "a6.id", "=", "a5.provincia_id")
                                ->leftJoin("$tabla4 AS a7", "a7.id", "=", "a6.departamento_id")
                                ->where("$tabla1.id", "=", $usuario_array['persona_id'])
                                ->select(DB::raw($select))
                                ->first();

            $persona_array_sw = TRUE;
        }

        $data = array(
            'rol_id'             => $this->rol_id,
            'permisos'           => $this->permisos,
            'title'              => 'Inicio',
            'home'               => 'Inicio',
            'sistema'            => 'Recursos Humanos',
            'modulo'             => 'Mi perfil',
            'estado_civil_array' => $this->estado_civil,
            'sexo_array'         => $this->sexo,
            'usuario_array'      => $usuario_array,
            'persona_array'      => $persona_array,
            'persona_array_sw'   => $persona_array_sw,
            'public_url'         => $this->public_url
        );
        return view('home')->with($data);
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
                    $id = Auth::user()->persona_id;
                // === LIBRERIAS ===
                    $util = new UtilClass();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>INFORMACION PERSONAL</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1
                    );
                    $opcion = 'n';
                    $error  = FALSE;

                // === PERMISOS ===
                    if($id == '')
                    {
                        $respuesta['respuesta'] .= "No tiene información personal. Consulte con el Administrador de personal";
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $data                            = [];
                    $data['estado']                  = trim($request->input('estado'));
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
                    $data['municipio_id_nacimiento'] = trim($request->input('municipio_id_nacimiento'));
                    $data['municipio_id_residencia'] = trim($request->input('municipio_id_residencia'));

                    $n_documento             = trim($request->input('n_documento'));
                    $n_documento_1           = strtoupper($util->getNoAcentoNoComilla(trim($request->input('n_documento_1'))));

                    if($n_documento_1 != '')
                    {
                        $n_documento .= '-' . $n_documento_1;
                    }

                    $data['n_documento'] = $n_documento;

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
                            $iu                          = new RrhhPersona;
                            $iu->municipio_id_nacimiento = $data['municipio_id_nacimiento'];
                            $iu->municipio_id_residencia = $data['municipio_id_residencia'];
                            $iu->estado                  = $data['estado'];
                            $iu->n_documento             = $data['n_documento'];
                            $iu->nombre                  = $data['nombre'];
                            $iu->ap_paterno              = $data['ap_paterno'];
                            $iu->ap_materno              = $data['ap_materno'];
                            $iu->ap_esposo               = $data['ap_esposo'];
                            $iu->f_nacimiento            = $data['f_nacimiento'];
                            $iu->estado_civil            = $data['estado_civil'];
                            $iu->sexo                    = $data['sexo'];
                            $iu->domicilio               = $data['domicilio'];
                            $iu->telefono                = $data['telefono'];
                            $iu->celular                 = $data['celular'];
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
                        $c_n_documento = RrhhPersona::where('n_documento', '=', $data['n_documento'])->where('id', '<>', $id)->count();
                        if($c_n_documento < 1)
                        {
                            $iu                          = RrhhPersona::find($id);
                            $iu->municipio_id_nacimiento = $data['municipio_id_nacimiento'];
                            $iu->municipio_id_residencia = $data['municipio_id_residencia'];
                            $iu->estado                  = $data['estado'];
                            $iu->n_documento             = $data['n_documento'];
                            $iu->nombre                  = $data['nombre'];
                            $iu->ap_paterno              = $data['ap_paterno'];
                            $iu->ap_materno              = $data['ap_materno'];
                            $iu->ap_esposo               = $data['ap_esposo'];
                            $iu->f_nacimiento            = $data['f_nacimiento'];
                            $iu->estado_civil            = $data['estado_civil'];
                            $iu->sexo                    = $data['sexo'];
                            $iu->domicilio               = $data['domicilio'];
                            $iu->telefono                = $data['telefono'];
                            $iu->celular                 = $data['celular'];
                            $iu->save();

                            $respuesta['respuesta'] .= "La PERSONA se edito con éxito.";
                            $respuesta['sw']         = 1;
                            $respuesta['iu']         = 2;

                            $c_usuario = User::where('persona_id', '=', $id)->select("id")->first();
                            if(count($c_usuario) > 0)
                            {
                                $iu1       = User::find($c_usuario['id']);
                                $iu1->name = $data['nombre'];
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
                }
                break;
            default:
                break;
        }
    }
}
