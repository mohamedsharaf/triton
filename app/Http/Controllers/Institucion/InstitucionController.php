<?php

namespace App\Http\Controllers\Institucion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;
use App\Libraries\UtilClass;
use App\Models\Seguridad\SegPermisoRol;
use App\Models\Institucion\InstInstitucion;
use App\Models\UbicacionGeografica\UbgeMunicipio;

class InstitucionController extends Controller
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
        if(in_array(['codigo' => '2401'], $this->permisos))
        {
            $data = [
                'rol_id'       => $this->rol_id,
                'permisos'     => $this->permisos,
                'title'        => 'Gestor de Instituciones',
                'home'         => 'Inicio',
                'sistema'      => 'Institución',
                'modulo'       => 'Gestor de instituciones',
                'title_table'  => 'Instituciones y Oficinas',
                'estado_array' => $this->estado
            ];
            return view('institucion.institucion.institucion')->with($data);
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

            $institucion1      = "inst_instituciones";
            $institucion2      = "inst_instituciones";
            $municipio         = "ubge_municipios";
            $provincia         = "ubge_provincias";
            $departamento      = "ubge_departamentos";
            $lugar_dependencia = "inst_lugares_dependencia";
            $seg_ld_users      = "seg_ld_users";

            $select = "
            b.id,
            b.ubge_municipios_id,
            $institucion1.id as institucion_id,
            b.estado,
            b.nombre,
            $institucion1.nombre as institucion,
            b.direccion,
            b.zona,
            b.telefono,
            b.celular,
            b.email,
            case when b.institucion_id is not null then b.respcontacto else $institucion1.respcontacto end,
            c.nombre as municipio,
            d.nombre as provincia,
            e.nombre as departamento
            ";

            $array_where = "$institucion1.institucion_id is null and s.user_id = " . Auth::user()->id;
            $array_where .= $jqgrid->getWhere();

            $count = InstInstitucion::leftJoin("$institucion2 AS b", "b.institucion_id", "=", "$institucion1.id")
                ->leftJoin("$municipio AS c", "c.id", "=", "$institucion1.ubge_municipios_id")
                ->leftJoin("$provincia AS d", "d.id", "=", "c.provincia_id")
                ->leftJoin("$departamento AS e", "e.id", "=", "d.departamento_id")
                ->leftJoin("$lugar_dependencia AS l", "l.ubge_departamentos_id", "=", "e.id")
                ->leftJoin("$seg_ld_users AS s", "s.lugar_dependencia_id", "=", "l.id")
                ->whereRaw($array_where)
                ->count();

            $limit_offset = $jqgrid->getLimitOffset($count);

            $query = InstInstitucion::leftJoin("$institucion2 AS b", "b.institucion_id", "=", "$institucion1.id")
                ->leftJoin("$municipio AS c", "c.id", "=", "$institucion1.ubge_municipios_id")
                ->leftJoin("$provincia AS d", "d.id", "=", "c.provincia_id")
                ->leftJoin("$departamento AS e", "e.id", "=", "d.departamento_id")
                ->leftJoin("$lugar_dependencia AS l", "l.ubge_departamentos_id", "=", "e.id")
                ->leftJoin("$seg_ld_users AS s", "s.lugar_dependencia_id", "=", "l.id")
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
                    'estado'       => $row["estado"],
                    'municipio_id' => $row["ubge_municipios_id"],
                    'institucion_id' => $row["institucion_id"]
                );

                $respuesta['rows'][$i]['id'] = $row["id"];
                $respuesta['rows'][$i]['cell'] = array(
                    '',
                    $this->utilitarios(array('tipo' => '1', 'estado' => $row["estado"])),
                    $row["nombre"],
                    $row["institucion"],
                    $row["direccion"],
                    $row["zona"],
                    $row["telefono"],
                    $row["celular"],
                    $row["email"],
                    $row["respcontacto"],
                    $row["municipio"],
                    $row["provincia"],
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
            'titulo'    => 'GESTOR DE INSTITUCIONES/OFICINAS',
            'respuesta' => 'No es solicitud AJAX.'
        ];
        return json_encode($respuesta);
        }

        $tipo = $request->input('tipo');

        switch($tipo)
        {
            // === INSERT UPDATE GESTOR DE GRUPOS ===
            case '1':
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
                    'titulo'     => '<div class="text-center"><strong>GESTOR DE INSTITUCIONES</strong></div>',
                    'respuesta'  => '',
                    'tipo'       => $tipo,
                    'iu'         => 1
                );
                $opcion = 'n';
                $error  = FALSE;

                // === PERMISOS ===
                $id = trim($request->input('idedinstitucion'));
                if($id != '')
                {
                    $opcion = 'e';
                    if(!in_array(['codigo' => '2403'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para EDITAR.";
                        return json_encode($respuesta);
                    }
                }
                else
                {
                    if(!in_array(['codigo' => '2402'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                        return json_encode($respuesta);
                    }
                }
                // === VALIDATE ===
                try
                {
                    $validator = $this->validate($request,[
                        'nombre'       => 'required|max:255',
                        'municipio'    => 'required',
                        'email'        => 'required|email',
                        'respcontacto' => 'required',
                        'direccion'    => 'required',
                        'telefono'     => 'required',
                    ],
                    [
                        'nombre.required'       => 'El campo NOMBRE es obligatorio',
                        'nombre.max'            => 'El campo NOMBRE debe tener :max caracteres como máximo',
                        'celular.required'      => 'El campo CELULAR es obligatorio',
                        'email.required'        => 'El campo CORREO ELECTRONICO es obligatorio.',
                        'email.email'           => 'El campo CORREO ELECTRONICO no corresponde con una dirección de e-mail válida.',
                        'respcontacto.required' => 'El campo RESPONSABLE/CONTACTO es obligatorio.',
                        'municipio.required'    => 'El campo LUGAR DE NACIMIENTO es obligatorio.'
                    ]);
                }
                catch (Exception $e)
                {
                    $respuesta['error_sw'] = 2;
                    $respuesta['error']    = $e;
                    return json_encode($respuesta);
                }
                //=== OPERACION ===
                $estado          = trim($request->input('estado'));
                $nombre          = strtoupper($util->getNoAcentoNoComilla(trim($request->input('nombre'))));
                $zona            = strtoupper($util->getNoAcentoNoComilla(trim($request->input('zona'))));
                $direccion       = strtoupper($util->getNoAcentoNoComilla(trim($request->input('direccion'))));
                $telefono        = trim($request->input('telefono'));
                $celular         = trim($request->input('celular'));
                $email           = strtolower($util->getNoAcentoNoComilla(trim($request->input('email'))));
                $respcontacto    = strtoupper($util->getNoAcentoNoComilla(trim($request->input('respcontacto'))));
                $municipio_id    = trim($request->input('municipio'));
                $institucion_id  = trim($request->input('institucion'));
                $instituciontipo = trim($request->input('instituciontipo'));
                if($opcion == 'n')
                {
                    $c_nombre = InstInstitucion::where('nombre', '=', $nombre)->count();
                    if($c_nombre < 1)
                    {
                        $correoe = InstInstitucion::where('email', '=', $email)->count();
                        if ($correoe < 1)
                        {
                            $iu               = new InstInstitucion;
                            $iu->estado       = $estado;
                            $iu->nombre       = $nombre;
                            $iu->zona         = $zona;
                            $iu->direccion    = $direccion;
                            $iu->telefono     = $telefono;
                            $iu->celular      = $celular;
                            $iu->email        = $email;
                            $iu->respcontacto = $respcontacto;
                            if ($instituciontipo == '2')
                                $iu->institucion_id = $institucion_id;
                            $iu->ubge_municipios_id = $municipio_id;
                            $iu->save();

                            $respuesta['respuesta'] .= "La INSTITUCION/OFICINA se registro con éxito.";
                            $respuesta['sw']         = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El CORREO ELECTRONICO de la INSTITUCION/OFICINA ya fue registrado.";
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "El NOMBRE de la INSTITUCION/OFICINA ya fue registrado.";
                    }
                }
                else
                {
                    $c_nombre = InstInstitucion::where('nombre', '=', $nombre)->where('id', '<>', $id)->count();
                    if($c_nombre < 1)
                    {
                        $correoe = InstInstitucion::where('email', '=', $email)->where('id', '<>', $id)->count();
                        if ($correoe < 1)
                        {
                            $iu               = InstInstitucion::find($id);
                            $iu->estado       = $estado;
                            $iu->nombre       = $nombre;
                            $iu->zona         = $zona;
                            $iu->direccion    = $direccion;
                            $iu->telefono     = $telefono;
                            $iu->celular      = $celular;
                            $iu->email        = $email;
                            $iu->respcontacto = $respcontacto;
                            if ($instituciontipo == '2')
                                $iu->institucion_id = $institucion_id;
                            $iu->ubge_municipios_id = $municipio_id;
                            $iu->save();

                            $respuesta['respuesta'] .= "El INSTITUCION/OFICINA se edito con éxito.";
                            $respuesta['sw']         = 1;
                            $respuesta['iu']         = 2;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "El CORREO ELECTRONICO de la INSTITUCION/OFICINA ya fue registrado.";
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "El NOMBRE de la INSTITUCION/OFICINA ya fue registrado.";
                    }
                }
                //=== respuesta ===
                // sleep(5);
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
                if ($request->has('q'))
                {
                    $nombre     = $request->input('q');
                    $estado     = trim($request->input('estado'));
                    $page_limit = trim($request->input('page_limit'));

                    $query = InstInstitucion::whereRaw("inst_instituciones.institucion_id is null and inst_instituciones.nombre ilike '%$nombre%'")
                        ->where("inst_instituciones.estado", "=", $estado)
                        ->select(DB::raw("inst_instituciones.id, inst_instituciones.nombre as text"))
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
