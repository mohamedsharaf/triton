<?php

namespace App\Http\Controllers\Seguridad;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

use Intervention\Image\Facades\Image;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;
use App\Libraries\UtilClass;

use App\Models\Seguridad\SegPermisoRol;
use App\Models\Seguridad\SegGrupo;
use App\Models\Seguridad\SegRol;
use App\Models\Seguridad\SegLdRol;
use App\Models\Seguridad\SegLdUser;
use App\Models\Institucion\InstLugarDependencia;
use App\Models\Rrhh\RrhhPersona;
use App\User;
use App\Mail\DatoUsuarioMail;

use App\Models\I4\Funcionario AS I4Funcionario;

use Exception;

class UsuarioController extends Controller
{
    private $estado;

    private $rol_id;
    private $permisos;

    private $public_dir;
    private $public_url;

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

        $this->no_si = [
            '1' => 'NO',
            '2' => 'SI'
        ];

        $this->public_dir = '/storage/seguridad/user/image';
        $this->public_url = 'storage/seguridad/user/image/';
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
            $user_id = Auth::user()->id;

            $consulta1 = SegLdUser::where("seg_ld_users.user_id", "=", $user_id)
                    ->select('lugar_dependencia_id')
                    ->get()
                    ->toArray();

            $array_where = 'estado = 1';
            $where2      = '';
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

                $c_1_sw        = TRUE;
                $array_where_1 = '';
                foreach ($consulta1 as $valor)
                {
                    if($c_1_sw)
                    {
                        $array_where_1 .= "lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
                        $c_1_sw      = FALSE;
                    }
                    else
                    {
                        $array_where_1 .= " OR lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
                    }
                }
                $where2 .= $array_where_1;
            }
            else
            {
                $array_where .= " AND id=0";
            }

            $array_where_2 = 'estado=1';
            if($this->rol_id != 1)
            {
                $consulta2 = SegLdRol::whereRaw($where2)
                    ->select('rol_id')
                    ->groupBy('rol_id')
                    ->orderBy('rol_id', 'asc')
                    ->get()
                    ->toArray();

                if(count($consulta2) > 0)
                {
                    $c_1_sw        = TRUE;
                    $c_2_sw        = TRUE;
                    $array_where_1 = '';
                    foreach ($consulta2 as $valor)
                    {
                        if($valor['rol_id'] != 1)
                        {
                            if($c_1_sw)
                            {
                                $array_where_1 .= " AND (id=" . $valor['rol_id'];
                                $c_1_sw      = FALSE;
                            }
                            else
                            {
                                $array_where_1 .= " OR id=" . $valor['rol_id'];
                            }
                        }
                    }
                    $array_where_1 .= ")";
                    
                    $array_where_2 .= $array_where_1;
                }
            }

            $data = [
                'rol_id'                  => $this->rol_id,
                'permisos'                => $this->permisos,
                'title'                   => 'Usuarios',
                'home'                    => 'Inicio',
                'sistema'                 => 'Seguridad',
                'modulo'                  => 'Usuarios',
                'title_table'             => 'Usuarios',
                'public_url'              => $this->public_url,
                'estado_array'            => $this->estado,
                'no_si_array'             => $this->no_si,
                'lugar_dependencia_array' => InstLugarDependencia::whereRaw($array_where)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray(),
                'rol_array'               => SegRol::whereRaw($array_where_2)
                                            ->select("id", "nombre")
                                            ->orderBy("nombre")
                                            ->get()
                                            ->toArray(),
                'grupo_array'             => SegGrupo::where('estado', '1')
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
                $tabla4 = "seg_grupos";

                $select = "
                    $tabla1.id,
                    $tabla1.rol_id,
                    $tabla1.persona_id,
                    $tabla1.grupo_id,
                    $tabla1.estado,
                    $tabla1.name,
                    $tabla1.imagen,
                    $tabla1.email,
                    $tabla1.lugar_dependencia,
                    $tabla1.i4_funcionario_id,
                    $tabla1.i4_funcionario_id_estado,

                    a2.n_documento,
                    a2.nombre,
                    a2.ap_paterno,
                    a2.ap_materno,
                    a2.ap_esposo,

                    a3.nombre AS rol,

                    a4.nombre AS grupo
                ";

                $array_where = '';

                $user_id = Auth::user()->id;

                $consulta1 = SegLdUser::leftJoin("inst_lugares_dependencia AS a2", "a2.id", "=", "seg_ld_users.lugar_dependencia_id")
                    ->where("seg_ld_users.user_id", "=", $user_id)
                    ->select('a2.id', 'a2.nombre')
                    ->get()
                    ->toArray();
                if(count($consulta1) > 0)
                {
                    $c_1_sw        = TRUE;
                    $c_2_sw        = TRUE;
                    $array_where_1 = '';
                    foreach ($consulta1 as $valor)
                    {
                        if($valor['id'] == '1')
                        {
                            $c_2_sw = FALSE;
                            break;
                        }

                        if($c_1_sw)
                        {
                            $array_where_1 .= "($tabla1.lugar_dependencia ILIKE '%" . $valor['nombre'] . "%'";
                            $c_1_sw      = FALSE;
                        }
                        else
                        {
                            $array_where_1 .= " OR $tabla1.lugar_dependencia ILIKE '%" . $valor['nombre'] . "%'";
                        }
                    }
                    $array_where_1 .= ") AND ";

                    if($c_2_sw)
                    {
                        $array_where .= $array_where_1;
                    }
                }
                else
                {
                    $array_where .= "$tabla1.lugar_dependencia = 'SIN VALOR' AND ";
                }

                $array_where .= "$tabla1.id <> 1";
                $array_where .= $jqgrid->getWhere();

                $count = User::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.persona_id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.rol_id")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "$tabla1.grupo_id")
                            ->whereRaw($array_where)
                            ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = User::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.persona_id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.rol_id")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "$tabla1.grupo_id")
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
                        'estado'                   => $row["estado"],
                        'rol_id'                   => $row["rol_id"],
                        'persona_id'               => $row["persona_id"],
                        'grupo_id'                 => $row["grupo_id"],
                        'imagen'                   => $row["imagen"],
                        'i4_funcionario_id'        => $row["i4_funcionario_id"],
                        'i4_funcionario_id_estado' => $row["i4_funcionario_id_estado"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',
                        $this->utilitarios(array('tipo' => '2', 'imagen' => $row["imagen"])),
                        $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                        $this->utilitarios(array('tipo' => '4', 'valor' => $row["i4_funcionario_id_estado"])),
                        $row["n_documento"],
                        $row["nombre"],
                        $row["ap_paterno"],
                        $row["ap_materno"],
                        $row["ap_esposo"],
                        $row["email"],
                        $row["rol"],
                        $row["grupo"],
                        $this->utilitarios(array('tipo' => '3', 'd_json' => $row["lugar_dependencia"])),
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
                        'titulo'     => '<div class="text-center"><strong>GESTOR DE USUARIO</strong></div>',
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

                // === VALIDATE ===
                    try
                    {
                        if($request->has('password'))
                        {
                            $validator = $this->validate($request,[
                                'email'             => 'required|email',
                                'password'          => 'min:6|max:16',
                                'rol_id'            => 'required',
                                'grupo_id'          => 'required',
                                'lugar_dependencia' => 'required'
                            ],
                            [
                                'email.required' => 'El campo CORREO ELECTRONICO es obligatorio.',
                                'email.email'    => 'El campo CORREO ELECTRONICO no corresponde con una dirección de e-mail válida.',

                                'password.min' => 'El campo CONTRASEÑA debe tener al menos :min caracteres.',
                                'password.max' => 'El campo CONTRASEÑA debe ser :max caracteres como máximo.',

                                'rol_id.required' => 'El campo ROL es obligatorio.',

                                'grupo_id.required' => 'El campo GRUPO es obligatorio.',

                                'lugar_dependencia.required' => 'El campo LUGARES DE DEPENDENCIA es obligatorio.'
                            ]);
                        }
                        else
                        {
                            $validator = $this->validate($request,[
                                'email'             => 'required|email',
                                'rol_id'            => 'required',
                                'grupo_id'          => 'required',
                                'lugar_dependencia' => 'required'
                            ],
                            [
                                'email.required' => 'El campo CORREO ELECTRONICO es obligatorio.',
                                'email.email'    => 'El campo CORREO ELECTRONICO no corresponde con una dirección de e-mail válida.',

                                'rol_id.required' => 'El campo ROL es obligatorio.',

                                'grupo_id.required' => 'El campo GRUPO es obligatorio.',

                                'lugar_dependencia.required' => 'El campo LUGARES DE DEPENDENCIA es obligatorio.'
                            ]);
                        }
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $estado = trim($request->input('estado'));

                    if($request->has('persona_id'))
                    {
                        $persona_id    = trim($request->input('persona_id'));
                        $persona_array = RrhhPersona::where('id', '=', $persona_id)
                            ->select("nombre")
                            ->first()
                            ->toArray();
                        $name = $persona_array['nombre'];
                    }
                    else
                    {
                        $persona_id = NULL;
                        $name       = strtolower($util->getNoAcentoNoComilla(trim($request->input('email'))));
                    }

                    if($request->has('i4_funcionario_id'))
                    {
                        $i4_funcionario_id       = trim($request->input('i4_funcionario_id'));
                        if($opcion == 'n')
                        {
                            $i4_funcionario_id_array = User::where('i4_funcionario_id', $i4_funcionario_id)
                                ->count();
                        }
                        else
                        {
                            $i4_funcionario_id_array = User::where('i4_funcionario_id', $i4_funcionario_id)
                                ->where('id', '<>', $id)
                                ->count();
                        }
                        
                        if($i4_funcionario_id_array > 0)
                        {
                            $respuesta['respuesta'] .= "Ya se asigno a otra persona al FUNCIONARIO DEL I4.";
                            return json_encode($respuesta);
                        }

                        $i4_funcionario_id_estado = 2;
                    }
                    else
                    {
                        $i4_funcionario_id        = NULL;
                        $i4_funcionario_id_estado = 1;
                    }

                    $email = strtolower($util->getNoAcentoNoComilla(trim($request->input('email'))));

                    $password_email = '';
                    if($request->has('password'))
                    {
                        $password = trim($request->input('password'));

                        if(!preg_match('/(?=[a-z])/', $password))
                        {
                            $respuesta['respuesta'] .= "La CONTRASEÑA debe contener al menos una minuscula.";
                            return json_encode($respuesta);
                        }

                        if(!preg_match('/(?=[A-Z])/', $password))
                        {
                            $respuesta['respuesta'] .= "La CONTRASEÑA debe contener al menos una mayuscula.";
                            return json_encode($respuesta);
                        }

                        if(!preg_match('/(?=\d)/', $password))
                        {
                            $respuesta['respuesta'] .= "La CONTRASEÑA debe contener al menos un digito.";
                            return json_encode($respuesta);
                        }

                        $password_email = $password;
                        $password       = bcrypt($password);
                    }

                    $rol_id   = trim($request->input('rol_id'));
                    $grupo_id = trim($request->input('grupo_id'));

                    if($request->has('lugar_dependencia'))
                    {
                        $lugar_dependencia = $request->input('lugar_dependencia');

                        $i = 0;
                        foreach($lugar_dependencia as $lugar_dependencia_id)
                        {
                            $ld_query = InstLugarDependencia::where('id', '=', $lugar_dependencia_id)
                                ->select("id", "nombre")
                                ->first()
                                ->toArray();

                            $ld_nombre_array[$i] = $ld_query['nombre'];
                            $i++;
                        }
                        $ld_json = json_encode($ld_nombre_array);
                    }
                    else
                    {
                        $lugar_dependencia = NULL;
                        $ld_json           = NULL;
                    }

                    $nombre_archivo = NULL;
                    if($opcion == 'n')
                    {
                        $c_email = User::where('email', '=', $email)->count();
                        if($c_email < 1)
                        {
                            $iu             = new User;
                            $iu->estado     = $estado;
                            $iu->persona_id = $persona_id;
                            $iu->name       = $name;
                            $iu->email      = $email;
                            if($request->has('password'))
                            {
                                $iu->password = $password;
                            }
                            else
                            {
                                $email_array    = explode('@', $email);
                                $password_email = $email_array[0] . date('Y');
                                $iu->password   = bcrypt($password_email);
                            }
                            $iu->rol_id                   = $rol_id;
                            $iu->grupo_id                 = $grupo_id;
                            $iu->i4_funcionario_id        = $i4_funcionario_id;
                            $iu->i4_funcionario_id_estado = $i4_funcionario_id_estado;
                            $iu->lugar_dependencia        = $ld_json;
                            $iu->save();

                            $id = $iu->id;

                            $respuesta['respuesta'] .= "El USUARIO fue registrado con éxito.";
                            $respuesta['sw']         = 1;

                            if($request->has('lugar_dependencia'))
                            {
                                foreach($lugar_dependencia as $lugar_dependencia_id)
                                {
                                    $iu1                       = new SegLdUser;
                                    $iu1->lugar_dependencia_id = $lugar_dependencia_id;
                                    $iu1->user_id              = $id;
                                    $iu1->save();
                                }
                            }
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El CORREO ELECTRONICO ya fue registrada.";
                        }
                    }
                    else
                    {
                        $c_email = User::where('email', '=', $email)->where('id', '<>', $id)->count();
                        if($c_email < 1)
                        {
                            $iu                    = User::find($id);
                            $iu->estado            = $estado;
                            $iu->persona_id        = $persona_id;
                            $iu->name              = $name;
                            $iu->email             = $email;
                            if($request->has('password'))
                            {
                                $iu->password = $password;
                            }
                            $iu->rol_id                   = $rol_id;
                            $iu->grupo_id                 = $grupo_id;
                            $iu->i4_funcionario_id        = $i4_funcionario_id;
                            $iu->i4_funcionario_id_estado = $i4_funcionario_id_estado;
                            $iu->lugar_dependencia        = $ld_json;
                            $iu->save();

                            $respuesta['respuesta'] .= "El USUARIO se edito con éxito.";
                            $respuesta['sw']         = 1;
                            $respuesta['iu']         = 2;

                            $del1 = SegLdUser::where('user_id', '=', $id);
                            $del1->delete();

                            if($request->has('lugar_dependencia'))
                            {
                                foreach($lugar_dependencia as $lugar_dependencia_id)
                                {
                                    $iu1                       = new SegLdUser;
                                    $iu1->lugar_dependencia_id = $lugar_dependencia_id;
                                    $iu1->user_id              = $id;
                                    $iu1->save();
                                }
                            }
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El CORREO ELECTRONICO ya fue registrada.";
                        }
                    }

                //=== CORREO ELECTRONICO ===
                    if($request->has('enviar_mail'))
                    {
                        if($request->input('enviar_mail') == '1')
                        {
                            if($password_email != '')
                            {
                                $data1 = [
                                    'title'    => 'MINISTERIO PÚBLICO',
                                    'name'     => $name,
                                    'email'    => $email,
                                    'password' => $password_email,
                                    'url'      => env('APP_URL'),
                                    'i4'       => 'http://i4.fiscalia.gob.bo/i4',
                                    'titan'    => 'http://virtual.fiscalia.gob.bo/titan'
                                ];

                                Mail::to($email, $name)
                                    ->send(new DatoUsuarioMail($data1));
                            }
                        }
                    }

                return json_encode($respuesta);
                break;
            // === UPLOAD IMAGE ===
            case '2':
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
                        'titulo'     => '<div class="text-center"><strong>GESTOR DE USUARIO</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );
                    $opcion = 'n';

                // === PERMISOS ===
                    $id = trim($request->input('usuario_id'));
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

                // === VALIDATE ===
                    try
                    {
                        if($request->has('password'))
                        {
                            $validator = $this->validate($request,[
                                'file'              => 'image|mimes:jpeg,png,jpg|max:5120',
                                'email'             => 'required|email',
                                'password'          => 'min:6|max:16',
                                'rol_id'            => 'required',
                                'lugar_dependencia' => 'required'
                            ],
                            [
                                'file.image' => 'El archivo subido debe de ser imagen.',
                                'file.mimes' => 'El archivo subido debe de ser de tipo jpeg,png,jpg.',
                                'file.max'   => 'El archivo debe pesar 5120 kilobytes como máximo.',

                                'email.required' => 'El campo CORREO ELECTRONICO es obligatorio.',
                                'email.email'    => 'El campo CORREO ELECTRONICO no corresponde con una dirección de e-mail válida.',

                                'password.min' => 'El campo CONTRASEÑA debe tener al menos :min caracteres.',
                                'password.max' => 'El campo CONTRASEÑA debe ser :max caracteres como máximo.',

                                'rol_id.required' => 'El campo ROL es obligatorio.',

                                'lugar_dependencia.required' => 'El campo LUGARES DE DEPENDENCIA es obligatorio.'
                            ]);
                        }
                        else
                        {
                            $validator = $this->validate($request,[
                                'file'              => 'image|mimes:jpeg,png,jpg|max:5120',
                                'email'             => 'required|email',
                                'rol_id'            => 'required',
                                'lugar_dependencia' => 'required'
                            ],
                            [
                                'file.image' => 'El archivo subido debe de ser imagen.',
                                'file.mimes' => 'El archivo subido debe de ser de tipo jpeg,png,jpg.',
                                'file.max'   => 'El archivo debe pesar 5120 kilobytes como máximo.',

                                'email.required' => 'El campo CORREO ELECTRONICO es obligatorio.',
                                'email.email'    => 'El campo CORREO ELECTRONICO no corresponde con una dirección de e-mail válida.',

                                'rol_id.required' => 'El campo ROL es obligatorio.',

                                'lugar_dependencia.required' => 'El campo LUGARES DE DEPENDENCIA es obligatorio.'
                            ]);
                        }
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    $estado = trim($request->input('estado'));

                    if($request->has('persona_id'))
                    {
                        $persona_id = trim($request->input('persona_id'));
                        $persona_array = RrhhPersona::where('id', '=', $persona_id)
                            ->select("nombre")
                            ->first()
                            ->toArray();
                        $name = $persona_array['nombre'];
                    }
                    else
                    {
                        $persona_id = NULL;
                        $name       = strtolower($util->getNoAcentoNoComilla(trim($request->input('email'))));
                    }

                    $email = strtolower($util->getNoAcentoNoComilla(trim($request->input('email'))));

                    $password_email = '';
                    if($request->has('password'))
                    {
                        $password = trim($request->input('password'));

                        if(!preg_match('/(?=[a-z])/', $password))
                        {
                            $respuesta['respuesta'] .= "La CONTRASEÑA debe contener al menos una minuscula.";
                            return json_encode($respuesta);
                        }

                        if(!preg_match('/(?=[A-Z])/', $password))
                        {
                            $respuesta['respuesta'] .= "La CONTRASEÑA debe contener al menos una mayuscula.";
                            return json_encode($respuesta);
                        }

                        if(!preg_match('/(?=\d)/', $password))
                        {
                            $respuesta['respuesta'] .= "La CONTRASEÑA debe contener al menos un digito.";
                            return json_encode($respuesta);
                        }

                        $password_email = $password;
                        $password       = bcrypt($password);
                    }

                    $rol_id = trim($request->input('rol_id'));

                    if($request->has('lugar_dependencia'))
                    {
                        $lugar_dependencia       = trim($request->input('lugar_dependencia'));
                        $lugar_dependencia_array = explode(",", $lugar_dependencia);

                        $i = 0;
                        foreach($lugar_dependencia_array as $lugar_dependencia_id)
                        {
                            $ld_query = InstLugarDependencia::where('id', '=', $lugar_dependencia_id)
                                ->select("id", "nombre")
                                ->first()
                                ->toArray();

                            $ld_nombre_array[$i] = $ld_query['nombre'];
                            $i++;
                        }
                        $ld_json = json_encode($ld_nombre_array);
                    }
                    else
                    {
                        $lugar_dependencia = NULL;
                        $ld_json           = NULL;
                    }

                    $nombre_archivo = NULL;
                    if($opcion == 'n')
                    {
                        $c_email = User::where('email', '=', $email)->count();
                        if($c_email < 1)
                        {
                            //=== IMAGEN UPLOAD ===
                                if($request->hasFile('file'))
                                {
                                    $archivo = $request->file('file');
                                    $nombre_archivo = uniqid('user_', true) . '.' . $archivo->getClientOriginalExtension();
                                    $direccion_archivo = public_path($this->public_dir);

                                    $archivo->move($direccion_archivo, $nombre_archivo);

                                    $image_user   = Image::make($direccion_archivo . '/' . $nombre_archivo);

                                    // $image_width  = $image_user->width();
                                    // $image_height = $image_user->height();

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

                            $iu             = new User;
                            $iu->estado     = $estado;
                            $iu->persona_id = $persona_id;
                            $iu->name       = $name;
                            $iu->email      = $email;
                            if($request->has('password'))
                            {
                                $iu->password = $password;
                            }
                            else
                            {
                                $email_array    = explode('@', $email);
                                $password_email = $email_array[0] . date('Y');
                                $iu->password   = bcrypt($password_email);
                            }
                            $iu->rol_id            = $rol_id;
                            $iu->lugar_dependencia = $ld_json;
                            $iu->imagen            = $nombre_archivo;
                            $iu->save();

                            $id = $iu->id;

                            $respuesta['respuesta'] .= "El USUARIO fue registrado con éxito.";
                            $respuesta['sw']         = 1;

                            if($request->has('lugar_dependencia'))
                            {
                                foreach($lugar_dependencia_array as $lugar_dependencia_id)
                                {
                                    $iu1                       = new SegLdUser;
                                    $iu1->lugar_dependencia_id = $lugar_dependencia_id;
                                    $iu1->user_id              = $id;
                                    $iu1->save();
                                }
                            }
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El CORREO ELECTRONICO ya fue registrada.";
                        }
                    }
                    else
                    {
                        $c_email = User::where('email', '=', $email)->where('id', '<>', $id)->count();
                        if($c_email < 1)
                        {
                            //=== IMAGEN UPLOAD ===
                                $user_imagen = User::where('id', '=', $id)
                                    ->select('imagen')
                                    ->first()
                                    ->toArray();
                                if($user_imagen['imagen'] != '')
                                {
                                    if(file_exists(public_path($this->public_dir) . '/' . $user_imagen['imagen']))
                                    {
                                        unlink(public_path($this->public_dir) . '/' . $user_imagen['imagen']);
                                    }
                                }

                                if($request->hasFile('file'))
                                {
                                    $archivo           = $request->file('file');
                                    $nombre_archivo    = uniqid('user_', true) . '.' . $archivo->getClientOriginalExtension();
                                    $direccion_archivo = public_path($this->public_dir);

                                    $archivo->move($direccion_archivo, $nombre_archivo);

                                    $image_user   = Image::make($direccion_archivo . '/' . $nombre_archivo);

                                    // $image_width  = $image_user->width();
                                    // $image_height = $image_user->height();

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
                            $iu->estado            = $estado;
                            $iu->persona_id        = $persona_id;
                            $iu->name              = $name;
                            $iu->email             = $email;
                            if($request->has('password'))
                            {
                                $iu->password = $password;
                            }
                            $iu->rol_id            = $rol_id;
                            $iu->lugar_dependencia = $ld_json;
                            $iu->imagen            = $nombre_archivo;
                            $iu->save();

                            $respuesta['respuesta'] .= "El USUARIO se edito con éxito.";
                            $respuesta['sw']         = 1;
                            $respuesta['iu']         = 2;

                            $del1 = SegLdUser::where('user_id', '=', $id);
                            $del1->delete();

                            if($request->has('lugar_dependencia'))
                            {
                                foreach($lugar_dependencia_array as $lugar_dependencia_id)
                                {
                                    $iu1                       = new SegLdUser;
                                    $iu1->lugar_dependencia_id = $lugar_dependencia_id;
                                    $iu1->user_id              = $id;
                                    $iu1->save();
                                }
                            }
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El CORREO ELECTRONICO ya fue registrada.";
                        }
                    }

                //=== CORREO ELECTRONICO ===
                    if($request->has('enviar_mail'))
                    {
                        if($request->input('enviar_mail') == '1')
                        {
                            if($password_email != '')
                            {
                                $data1 = [
                                    'title'    => 'MINISTERIO PÚBLICO',
                                    'name'     => $name,
                                    'email'    => $email,
                                    'password' => $password_email,
                                    'url'      => env('APP_URL'),
                                    'i4'       => 'http://i4.fiscalia.gob.bo/i4',
                                    'titan'    => 'http://virtual.fiscalia.gob.bo/titan'
                                ];

                                Mail::to($email, $name)
                                    ->send(new DatoUsuarioMail($data1));

                                // Mail::send('mail.dato_cuenta', $data1, function(Message $message){
                                //     $message->to($email, $name)
                                //             ->from('informatica@fiscalia.gob.bo', 'MINISTERIO PUBLICO - DATOS DE TU CUENTA')
                                //             ->subject('DATOS DE TU CUENTA');
                                // });
                            }
                        }
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
            // === SELECT2 RELLENAR LUGAR DE DEPENDENCIA ===
            case '102':
                $respuesta = [
                    'tipo' => $tipo,
                    'sw'   => 1
                ];

                if($request->has('usuario_id'))
                {
                    $user_id  = $request->input('usuario_id');

                    $query = SegLdUser::where("user_id", "=", $user_id)
                                ->select('lugar_dependencia_id')
                                ->get()
                                ->toArray();

                    if(count($query) > 0)
                    {
                        $respuesta['consulta'] = $query;
                        $respuesta['sw']       = 2;
                    }
                }
                return json_encode($respuesta);
                break;
            // === SELECT2 RELLENAR FUNCIONARIO DEL I4 ===
            case '110':
                if($request->has('q'))
                {
                    $nombre     = $request->input('q');
                    $estado     = trim($request->input('estado'));
                    $page_limit = trim($request->input('page_limit'));

                    $query = I4Funcionario::whereRaw("CONCAT_WS(' - ', NumDocId, CONCAT_WS(' ', ApPat, ApMat, Nombres)) LIKE '%$nombre%'")
                                ->select(DB::raw("id, UPPER(CONCAT_WS(' - ', NumDocId, CONCAT_WS(' ', ApPat, ApMat, Nombres))) AS text"))
                                ->orderByRaw("CONCAT_WS(' ', ApPat, ApMat, Nombres) ASC")
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
            // === RELLENAR FUNCIONARIO DEL I4 ===
            case '111':
                $respuesta = [
                    'tipo' => $tipo,
                    'sw'   => 1
                ];

                if($request->has('i4_funcionario_id'))
                {
                    $i4_funcionario_id = $request->input('i4_funcionario_id');

                    $query = I4Funcionario::where('id', $i4_funcionario_id)
                        ->select(DB::raw("id, UPPER(CONCAT_WS(' - ', NumDocId, CONCAT_WS(' ', ApPat, ApMat, Nombres))) AS text"))
                        ->first()
                        ->toArray();

                    if(count($query) > 0)
                    {
                        $respuesta['consulta'] = $query;
                        $respuesta['sw']       = 2;
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
                if($valor['imagen'] == '')
                {
                    return '';
                }
                else
                {
                    return "<img  width='100%' class='img-thumbnail' alt='Imagen Personal' src='" . asset($this->public_url . $valor['imagen']) . "' />";
                }
                break;
            case '3':
                $respuesta = '';
                if($valor['d_json'] != '')
                {
                    $sw = TRUE;
                    foreach(json_decode($valor['d_json']) as $valor)
                    {
                        if($sw)
                        {
                            $respuesta .= $valor;
                            $sw = FALSE;
                        }
                        else
                        {
                            $respuesta .= "<br>" . $valor;
                        }
                    }
                }
                return($respuesta);
                break;
            case '4':
                switch($valor['valor'])
                {
                    case '1':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->no_si[$valor['valor']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-primary font-sm">' . $this->no_si[$valor['valor']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN VALOR</span>';
                        return($respuesta);
                        break;
                }
                break;
            default:
                break;
        }
    }
}
