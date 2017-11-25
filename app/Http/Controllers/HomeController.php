<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Intervention\Image\Facades\Image;

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
                        'iu'         => 1,
                        'error_sw'   => 1
                    );
                    $opcion = 'n';
                    $error  = FALSE;

                // === PERMISOS ===
                    if($id == '')
                    {
                        $respuesta['respuesta'] .= "No tiene información personal. Consulte con el Administrador de personal.";
                        return json_encode($respuesta);
                    }

                // === VALIDATE ===
                    try
                    {
                        $validator = $this->validate($request,[
                            'f_nacimiento'            => 'required|date',
                            'nombre'                  => 'required|max:50',
                            'ap_paterno'              => 'max:50',
                            'ap_materno'              => 'max:50',
                            'ap_esposo'               => 'max:50',
                            'estado_civil'            => 'required',
                            'municipio_id_nacimiento' => 'required',
                            'domicilio'               => 'required|max:500',
                            'telefono'                => 'max:50',
                            'celular'                 => 'required|max:50',
                            'municipio_id_residencia' => 'required'
                        ],
                        [
                            'f_nacimiento.required' => 'El campo FECHA DE NACIMIENTO es obligatorio.',
                            'f_nacimiento.date'    => 'El campo FECHA DE NACIMIENTO no corresponde con una fecha válida.',

                            'nombre.required' => 'El campo NOMBRE(S) es obligatorio.',
                            'nombre.max'      => 'El campo NOMBRE(S) debe ser :max caracteres como máximo.',


                            'ap_paterno.max'      => 'El campo APELLIDO PATERNO debe ser :max caracteres como máximo.',

                            'ap_materno.max'      => 'El campo APELLIDO MATERNO debe ser :max caracteres como máximo.',

                            'ap_esposo.max'      => 'El campo APELLIDO ESPOSO debe ser :max caracteres como máximo.',

                            'estado_civil.required' => 'El campo ESTADO CIVIL es obligatorio.',

                            'municipio_id_nacimiento.required' => 'El campo LUGAR DE NACIMIENTO es obligatorio.',

                            'domicilio.required' => 'El campo DOMICILIO es obligatorio.',
                            'domicilio.max'      => 'El campo DOMICILIO debe ser :max caracteres como máximo.',

                            'telefono.max'      => 'El campo TELEFONO debe ser :max caracteres como máximo.',

                            'celular.required' => 'El campo CELULAR es obligatorio.',
                            'celular.max'      => 'El campo CELULAR debe ser :max caracteres como máximo.',

                            'municipio_id_residencia.required' => 'El campo RESIDENCIA ACTUAL es obligatorio.'
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
                    $data['f_nacimiento']            = trim($request->input('f_nacimiento'));
                    $data['sexo']                    = trim($request->input('sexo'));
                    $data['nombre']                  = strtoupper($util->getNoAcentoNoComilla(trim($request->input('nombre'))));
                    $data['ap_paterno']              = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ap_paterno'))));
                    $data['ap_materno']              = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ap_materno'))));
                    $data['ap_esposo']               = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ap_esposo'))));
                    $data['estado_civil']            = trim($request->input('estado_civil'));
                    $data['municipio_id_nacimiento'] = trim($request->input('municipio_id_nacimiento'));
                    $data['domicilio']               = strtoupper($util->getNoAcentoNoComilla(trim($request->input('domicilio'))));
                    $data['telefono']                = trim($request->input('telefono'));
                    $data['celular']                 = trim($request->input('celular'));
                    $data['municipio_id_residencia'] = trim($request->input('municipio_id_residencia'));

                    // === CONVERTIR VALORES VACIOS A NULL ===
                        foreach ($data as $llave => $valor)
                        {
                            if ($valor == '')
                                $data[$llave] = NULL;
                        }

                    $iu                          = RrhhPersona::find($id);
                    $iu->municipio_id_nacimiento = $data['municipio_id_nacimiento'];
                    $iu->municipio_id_residencia = $data['municipio_id_residencia'];
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

                    $respuesta['respuesta'] .= "Su INFORMACION PERSONAL fue actualizada con éxito.";
                    $respuesta['sw']         = 1;
                    $respuesta['iu']         = 2;

                    $c_usuario = User::where('persona_id', '=', $id)->select("id")->first();
                    if(count($c_usuario) > 0)
                    {
                        $iu1       = User::find($c_usuario['id']);
                        $iu1->name = $data['nombre'];
                        $iu1->save();
                    }
                //=== respuesta ===
                return json_encode($respuesta);
                break;
            // === UPLOAD IMAGE ===
            case '2':
                // === SEGURIDAD ===
                    $id = Auth::user()->id;

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>SUBIR FOTOGRAFIA</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );
                    $opcion = 'n';

                // === PERMISOS ===
                    if($id == '')
                    {
                        $respuesta['respuesta'] .= "No se tiene información de su usuario. Consulte con el Administrador del Sistema.";
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    //=== IMAGEN UPLOAD ===
                        $user_imagen = User::where('id', '=', $id)
                            ->select('imagen')
                            ->first()
                            ->toArray();
                        if($user_imagen['imagen'] != '')
                        {
                            unlink(public_path($this->public_dir) . '/' . $user_imagen['imagen']);
                        }

                        if($request->hasFile('file'))
                        {
                            $archivo           = $request->file('file');
                            $nombre_archivo    = uniqid('user_', true) . '.' . $archivo->getClientOriginalExtension();
                            $direccion_archivo = public_path($this->public_dir);

                            $archivo->move($direccion_archivo, $nombre_archivo);

                            $image_user   = Image::make($direccion_archivo . '/' . $nombre_archivo);

                            $image_user->resize(512, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });

                            $image_user->resize(null, 512, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });

                            $image_user->save($direccion_archivo . '/' . $nombre_archivo);
                        }

                    $iu                    = User::find($id);
                    $iu->imagen            = $nombre_archivo;
                    $iu->save();

                    $respuesta['respuesta']      .= "La FOTOGRAFIA fue subida.";
                    $respuesta['sw']             = 1;
                    $respuesta['iu']             = 2;
                    $respuesta['nombre_archivo'] = $nombre_archivo;

                return json_encode($respuesta);
                break;
            // === INSERT UPDATE ===
            case '3':
                // === SEGURIDAD ===
                    $id = Auth::user()->id;
                // === LIBRERIAS ===
                    $util = new UtilClass();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>CAMBIO DE CONTRASEÑA</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );
                    $opcion = 'n';

                // === PERMISOS ===
                    if($id == '')
                    {
                        $respuesta['respuesta'] .= "No se tiene información de su usuario. Consulte con el Administrador del Sistema.";
                        return json_encode($respuesta);
                    }

                // === VALIDATE ===
                    try
                    {
                        $validator = $this->validate($request,[
                            'a_contrasenia' => 'required|min:6|max:16',
                            'contrasenia'   => 'required|min:6|max:16'
                        ],
                        [
                            'a_contrasenia.required' => 'El campo CONTRASEÑA ACTUAL es obligatorio.',
                            'a_contrasenia.min'      => 'El campo CONTRASEÑA ACTUAL debe tener al menos :min caracteres.',
                            'a_contrasenia.max'      => 'El campo CONTRASEÑA ACTUAL debe ser :max caracteres como máximo.',

                            'contrasenia.required' => 'El campo NUEVA CONTRASEÑA es obligatorio.',
                            'contrasenia.min'      => 'El campo NUEVA CONTRASEÑA debe tener al menos :min caracteres.',
                            'contrasenia.max'      => 'El campo NUEVA CONTRASEÑA debe ser :max caracteres como máximo.'
                        ]);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    if(Hash::check($request->input('a_contrasenia'), Auth::user()->password))
                    {
                        if($request->has('contrasenia'))
                        {
                            $password = trim($request->input('contrasenia'));

                            if(!preg_match('/(?=[a-z])/', $password))
                            {
                                $respuesta['respuesta'] .= "La NUEVA CONTRASEÑA debe contener al menos una minuscula.";
                                return json_encode($respuesta);
                            }

                            if(!preg_match('/(?=[A-Z])/', $password))
                            {
                                $respuesta['respuesta'] .= "La NUEVA CONTRASEÑA debe contener al menos una mayuscula.";
                                return json_encode($respuesta);
                            }

                            if(!preg_match('/(?=\d)/', $password))
                            {
                                $respuesta['respuesta'] .= "La NUEVA CONTRASEÑA debe contener al menos un digito.";
                                return json_encode($respuesta);
                            }

                            $password       = bcrypt($password);
                        }

                        $iu           = User::find($id);
                        $iu->password = $password;
                        $iu->save();

                        $respuesta['respuesta'] .= "Se cambio la CONTRASEÑA con éxito.";
                        $respuesta['sw']         = 1;
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "La CONTRASEÑA ACTUAL está incorrecto.";
                    }

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
            default:
                break;
        }
    }
}
