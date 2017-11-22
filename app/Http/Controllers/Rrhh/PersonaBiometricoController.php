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
use App\Models\Rrhh\RrhhPersona;
use App\Models\Rrhh\RrhhBiometrico;
use App\Models\Rrhh\RrhhLogAlerta;
use App\Models\Rrhh\RrhhLogMarcacion;
use App\Models\Rrhh\RrhhPersonaBiometrico;
use App\User;

use TADPHP\TADFactory;
use TADPHP\TAD;

use Exception;

class PersonaBiometricoController extends Controller
{
    private $estado;
    private $privilegio;

    private $rol_id;
    private $permisos;
    private $tipo_marcacion;

    public function __construct()
    {
        $this->middleware('auth');

        $this->estado = [
            '1' => 'REGISTRADO EN EL BIOMETRICO',
            '2' => 'EL BIOMETRICO NO PERMITE REGISTROS'
        ];

        $this->privilegio = [
            '0'  => 'USUARIO NORMAL',
            '14' => 'ADMINISTRADOR'
        ];

        $this->tipo_marcacion = [
            '1' => 'POR RED MEDIANTE CRON',
            '2' => 'POR RED PULSANDO BOTON',
            '3' => 'DESDE ARCHIVO SUBIDO',
            '4' => 'POR DIGITAL PERSONA'
        ];
    }

    public function index()
    {
        $this->rol_id   = Auth::user()->rol_id;
        $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                            ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                            ->select("seg_permisos.codigo")
                            ->get()
                            ->toArray();
        if(in_array(['codigo' => '0701'], $this->permisos))
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
                'title'                   => 'Personas - Biometricos',
                'home'                    => 'Inicio',
                'sistema'                 => 'Biometricos',
                'modulo'                  => 'Personas - Biometricos',
                'title_table'             => 'Personas y Biometricos',
                'estado_array'            => $this->estado,
                'privilegio_array'        => $this->privilegio,
                'tipo_marcacion_array'    => $this->tipo_marcacion,
                'lugar_dependencia_array' => InstLugarDependencia::whereRaw($array_where)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray()
            ];
            return view('rrhh.persona_biometrico.persona_biometrico')->with($data);
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

                $tabla1 = "rrhh_personas_biometricos";
                $tabla2 = "rrhh_personas";
                $tabla3 = "rrhh_biometricos";
                $tabla4 = "inst_unidades_desconcentradas";
                $tabla5 = "inst_lugares_dependencia";

                $select = "
                    $tabla1.id,
                    $tabla1.persona_id,
                    $tabla1.biometrico_id,
                    $tabla1.estado,
                    $tabla1.f_registro_biometrico,
                    $tabla1.n_documento_biometrico,
                    $tabla1.nombre,
                    $tabla1.privilegio,
                    $tabla1.password,

                    a2.n_documento,
                    a2.nombre AS nombre_persona,
                    a2.ap_paterno,
                    a2.ap_materno,

                    a3.unidad_desconcentrada_id,
                    a3.codigo_af,
                    a3.ip,

                    a4.lugar_dependencia_id,
                    a4.nombre AS unidad_desconcentrada,

                    a5.nombre AS lugar_dependencia
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
                            $array_where_1 .= " AND (a4.lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
                            $c_1_sw        = FALSE;
                        }
                        else
                        {
                            $array_where_1 .= " OR a4.lugar_dependencia_id=" . $valor['lugar_dependencia_id'];
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
                    $array_where .= " AND a4.lugar_dependencia_id=0 AND ";
                }

                $array_where .= $jqgrid->getWhere();

                $count = RrhhPersonaBiometrico::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.persona_id")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.biometrico_id")
                    ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.unidad_desconcentrada_id")
                    ->leftJoin("$tabla5 AS a5", "a5.id", "=", "a4.lugar_dependencia_id")
                    ->whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = RrhhPersonaBiometrico::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.persona_id")
                    ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.biometrico_id")
                    ->leftJoin("$tabla4 AS a4", "a4.id", "=", "a3.unidad_desconcentrada_id")
                    ->leftJoin("$tabla5 AS a5", "a5.id", "=", "a4.lugar_dependencia_id")
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
                        'persona_id'               => $row["persona_id"],
                        'biometrico_id'            => $row["biometrico_id"],
                        'unidad_desconcentrada_id' => $row["unidad_desconcentrada_id"],
                        'lugar_dependencia_id'     => $row["lugar_dependencia_id"],
                        'n_documento'              => $row["n_documento"],
                        'nombre_persona'           => $row["nombre_persona"],
                        'ap_paterno'               => $row["ap_paterno"],
                        'ap_materno'               => $row["ap_materno"],
                        'privilegio'               => $row["privilegio"],
                        'password'                 => $row["password"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',
                        $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                        $row["f_registro_biometrico"],
                        $row["n_documento_biometrico"],
                        $row["nombre"],
                        $this->privilegio[$row["privilegio"]],

                        'MP-' . $row["codigo_af"],
                        $row["ip"],
                        $row["unidad_desconcentrada"],
                        $row["lugar_dependencia"],
                        //=== VARIABLES OCULTOS ===
                            json_encode($val_array)
                    );
                    $i++;
                }
                return json_encode($respuesta);
                break;
            case '2':
                $jqgrid = new JqgridClass($request);

                $tabla1 = "rrhh_log_marcaciones";

                $select = "
                    id,
                    biometrico_id,
                    persona_id,
                    tipo_marcacion,
                    n_documento_biometrico,
                    f_marcacion
                ";

                $array_where = 'TRUE';

                $array_where .= " AND biometrico_id=" . $request->input('biometrico_id') . " AND persona_id=" . $request->input('persona_id');

                $array_where .= $jqgrid->getWhere();

                $count = RrhhLogMarcacion::whereRaw($array_where)
                    ->count();

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = RrhhLogMarcacion::whereRaw($array_where)
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
                        'tipo_marcacion' => $row["tipo_marcacion"]
                    );

                    $respuesta['rows'][$i]['id']   = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        $this->tipo_marcacion[$row["tipo_marcacion"]],
                        $row["f_marcacion"],
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
                        'titulo'     => '<div class="text-center"><strong>PERSONAS - BIOMETRICOS</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'iu'         => 1,
                        'error_sw'   => 1
                    );
                    $opcion = 'n';

                    $fs_conexion = date("Y-m-d H:i:s");

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if($id != '')
                    {
                        $opcion = 'e';
                        if(!in_array(['codigo' => '0703'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '0702'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                // === VALIDATE ===
                    if($opcion == 'n')
                    {
                        try
                        {
                            $this->validate($request,[
                                'persona_id'               => 'required',
                                'lugar_dependencia_id'     => 'required',
                                'unidad_desconcentrada_id' => 'required',
                                'biometrico_id'            => 'required',
                                'privilegio'               => 'required'
                            ],
                            [
                                'persona_id.required'               => 'El campo PERSONA es obligatorio.',

                                'lugar_dependencia_id.required'     => 'El campo LUGAR DE DEPENDENCIA es obligatorio.',

                                'unidad_desconcentrada_id.required' => 'El campo UNIDAD DESCONCENTRADA es obligatorio.',

                                'biometrico_id.required'            => 'El campo BIOMETRICO es obligatorio.',

                                'privilegio.required'               => 'El campo PRIVILEGIO es obligatorio.'
                            ]);
                        }
                        catch (Exception $e)
                        {
                            $respuesta['error_sw'] = 2;
                            $respuesta['error']    = $e;
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        try
                        {
                            $this->validate($request,[
                                'privilegio'               => 'required'
                            ],
                            [
                                'privilegio.required'               => 'El campo PRIVILEGIO es obligatorio.'
                            ]);
                        }
                        catch (Exception $e)
                        {
                            $respuesta['error_sw'] = 2;
                            $respuesta['error']    = $e;
                            return json_encode($respuesta);
                        }
                    }

                // === VARIABLES ===
                    $persona_id               = trim($request->input('persona_id'));
                    $lugar_dependencia_id     = trim($request->input('lugar_dependencia_id'));
                    $unidad_desconcentrada_id = trim($request->input('unidad_desconcentrada_id'));
                    $biometrico_id            = trim($request->input('biometrico_id'));
                    $privilegio               = trim($request->input('privilegio'));
                    $password                 = rand(1000, 9999);
                    $estado                   = 1;

                // === VERIFICANDO CONEXION ===
                    if($opcion == 'n')
                    {
                        $consulta1 = RrhhBiometrico::where("id", "=", $biometrico_id)
                            ->select('estado', 'ip', 'internal_id', 'com_key', 'soap_port', 'udp_port', 'encoding')
                            ->first()
                            ->toArray();

                        $consulta2 = RrhhPersona::where("id", "=", $persona_id)
                            ->select('n_documento', 'nombre', 'ap_paterno', 'ap_materno')
                            ->first()
                            ->toArray();

                        $nombre = trim($consulta2['ap_paterno'] . ' ' . $consulta2['ap_materno'] . ' ' . $consulta2['nombre']);

                        $n_documento_array = explode("-", $consulta2['n_documento']);
                        if(isset($n_documento_array[1]))
                        {
                            //=== TAL VES PROVOQUE ERRORES ===
                                $i = 0;
                                $e_while = FALSE;
                                while(TRUE)
                                {
                                    $n_documento = $n_documento_array[0] . rand(0, 9);

                                    $consulta3 = RrhhPersonaBiometrico::where('n_documento_biometrico', '=', $n_documento)
                                        ->where('biometrico_id', '=', $biometrico_id)
                                        ->count();

                                    if($consulta3 < 1)
                                    {
                                        break;
                                    }
                                    if($i > 50)
                                    {
                                        $e_while = TRUE;
                                        break;
                                    }
                                }
                                if($e_while)
                                {
                                    $respuesta['respuesta'] .= "Fracaso al generar el PIN de enlace al Biométrico.<br>CONSULTE CON EL ADMINISTRADOR DEL SISTEMA URGENTEMENTE.";
                                    return json_encode($respuesta);
                                }
                        }
                        else
                        {
                            $n_documento = $n_documento_array[0];
                        }

                        $n_documento_biometrico = $n_documento;

                        if($consulta1['estado'] == '1')
                        {
                            $data_conexion = [
                                'ip'          => $consulta1['ip'],
                                'internal_id' => $consulta1['internal_id'],
                                'com_key'     => $consulta1['com_key'],
                                'soap_port'   => $consulta1['soap_port'],
                                'udp_port'    => $consulta1['udp_port'],
                                'encoding'    => $consulta1['encoding']
                            ];

                            $tad_factory = new TADFactory($data_conexion);
                            $tad         = $tad_factory->get_instance();

                            try
                            {
                                $user_info = $tad->get_user_info(['pin' => $n_documento_biometrico])->to_array();

                                if(count($user_info) == 0)
                                {
                                    $res = $tad->set_user_info([
                                        'pin'       => $n_documento_biometrico,
                                        'name'      => $nombre,
                                        'privilege' => $privilegio,
                                        'password'  => $password
                                    ]);
                                    $respuesta['respuesta'] .= "La persona se registro en el biométrico.<br>";
                                }
                                else
                                {

                                    $respuesta['respuesta'] .= "La persona ya estaba registrado en el biométrico.<br>";
                                }

                                $fb_conexion_array = $tad->get_date()->to_array();
                                $fb_conexion       = $fb_conexion_array['Row']['Date'] . ' ' . $fb_conexion_array['Row']['Time'];
                                $e_conexion        = 1;

                                $iu              = RrhhBiometrico::find($biometrico_id);
                                $iu->e_conexion  = $e_conexion;
                                $iu->fs_conexion = $fs_conexion;
                                $iu->fb_conexion = $fb_conexion;
                                $iu->save();
                            }
                            catch (Exception $e)
                            {
                                $respuesta['respuesta'] .= "No se pudo conectar a " . $consulta1['ip'] . "<br>Verifique la conexión.<br>";
                                $e_conexion             = 2;

                                $error = '' . $e;
                                $error_array = explode("Stack trace:", $error);

                                $iu                = new RrhhLogAlerta;
                                $iu->biometrico_id = $biometrico_id;
                                $iu->tipo_emisor   = 4;
                                $iu->tipo_alerta   = 1;
                                $iu->f_alerta      = $fs_conexion;
                                $iu->mensaje       = $error_array[0];
                                $iu->save();

                                return json_encode($respuesta);
                            }
                        }
                        else
                        {
                            $estado = 2;
                        }
                    }
                    else
                    {
                        $consulta5 = RrhhPersonaBiometrico::where('id', '=', $id)
                            ->select('persona_id', 'biometrico_id', 'n_documento_biometrico', 'nombre', 'privilegio')
                            ->first()
                            ->toArray();

                        if($consulta5['privilegio'] == $privilegio)
                        {
                            $respuesta['respuesta'] .= "No se puede editar porque no se cambio el PRIVILEGIO.";
                            return json_encode($respuesta);
                        }

                        $consulta1 = RrhhBiometrico::where("id", "=", $consulta5['biometrico_id'])
                            ->select('estado', 'ip', 'internal_id', 'com_key', 'soap_port', 'udp_port', 'encoding')
                            ->first()
                            ->toArray();

                        $consulta2 = RrhhPersona::where("id", "=", $consulta5['persona_id'])
                            ->select('n_documento', 'nombre', 'ap_paterno', 'ap_materno')
                            ->first()
                            ->toArray();

                        $nombre = trim($consulta2['ap_paterno'] . ' ' . $consulta2['ap_materno'] . ' ' . $consulta2['nombre']);

                        if($consulta1['estado'] == '1')
                        {
                            $data_conexion = [
                                'ip'          => $consulta1['ip'],
                                'internal_id' => $consulta1['internal_id'],
                                'com_key'     => $consulta1['com_key'],
                                'soap_port'   => $consulta1['soap_port'],
                                'udp_port'    => $consulta1['udp_port'],
                                'encoding'    => $consulta1['encoding']
                            ];

                            $tad_factory = new TADFactory($data_conexion);
                            $tad         = $tad_factory->get_instance();

                            try
                            {
                                $res = $tad->set_user_info([
                                    'pin'       => $consulta5['n_documento_biometrico'],
                                    'name'      => $nombre,
                                    'privilege' => $privilegio,
                                    'password'  => $password
                                ]);
                                $respuesta['respuesta'] .= "La persona se edito en el biométrico.<br>";

                                $fb_conexion_array = $tad->get_date()->to_array();
                                $fb_conexion       = $fb_conexion_array['Row']['Date'] . ' ' . $fb_conexion_array['Row']['Time'];
                                $e_conexion        = 1;

                                $iu              = RrhhBiometrico::find($consulta5['biometrico_id']);
                                $iu->e_conexion  = $e_conexion;
                                $iu->fs_conexion = $fs_conexion;
                                $iu->fb_conexion = $fb_conexion;
                                $iu->save();
                            }
                            catch (Exception $e)
                            {
                                $respuesta['respuesta'] .= "No se pudo conectar a " . $consulta1['ip'] . "<br>Verifique la conexión.<br>";
                                $e_conexion             = 2;

                                $error = '' . $e;
                                $error_array = explode("Stack trace:", $error);

                                $iu                = new RrhhLogAlerta;
                                $iu->biometrico_id = $consulta5['biometrico_id'];
                                $iu->tipo_emisor   = 4;
                                $iu->tipo_alerta   = 1;
                                $iu->f_alerta      = $fs_conexion;
                                $iu->mensaje       = $error_array[0];
                                $iu->save();

                                return json_encode($respuesta);
                            }
                        }
                        else
                        {
                            $estado = 2;
                        }
                    }

                //=== OPERACION ===
                    if($opcion == 'n')
                    {
                        $consulta4 = RrhhPersonaBiometrico::where('persona_id', '=', $persona_id)
                            ->where('biometrico_id', '=', $biometrico_id)
                            ->count();

                        if($consulta4 < 1)
                        {
                            $iu                         = new RrhhPersonaBiometrico;
                            $iu->persona_id             = $persona_id;
                            $iu->biometrico_id          = $biometrico_id;
                            $iu->f_registro_biometrico  = $fs_conexion;
                            $iu->n_documento_biometrico = $n_documento_biometrico;
                            $iu->nombre                 = $nombre;
                            $iu->privilegio             = $privilegio;
                            $iu->password               = $password;
                            $iu->estado                 = $estado;
                            $iu->save();

                            $respuesta['respuesta'] .= "La relación entre PERSONA y BIOMETRICO fue registrada con éxito.";
                            $respuesta['sw']         = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "La relación entre PERSONA y BIOMETRICO ya se encuentra registrada.";
                        }
                    }
                    else
                    {
                        $iu             = RrhhPersonaBiometrico::find($id);
                        $iu->privilegio = $privilegio;
                        $iu->estado     = $estado;
                        $iu->save();

                        $respuesta['respuesta'] .= "La relación entre PERSONA y BIOMETRICO fue editada con éxito.<br>Favor vuelva a registrar las huellas y la cara de la persona.";
                        $respuesta['sw']         = 1;
                        $respuesta['iu']         = 2;
                    }

                return json_encode($respuesta);
                break;
            // === ELIMINAR HUELLA Y ROSTRO ===
            case '2':
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
                        'titulo'     => '<div class="text-center"><strong>ALERTA</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo
                    );

                    $fs_conexion = date("Y-m-d H:i:s");

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if(!in_array(['codigo' => '0704'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para ELIMINAR HUELLA Y ROSTRO.";
                        return json_encode($respuesta);
                    }

                // === CONSULTA ===
                    $consulta1 = RrhhPersonaBiometrico::where('id', '=', $id)
                            ->select('persona_id', 'biometrico_id', 'n_documento_biometrico', 'nombre', 'privilegio')
                            ->first()
                            ->toArray();

                    $biometrico = RrhhBiometrico::where('id', '=', $consulta1['biometrico_id'])
                        ->first()
                        ->toArray();

                // === VERIFICANDO CONEXION ===
                    if($biometrico['estado'] == '1')
                    {
                        $data_conexion = [
                            'ip'            => $biometrico['ip'],
                            'internal_id'   => $biometrico['internal_id'],
                            'com_key'       => $biometrico['com_key'],
                            'soap_port'     => $biometrico['soap_port'],
                            'udp_port'      => $biometrico['udp_port'],
                            'encoding'      => $biometrico['encoding']
                        ];

                        $tad_factory = new TADFactory($data_conexion);
                        $tad         = $tad_factory->get_instance();

                        try
                        {
                            $fb_conexion_array = $tad->get_date()->to_array();
                            $fb_conexion       = $fb_conexion_array['Row']['Date'] . ' ' . $fb_conexion_array['Row']['Time'];
                            $e_conexion        = 1;

                            $tad->delete_template(['pin' => $consulta1['n_documento_biometrico']]);

                            $respuesta['respuesta'] .= "Se elimino las huellas y rostro del biométrico de la siguiente dirección " . $biometrico['ip'] . ".";
                            $respuesta['sw'] = 1;
                        }
                        catch (Exception $e)
                        {
                            $respuesta['respuesta'] .= "No se logro eliminar las huellas y rostro del biométrico de la siguiente dirección " . $biometrico['ip'] . "<br>Verifique la conexión.<br>";
                            $e_conexion             = 2;
                            $fb_conexion            = null;

                            $error = '' . $e;
                            $error_array = explode("Stack trace:", $error);

                            $iu                = new RrhhLogAlerta;
                            $iu->biometrico_id = $consulta1['biometrico_id'];
                            $iu->tipo_emisor   = 2;
                            $iu->tipo_alerta   = 1;
                            $iu->f_alerta      = $fs_conexion;
                            $iu->mensaje       = $error_array[0];
                            $iu->save();
                        }
                    }

                //=== OPERACION ===
                    $iu              = RrhhBiometrico::find($consulta1['biometrico_id']);
                    $iu->e_conexion  = $e_conexion;
                    $iu->fs_conexion = $fs_conexion;
                    $iu->fb_conexion = $fb_conexion;
                    $iu->save();

                return json_encode($respuesta);
                break;
            // === ELIMINAR RELACION PERSONA - BIOMETRICO ===
            case '3':
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
                        'titulo'     => '<div class="text-center"><strong>ALERTA</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo
                    );

                    $fs_conexion = date("Y-m-d H:i:s");

                // === PERMISOS ===
                    $id = trim($request->input('id'));
                    if(!in_array(['codigo' => '0705'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para ELIMINAR RELACION PERSONA - BIOMETRICO.";
                        return json_encode($respuesta);
                    }

                // === CONSULTA ===
                    $consulta1 = RrhhPersonaBiometrico::where('id', '=', $id)
                            ->select('persona_id', 'biometrico_id', 'n_documento_biometrico', 'nombre', 'privilegio')
                            ->first()
                            ->toArray();

                    $biometrico = RrhhBiometrico::where('id', '=', $consulta1['biometrico_id'])
                        ->first()
                        ->toArray();

                // === VERIFICANDO CONEXION ===
                    if($biometrico['estado'] == '1')
                    {
                        $data_conexion = [
                            'ip'            => $biometrico['ip'],
                            'internal_id'   => $biometrico['internal_id'],
                            'com_key'       => $biometrico['com_key'],
                            'soap_port'     => $biometrico['soap_port'],
                            'udp_port'      => $biometrico['udp_port'],
                            'encoding'      => $biometrico['encoding']
                        ];

                        $tad_factory = new TADFactory($data_conexion);
                        $tad         = $tad_factory->get_instance();

                        try
                        {
                            $fb_conexion_array = $tad->get_date()->to_array();
                            $fb_conexion       = $fb_conexion_array['Row']['Date'] . ' ' . $fb_conexion_array['Row']['Time'];
                            $e_conexion        = 1;

                            $tad->delete_user(['pin' => $consulta1['n_documento_biometrico']]);

                            $respuesta['respuesta'] .= "Se elimino la relación PERSONA - BIOMETRICO de la siguiente dirección " . $biometrico['ip'] . ".<br>";
                        }
                        catch (Exception $e)
                        {
                            $respuesta['respuesta'] .= "No se logro eliminar la relación PERSONA - BIOMETRICO de la siguiente dirección " . $biometrico['ip'] . "<br>Verifique la conexión.<br>";
                            $e_conexion             = 2;
                            $fb_conexion            = null;

                            $error = '' . $e;
                            $error_array = explode("Stack trace:", $error);

                            $iu                = new RrhhLogAlerta;
                            $iu->biometrico_id = $consulta1['biometrico_id'];
                            $iu->tipo_emisor   = 2;
                            $iu->tipo_alerta   = 1;
                            $iu->f_alerta      = $fs_conexion;
                            $iu->mensaje       = $error_array[0];
                            $iu->save();
                        }

                        $iu              = RrhhBiometrico::find($consulta1['biometrico_id']);
                        $iu->e_conexion  = $e_conexion;
                        $iu->fs_conexion = $fs_conexion;
                        $iu->fb_conexion = $fb_conexion;
                        $iu->save();
                    }

                //=== OPERACION ===
                    $de = RrhhPersonaBiometrico::find($id);
                    $de->delete();

                    $respuesta['sw'] = 1;
                    $respuesta['respuesta'] .= "Se elimino la relación PERSONA - BIOMETRICO de la base de datos.";

                return json_encode($respuesta);
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
                }
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
                        ->where("estado", "=", 1)
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
            // === SELECT2 BIOMETRICOS ===
            case '104':
                $respuesta = [
                    'tipo' => $tipo,
                    'sw'   => 1
                ];
                if($request->has('unidad_desconcentrada_id'))
                {
                    $unidad_desconcentrada_id  = $request->input('unidad_desconcentrada_id');
                    $query = RrhhBiometrico::where("unidad_desconcentrada_id", "=", $unidad_desconcentrada_id)
                        ->where("estado", "<>", 2)
                        ->select(DB::raw("id, CONCAT_WS(' - ', codigo_af, ip) AS nombre"))
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
            case '3':
                switch($valor['tipo_alerta'])
                {
                    case '1':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->tipo_alerta[$valor['tipo_alerta']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-warning font-sm">' . $this->tipo_alerta[$valor['tipo_alerta']] . '</span>';
                        return($respuesta);
                        break;
                    case '3':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->tipo_alerta[$valor['tipo_alerta']] . '</span>';
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