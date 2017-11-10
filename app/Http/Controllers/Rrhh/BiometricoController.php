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
use App\Models\Rrhh\RrhhBiometrico;
use App\User;
use App\Mail\DatoUsuarioMail;

use TADPHP\TADFactory;
use TADPHP\TAD;

use Exception;

class BiometricoController extends Controller
{
    private $estado;
    private $e_conexion;
    private $encoding;

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
            '1' => 'HABILITADO CON RED',
            '2' => 'INHABILITADO',
            '3' => 'HABILITADO SIN RED'
        ];

        $this->e_conexion = [
            '1' => 'CON CONEXION',
            '2' => 'SIN CONEXION',
            '3' => 'SIN RED'
        ];

        $this->encoding = [
            'utf-8'     => 'utf-8',
            'iso8859-1' => 'iso8859-1'
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
        if(in_array(['codigo' => '0601'], $this->permisos))
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
                'title'                   => 'Gestor de Biometricos',
                'home'                    => 'Inicio',
                'sistema'                 => 'Biometricos',
                'modulo'                  => 'Gestor de Biometricos',
                'title_table'             => 'Biometricos',
                'estado_array'            => $this->estado,
                'e_conexion_array'        => $this->e_conexion,
                'encoding_array'          => $this->encoding,
                'lugar_dependencia_array' => InstLugarDependencia::whereRaw($array_where)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray()
            ];
            return view('rrhh.biometrico.biometrico')->with($data);
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

                $tabla1 = "rrhh_biometricos";
                $tabla2 = "inst_unidades_desconcentradas";
                $tabla3 = "inst_lugares_dependencia";

                $select = "
                    $tabla1.id,
                    $tabla1.unidad_desconcentrada_id,
                    $tabla1.estado,
                    $tabla1.e_conexion,
                    $tabla1.fs_conexion,
                    $tabla1.fb_conexion,
                    $tabla1.codigo_af,
                    $tabla1.ip,
                    $tabla1.internal_id,
                    $tabla1.com_key,
                    $tabla1.soap_port,
                    $tabla1.udp_port,
                    $tabla1.encoding,
                    $tabla1.description,

                    a2.lugar_dependencia_id,
                    a2.nombre AS unidad_desconcentrada,

                    a3.nombre AS lugar_dependencia
                ";

                $array_where = 'TRUE';

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
                            $array_where_1 .= " AND (a2.lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
                            $c_1_sw        = FALSE;
                        }
                        else
                        {
                            $array_where_1 .= " OR a2.lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
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
                    $array_where .= " AND a2.lugar_dependencia_id=0 AND ";
                }

                $array_where .= $jqgrid->getWhere();

                $count = RrhhBiometrico::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.unidad_desconcentrada_id")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.lugar_dependencia_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = RrhhBiometrico::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.unidad_desconcentrada_id")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "a2.lugar_dependencia_id")
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
                        'unidad_desconcentrada_id' => $row["unidad_desconcentrada_id"],
                        'lugar_dependencia_id'     => $row["lugar_dependencia_id"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',
                        $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                        $this->utilitarios(array('tipo' => '2', 'e_conexion' => $row["e_conexion"])),
                        $row["fs_conexion"],
                        $row["fb_conexion"],
                        $row["lugar_dependencia"],
                        $row["unidad_desconcentrada"],
                        "MP-" . $row["codigo_af"],
                        $row["ip"],
                        $row["internal_id"],
                        $row["com_key"],
                        $row["soap_port"],
                        $row["udp_port"],
                        $row["encoding"],
                        $row["description"],
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
                        'titulo'     => '<div class="text-center"><strong>GESTOR DE BIOMETRICOS</strong></div>',
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
                    $estado = trim($request->input('estado'));
                    try
                    {
                        if($estado == '1')
                        {
                            $this->validate($request,[
                                'lugar_dependencia_id'     => 'required',
                                'unidad_desconcentrada_id' => 'required',
                                'codigo_af'                => 'required',
                                'ip'                       => 'required|ipv4',
                                'internal_id'              => 'required|numeric',
                                'com_key'                  => 'required',
                                'soap_port'                => 'required|numeric',
                                'udp_port'                 => 'required|numeric'
                            ],
                            [
                                'lugar_dependencia_id.required'     => 'El campo LUGAR DE DEPENDENCIA es obligatorio.',

                                'unidad_desconcentrada_id.required' => 'El campo UNIDAD DESCONCENTRADA es obligatorio.',

                                'codigo_af.required'                => 'El campo CODIGO ACTIVO FIJO es obligatorio.',

                                'ip.required'                       => 'El campo IP es obligatorio.',
                                'ip.ipv4'                           => 'El campo IP debe ser una dirección IPv4 válida.',

                                'internal_id.required'              => 'El campo ID USUARIO es obligatorio.',
                                'internal_id.numeric'               => 'El campo ID USUARIO debe ser un número.',

                                'com_key.required'                  => 'El campo LLAVE COM es obligatorio.',

                                'soap_port.required'                => 'El campo PUERTO SOAP es obligatorio.',
                                'soap_port.numeric'                 => 'El campo PUERTO SOAP debe ser un número.',

                                'udp_port.required'                 => 'El campo PUERTO UDP es obligatorio.',
                                'udp_port.numeric'                  => 'El campo PUERTO UDP debe ser un número.'
                            ]);
                        }
                        else
                        {
                            $this->validate($request,[
                                'lugar_dependencia_id'     => 'required',
                                'unidad_desconcentrada_id' => 'required',
                                'codigo_af'                => 'required'
                            ],
                            [
                                'lugar_dependencia_id.required'     => 'El campo LUGAR DE DEPENDENCIA es obligatorio.',

                                'unidad_desconcentrada_id.required' => 'El campo UNIDAD DESCONCENTRADA es obligatorio.',

                                'codigo_af.required'                => 'El campo CODIGO ACTIVO FIJO es obligatorio.'
                            ]);
                        }
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 2;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                // === VARIABLES ===
                    $lugar_dependencia_id     = trim($request->input('lugar_dependencia_id'));
                    $unidad_desconcentrada_id = trim($request->input('unidad_desconcentrada_id'));
                    $codigo_af                = trim($request->input('codigo_af'));
                    $ip                       = trim($request->input('ip'));
                    $internal_id              = trim($request->input('internal_id'));
                    $com_key                  = trim($request->input('com_key'));
                    $soap_port                = trim($request->input('soap_port'));
                    $udp_port                 = trim($request->input('udp_port'));

                // === VERIFICANDO CONEXION ===
                    $data_conexion = [
                        'ip'            => $ip,         // '192.168.30.30' '200.107.241.111' by default (totally useless!!!).
                        'internal_id'   => $internal_id,// 1 by default.
                        'com_key'       => $com_key,    // 0 by default.
                        //'description' => '',          // 'N/A' by default.
                        'soap_port'     => $soap_port,  // 80 by default,
                        'udp_port'      => $udp_port,   // 4370 by default.
                        'encoding'      => 'utf-8'      // iso8859-1 by default.
                    ];

                    $tad_factory = new TADFactory($data_conexion);
                    $tad         = $tad_factory->get_instance();


                    try
                    {
                        $fh_biometrico = $tad->get_date()->to_array();

                        $f_biometrico           = date("d/m/Y H:i:s", strtotime($fh_biometrico['Row']['Date'] . ' ' . $fh_biometrico['Row']['Time']));
                        $respuesta['respuesta'] .= $f_biometrico;

                        return json_encode($respuesta);
                    }
                    catch (Exception $e)
                    {
                        $respuesta['error_sw'] = 3;
                        $respuesta['error']    = $e;
                        return json_encode($respuesta);
                    }

                //=== OPERACION ===
                    // $estado = trim($request->input('estado'));

                    // if($request->has('persona_id'))
                    // {
                    //     $persona_id    = trim($request->input('persona_id'));
                    //     $persona_array = RrhhPersona::where('id', '=', $persona_id)
                    //         ->select("nombre")
                    //         ->first()
                    //         ->toArray();
                    //     $name = $persona_array['nombre'];
                    // }
                    // else
                    // {
                    //     $persona_id = NULL;
                    //     $name       = strtolower($util->getNoAcentoNoComilla(trim($request->input('email'))));
                    // }

                    // $email = strtolower($util->getNoAcentoNoComilla(trim($request->input('email'))));

                    // $password_email = '';
                    // if($request->has('password'))
                    // {
                    //     $password = trim($request->input('password'));

                    //     if(!preg_match('/(?=[a-z])/', $password))
                    //     {
                    //         $respuesta['respuesta'] .= "La CONTRASEÑA debe contener al menos una minuscula.";
                    //         return json_encode($respuesta);
                    //     }

                    //     if(!preg_match('/(?=[A-Z])/', $password))
                    //     {
                    //         $respuesta['respuesta'] .= "La CONTRASEÑA debe contener al menos una mayuscula.";
                    //         return json_encode($respuesta);
                    //     }

                    //     if(!preg_match('/(?=\d)/', $password))
                    //     {
                    //         $respuesta['respuesta'] .= "La CONTRASEÑA debe contener al menos un digito.";
                    //         return json_encode($respuesta);
                    //     }

                    //     $password_email = $password;
                    //     $password       = bcrypt($password);
                    // }

                    // $rol_id = trim($request->input('rol_id'));

                    // if($request->has('lugar_dependencia'))
                    // {
                    //     $lugar_dependencia = $request->input('lugar_dependencia');

                    //     $i = 0;
                    //     foreach($lugar_dependencia as $lugar_dependencia_id)
                    //     {
                    //         $ld_query = InstLugarDependencia::where('id', '=', $lugar_dependencia_id)
                    //             ->select("id", "nombre")
                    //             ->first()
                    //             ->toArray();

                    //         $ld_nombre_array[$i] = $ld_query['nombre'];
                    //         $i++;
                    //     }
                    //     $ld_json = json_encode($ld_nombre_array);
                    // }
                    // else
                    // {
                    //     $lugar_dependencia = NULL;
                    //     $ld_json           = NULL;
                    // }

                    // $nombre_archivo = NULL;
                    // if($opcion == 'n')
                    // {
                    //     $c_email = User::where('email', '=', $email)->count();
                    //     if($c_email < 1)
                    //     {
                    //         $iu             = new User;
                    //         $iu->estado     = $estado;
                    //         $iu->persona_id = $persona_id;
                    //         $iu->name       = $name;
                    //         $iu->email      = $email;
                    //         if($request->has('password'))
                    //         {
                    //             $iu->password = $password;
                    //         }
                    //         else
                    //         {
                    //             $email_array    = explode('@', $email);
                    //             $password_email = $email_array[0] . date('Y');
                    //             $iu->password   = bcrypt($password_email);
                    //         }
                    //         $iu->rol_id            = $rol_id;
                    //         $iu->lugar_dependencia = $ld_json;
                    //         $iu->save();

                    //         $id = $iu->id;

                    //         $respuesta['respuesta'] .= "El USUARIO fue registrado con éxito.";
                    //         $respuesta['sw']         = 1;

                    //         if($request->has('lugar_dependencia'))
                    //         {
                    //             foreach($lugar_dependencia as $lugar_dependencia_id)
                    //             {
                    //                 $iu1                       = new SegLdUser;
                    //                 $iu1->lugar_dependencia_id = $lugar_dependencia_id;
                    //                 $iu1->user_id              = $id;
                    //                 $iu1->save();
                    //             }
                    //         }
                    //     }
                    //     else
                    //     {
                    //         $respuesta['respuesta'] .= "El CORREO ELECTRONICO ya fue registrada.";
                    //     }
                    // }
                    // else
                    // {
                    //     $c_email = User::where('email', '=', $email)->where('id', '<>', $id)->count();
                    //     if($c_email < 1)
                    //     {
                    //         $iu                    = User::find($id);
                    //         $iu->estado            = $estado;
                    //         $iu->persona_id        = $persona_id;
                    //         $iu->name              = $name;
                    //         $iu->email             = $email;
                    //         if($request->has('password'))
                    //         {
                    //             $iu->password = $password;
                    //         }
                    //         $iu->rol_id            = $rol_id;
                    //         $iu->lugar_dependencia = $ld_json;
                    //         $iu->save();

                    //         $respuesta['respuesta'] .= "El USUARIO se edito con éxito.";
                    //         $respuesta['sw']         = 1;
                    //         $respuesta['iu']         = 2;

                    //         $del1 = SegLdUser::where('user_id', '=', $id);
                    //         $del1->delete();

                    //         if($request->has('lugar_dependencia'))
                    //         {
                    //             foreach($lugar_dependencia as $lugar_dependencia_id)
                    //             {
                    //                 $iu1                       = new SegLdUser;
                    //                 $iu1->lugar_dependencia_id = $lugar_dependencia_id;
                    //                 $iu1->user_id              = $id;
                    //                 $iu1->save();
                    //             }
                    //         }
                    //     }
                    //     else
                    //     {
                    //         $respuesta['respuesta'] .= "El CORREO ELECTRONICO ya fue registrada.";
                    //     }
                    // }

                return json_encode($respuesta);
                break;
            // === SELECT2 UNIDAD DESCONCENTRADA ===
            case '103':
                $respuesta = [
                    'tipo' => $tipo,
                    'sw'   => 1
                ];

                if($request->has('lugar_dependencia_id'))
                {
                    $lugar_dependencia_id  = $request->input('lugar_dependencia_id');

                    $query = InstUnidadDesconcentrada::where("lugar_dependencia_id", "=", $lugar_dependencia_id)
                                ->select('id', 'nombre')
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
                        $respuesta = '<span class="label label-warning font-sm">' . $this->estado[$valor['estado']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
                        return($respuesta);
                        break;
                }
                break;
            case '2':
                switch($valor['e_conexion'])
                {
                    case '1':
                        $respuesta = '<span class="label label-primary font-sm">' . $this->e_conexion[$valor['e_conexion']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->e_conexion[$valor['e_conexion']] . '</span>';
                        return($respuesta);
                        break;
                    case '3':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->e_conexion[$valor['e_conexion']] . '</span>';
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