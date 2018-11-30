<?php

namespace App\Http\Controllers\I4;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;
use App\Libraries\JqgridMysqlClass;
use App\Libraries\UtilClass;
use App\Libraries\I4Class;
use App\Libraries\SegipClass;

use App\Models\Seguridad\SegPermisoRol;
use App\Models\Seguridad\SegLdUser;
use App\User;

use App\Models\I4\Caso;
use App\Models\I4\CasoFuncionario;
use App\Models\I4\Funcionario;
use App\Models\I4\CasoDelito;
use App\Models\I4\Delito;
use App\Models\I4\Actividad;
use App\Models\I4\TipoActividad;
use App\Models\I4\Persona;
use App\Models\I4\PersonaDelito;
use App\Models\I4\Sexo;
use App\Models\I4\PeligroProcesal;
use App\Models\I4\RecintoCarcelario;
use App\Models\I4\PersonaPeligroProcesal;
use App\Models\I4\Dep;
use App\Models\I4\EtapaCaso;

use App\Models\UbicacionGeografica\UbgeMunicipio;
use App\Models\Rrhh\RrhhPersona;

use Maatwebsite\Excel\Facades\Excel;
use PDF;

use Exception;

class DetencionPreventivaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->no_si = [
            '1' => 'NO',
            '2' => 'SI'
        ];

        $this->dp_estado = [
            '1' => 'SIN DETENCION PREVENTIVA',
            '2' => 'CON DETENCION PREVENTIVA',
            '3' => 'X'
        ];

        $this->tipo_recinto = [
            '1' => 'RECINTO PENITENCIARIO',
            '2' => 'CARCELETA',
            '3' => 'CENTRO DE REHABILITACION JUVENIL'
        ];

        $this->dp_semaforo = [
            '1' => 'VERDE',
            '2' => 'AMARILLO',
            '3' => 'ROJO'
        ];

        $this->sexo = [
            '1' => 'MASCULINO',
            '2' => 'FEMENINO'
        ];

        $this->public_dir = '/storage/rrhh/persona/certificacion';
        $this->public_url = 'storage/rrhh/persona/certificacion/';
    }

    public function index()
    {
        $this->rol_id            = Auth::user()->rol_id;
        $this->grupo_id          = Auth::user()->grupo_id;
        $this->i4_funcionario_id = Auth::user()->i4_funcionario_id;

        $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
            ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
            ->select("seg_permisos.codigo")
            ->get()
            ->toArray();

        if(in_array(['codigo' => '2001'], $this->permisos))
        {
            $data = [
                'rol_id'                 => $this->rol_id,
                'grupo_id'               => $this->grupo_id,
                'i4_funcionario_id'      => $this->i4_funcionario_id,
                'permisos'               => $this->permisos,
                'title'                  => 'Detención preventiva',
                'home'                   => 'Inicio',
                'sistema'                => 'i4',
                'modulo'                 => 'Detención preventiva',
                'title_table'            => 'Detención preventiva',
                'no_si_array'            => $this->no_si,
                'dp_estado_array'        => $this->dp_estado,
                'tipo_recinto_array'     => $this->tipo_recinto,
                'dp_semaforo_array'      => $this->dp_semaforo,
                'sexo_array'             => $this->sexo,
                'peligro_procesal_array' => PeligroProcesal::where('estado', 1)
                                                ->select("id", "nombre")
                                                ->orderBy("nombre")
                                                ->get()
                                                ->toArray(),
                'etapa_caso_array'       => EtapaCaso::where('triton_estado', 1)
                                                ->select(DB::raw("id, UPPER(EtapaCaso) AS nombre"))
                                                ->orderBy("EtapaCaso")
                                                ->get()
                                                ->toArray(),
                'departamento_array'     => Dep::select(DB::raw("id, UPPER(Dep) AS nombre"))
                                                ->orderBy("Dep")
                                                ->get()
                                                ->toArray()
            ];

            return view('i4.detencion_preventiva.detencion_preventiva')->with($data);
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

                $tabla1  = "Caso";
                $tabla2  = "Persona";
                $tabla3  = "Delito";
                $tabla4  = "EtapaCaso";
                $tabla5  = "CasoFuncionario";
                $tabla6  = "Funcionario";
                $tabla7  = "RecintosCarcelarios";
                $tabla8  = "PersonaPeligrosProcesales";
                $tabla9  = "PeligrosProcesales";
                $tabla10 = "CasoDelito";
                $tabla11 = "Division";
                $tabla12 = "Oficina";
                $tabla13 = "Muni";
                $tabla14 = "Dep";

                $select = "
                    $tabla1.id,
                    $tabla1.Caso,
                    $tabla1.CodCasoJuz,
                    $tabla1.FechaDenuncia,
                    $tabla1.EtapaCaso,
                    $tabla1.DelitoPrincipal,
                    $tabla1.triton_modificado,
                    $tabla1.n_detenidos,
                    $tabla1.DivisionFis AS division_id,

                    a2.id AS persona_id,
                    UPPER(a2.Nombres) AS Nombres,
                    UPPER(a2.ApPat) AS ApPat,
                    UPPER(a2.ApMat) AS ApMat,
                    UPPER(a2.ApEsp) AS ApEsp,
                    a2.NumDocId,
                    a2.FechaNac,
                    a2.Sexo,
                    a2.Edad,

                    a2.triton_modificado AS triton_modificado_persona,
                    a2.recinto_carcelario_id,
                    a2.dp_estado,
                    a2.dp_semaforo,
                    a2.dp_semaforo_delito,
                    a2.dp_fecha_detencion_preventiva,
                    a2.dp_fecha_conclusion_detencion,
                    a2.dp_etapa_gestacion_estado,
                    a2.dp_etapa_gestacion_semana,
                    a2.dp_enfermo_terminal_estado,
                    a2.dp_enfermo_terminal_tipo,
                    a2.dp_persona_mayor_65,
                    a2.dp_madre_lactante_1,
                    a2.dp_madre_lactante_1_fecha_nacimiento_menor,
                    a2.dp_custodia_menor_6,
                    a2.dp_custodia_menor_6_fecha_nacimiento_menor,
                    a2.dp_mayor_3,
                    a2.dp_minimo_previsto_delito,
                    a2.dp_pena_menor_4,
                    a2.dp_delito_pena_menor_4,
                    a2.dp_delito_patrimonial_menor_6,
                    a2.dp_etapa_preparatoria_dias_transcurridos_estado,
                    a2.dp_etapa_preparatoria_dias_transcurridos_numero,
                    a2.estado_segip,
                    a2.reincidencia,

                    UPPER(a3.Delito) AS delito_principal,

                    UPPER(a4.EtapaCaso) AS etapa_caso,

                    UPPER(a7.nombre) AS recinto_carcelario,

                    a12.Oficina AS oficina_id,
                    UPPER(a12.Division) AS division,

                    a13.Muni AS municipio_id,
                    UPPER(a13.Oficina) AS oficina,

                    a14.Dep AS departamento_id,
                    UPPER(a14.Muni) AS municipio,

                    UPPER(a15.Dep) AS departamento,

                    UPPER(GROUP_CONCAT(DISTINCT a6.Funcionario ORDER BY a6.Funcionario ASC SEPARATOR '::')) AS funcionario,

                    UPPER(GROUP_CONCAT(DISTINCT a9.nombre ORDER BY a9.nombre ASC SEPARATOR '::')) AS peligro_procesal,
                    UPPER(GROUP_CONCAT(DISTINCT a9.id ORDER BY a9.id ASC SEPARATOR '::')) AS peligro_procesal_id,

                    UPPER(GROUP_CONCAT(DISTINCT a11.Delito ORDER BY a11.Delito ASC SEPARATOR '::')) AS delitos
                ";

                $group_by = "
                    $tabla1.id,
                    $tabla1.Caso,
                    $tabla1.CodCasoJuz,
                    $tabla1.FechaDenuncia,
                    $tabla1.EtapaCaso,
                    $tabla1.DelitoPrincipal,
                    $tabla1.triton_modificado,
                    $tabla1.n_detenidos,
                    $tabla1.DivisionFis,

                    a2.id,
                    a2.Nombres,
                    a2.ApPat,
                    a2.ApMat,
                    a2.ApEsp,
                    a2.NumDocId,
                    a2.FechaNac,
                    a2.Sexo,
                    a2.Edad,

                    a2.triton_modificado,
                    a2.recinto_carcelario_id,
                    a2.dp_estado,
                    a2.dp_semaforo,
                    a2.dp_semaforo_delito,
                    a2.dp_fecha_detencion_preventiva,
                    a2.dp_fecha_conclusion_detencion,
                    a2.dp_etapa_gestacion_estado,
                    a2.dp_etapa_gestacion_semana,
                    a2.dp_enfermo_terminal_estado,
                    a2.dp_enfermo_terminal_tipo,
                    a2.dp_persona_mayor_65,
                    a2.dp_madre_lactante_1,
                    a2.dp_madre_lactante_1_fecha_nacimiento_menor,
                    a2.dp_custodia_menor_6,
                    a2.dp_custodia_menor_6_fecha_nacimiento_menor,
                    a2.dp_mayor_3,
                    a2.dp_minimo_previsto_delito,
                    a2.dp_pena_menor_4,
                    a2.dp_delito_pena_menor_4,
                    a2.dp_delito_patrimonial_menor_6,
                    a2.dp_etapa_preparatoria_dias_transcurridos_estado,
                    a2.dp_etapa_preparatoria_dias_transcurridos_numero,
                    a2.estado_segip,
                    a2.reincidencia,

                    a3.Delito,

                    a4.EtapaCaso,

                    a7.nombre,

                    a12.Oficina,
                    a12.Division,

                    a13.Muni,
                    a13.Oficina,

                    a14.Dep,
                    a14.Muni,

                    a15.Dep
                ";

                $array_where = '';

                $user_id           = Auth::user()->id;
                $grupo_id          = Auth::user()->grupo_id;
                $i4_funcionario_id = Auth::user()->i4_funcionario_id;

                $array_where .= "$tabla1.EstadoCaso=1 AND a2.EstadoLibertad=4 AND a5.FechaBaja IS NULL";
                if($grupo_id == 2 && $i4_funcionario_id != "")
                {
                    $array_where .= " AND a5.funcionario=" . $i4_funcionario_id;
                }
                $array_where .= $jqgrid->getWhere();

                $query1 = Caso::leftJoin("$tabla2 AS a2", "a2.Caso", "=", "$tabla1.id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.DelitoPrincipal")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "$tabla1.EtapaCaso")
                            ->leftJoin("$tabla5 AS a5", "a5.Caso", "=", "$tabla1.id")
                            ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.Funcionario")
                            ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a2.recinto_carcelario_id")
                            ->leftJoin("$tabla8 AS a8", "a8.persona_id", "=", "a2.id")
                            ->leftJoin("$tabla9 AS a9", "a9.id", "=", "a8.peligro_procesal_id")
                            ->leftJoin("$tabla10 AS a10", "a10.Caso", "=", "$tabla1.id")
                            ->leftJoin("$tabla3 AS a11", "a11.id", "=", "a10.Delito")
                            ->leftJoin("$tabla11 AS a12", "a12.id", "=", "$tabla1.DivisionFis")
                            ->leftJoin("$tabla12 AS a13", "a13.id", "=", "a12.Oficina")
                            ->leftJoin("$tabla13 AS a14", "a14.id", "=", "a13.Muni")
                            ->leftJoin("$tabla14 AS a15", "a15.id", "=", "a14.Dep")
                            ->whereRaw($array_where)
                            ->select(DB::raw("a2.id"))
                            ->groupBy(DB::raw("a2.id"))
                            ->get()
                            ->toArray();

                $count = count($query1);

                $limit_offset = $jqgrid->getLimitOffset($count);

                $query = Caso::leftJoin("$tabla2 AS a2", "a2.Caso", "=", "$tabla1.id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.DelitoPrincipal")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "$tabla1.EtapaCaso")
                            ->leftJoin("$tabla5 AS a5", "a5.Caso", "=", "$tabla1.id")
                            ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.Funcionario")
                            ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a2.recinto_carcelario_id")
                            ->leftJoin("$tabla8 AS a8", "a8.persona_id", "=", "a2.id")
                            ->leftJoin("$tabla9 AS a9", "a9.id", "=", "a8.peligro_procesal_id")
                            ->leftJoin("$tabla10 AS a10", "a10.Caso", "=", "$tabla1.id")
                            ->leftJoin("$tabla3 AS a11", "a11.id", "=", "a10.Delito")
                            ->leftJoin("$tabla11 AS a12", "a12.id", "=", "$tabla1.DivisionFis")
                            ->leftJoin("$tabla12 AS a13", "a13.id", "=", "a12.Oficina")
                            ->leftJoin("$tabla13 AS a14", "a14.id", "=", "a13.Muni")
                            ->leftJoin("$tabla14 AS a15", "a15.id", "=", "a14.Dep")
                            ->whereRaw($array_where)
                            ->select(DB::raw($select))
                            ->orderBy($limit_offset['sidx'], $limit_offset['sord'])
                            ->groupBy(DB::raw($group_by))
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
                        'caso_id'             => $row["id"],
                        'etapa_caso_id'       => $row["EtapaCaso"],
                        'delito_principal_id' => $row["DelitoPrincipal"],
                        'triton_modificado'   => $row["triton_modificado"],
                        'division_id'         => $row["division_id"],

                        'sexo_id'                                         => $row["Sexo"],
                        'Edad'                                            => $row["Edad"],
                        'triton_modificado_persona'                       => $row["triton_modificado_persona"],
                        'recinto_carcelario_id'                           => $row["recinto_carcelario_id"],
                        'dp_estado'                                       => $row["dp_estado"],
                        'dp_semaforo'                                     => $row["dp_semaforo"],
                        'dp_semaforo_delito'                              => $row["dp_semaforo_delito"],
                        'dp_etapa_gestacion_estado'                       => $row["dp_etapa_gestacion_estado"],
                        'dp_etapa_gestacion_semana'                       => $row["dp_etapa_gestacion_semana"],
                        'dp_enfermo_terminal_estado'                      => $row["dp_enfermo_terminal_estado"],
                        'dp_enfermo_terminal_tipo'                        => $row["dp_enfermo_terminal_tipo"],
                        'dp_persona_mayor_65'                             => $row["dp_persona_mayor_65"],
                        'dp_madre_lactante_1'                             => $row["dp_madre_lactante_1"],
                        'dp_madre_lactante_1_fecha_nacimiento_menor'      => $row["dp_madre_lactante_1_fecha_nacimiento_menor"],
                        'dp_custodia_menor_6'                             => $row["dp_custodia_menor_6"],
                        'dp_custodia_menor_6_fecha_nacimiento_menor'      => $row["dp_custodia_menor_6_fecha_nacimiento_menor"],
                        'dp_mayor_3'                                      => $row["dp_mayor_3"],
                        'dp_minimo_previsto_delito'                       => $row["dp_minimo_previsto_delito"],
                        'dp_pena_menor_4'                                 => $row["dp_pena_menor_4"],
                        'dp_delito_pena_menor_4'                          => $row["dp_delito_pena_menor_4"],
                        'dp_delito_patrimonial_menor_6'                   => $row["dp_delito_patrimonial_menor_6"],
                        'dp_etapa_preparatoria_dias_transcurridos_estado' => $row["dp_etapa_preparatoria_dias_transcurridos_estado"],
                        'dp_etapa_preparatoria_dias_transcurridos_numero' => $row["dp_etapa_preparatoria_dias_transcurridos_numero"],
                        'estado_segip'                                    => $row["estado_segip"],
                        'reincidencia'                                    => $row["reincidencia"],

                        'oficina_id'      => $row["oficina_id"],
                        'municipio_id'    => $row["municipio_id"],
                        'departamento_id' => $row["departamento_id"],

                        'peligro_procesal_id' => $row["peligro_procesal_id"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["persona_id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',

                        $this->utilitarios(array('tipo' => '3', 'valor1' => $row["estado_segip"], 'valor2' => $row["NumDocId"])),

                        $this->utilitarios(array('tipo' => '1', 'valor' => $row["dp_semaforo"])),
                        $this->utilitarios(array('tipo' => '1', 'valor' => $row["dp_semaforo_delito"])),
                        $row["n_detenidos"],
                        $this->dp_estado[$row["dp_estado"]],
                        $row["Caso"],
                        $row["CodCasoJuz"],
                        $row["departamento"],

                        $row["NumDocId"],
                        $row["ApPat"],
                        $row["ApMat"],
                        $row["ApEsp"],
                        $row["Nombres"],
                        $row["FechaNac"],
                        ($row["Sexo"] =="") ? "" : $this->sexo[$row["Sexo"]],

                        $row["FechaDenuncia"],
                        $row["delito_principal"],
                        $this->utilitarios(array('tipo' => '51', 'valor' => $row["delitos"])),

                        $row["dp_fecha_detencion_preventiva"],
                        $row["dp_fecha_conclusion_detencion"],
                        $row["etapa_caso"],
                        $this->utilitarios(array('tipo' => '51', 'valor' => $row["peligro_procesal"])),

                        $row["recinto_carcelario"],
                        $this->utilitarios(array('tipo' => '51', 'valor' => $row["funcionario"])),

                        $row["municipio"],
                        $row["oficina"],
                        $row["division"],
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
                    $i4   = new I4Class();

                // === INICIALIZACION DE VARIABLES ===
                    $data1     = array();
                    $respuesta = array(
                        'sw'         => 0,
                        'titulo'     => '<div class="text-center"><strong>Caracteristicas del detenido</strong></div>',
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
                        if(!in_array(['codigo' => '2003'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para MODIFICAR.";
                            return json_encode($respuesta);
                        }
                    }
                    else
                    {
                        if(!in_array(['codigo' => '2002'], $this->permisos))
                        {
                            $respuesta['respuesta'] .= "No tiene permiso para REGISTRAR.";
                            return json_encode($respuesta);
                        }
                    }

                // === VALIDATE ===
                    // try
                    // {
                    //     $validator = $this->validate($request,[
                    //         'Muni_id'      => 'required',
                    //         'tipo_recinto' => 'required',
                    //         'nombre'       => 'required|max: 500'
                    //     ],
                    //     [
                    //         'Muni_id.required' => 'El campo UBICACION es obligatorio.',

                    //         'tipo_recinto.required' => 'El campo TIPO DE RECINTO es obligatorio.',

                    //         'nombre.required' => 'El campo NOMBRE es obligatorio.',
                    //         'nombre.max'     => 'El campo NOMBRE debe contener :max caracteres como máximo.'
                    //     ]);
                    // }
                    // catch (Exception $e)
                    // {
                    //     $respuesta['error_sw'] = 2;
                    //     $respuesta['error']    = $e;
                    //     return json_encode($respuesta);
                    // }

                // === OPERACION ===
                    $data1['caso_id']    = trim($request->input('caso_id'));
                    $data1['CodCasoJuz'] = trim($request->input('CodCasoJuz'));

                    $data1['NumDocId'] = trim($request->input('NumDocId'));
                    $data1['FechaNac'] = trim($request->input('FechaNac'));
                    $data1['ApPat']    = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ApPat'))));
                    $data1['ApMat']    = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ApMat'))));
                    $data1['ApEsp']    = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ApEsp'))));
                    $data1['Nombres']  = strtoupper($util->getNoAcentoNoComilla(trim($request->input('Nombres'))));
                    $data1['sexo_id']  = trim($request->input('sexo_id'));

                    $data1['peligro_procesal_id']           = $request->input('peligro_procesal_id');
                    $data1['dp_fecha_detencion_preventiva'] = trim($request->input('dp_fecha_detencion_preventiva'));
                    $data1['dp_fecha_conclusion_detencion'] = trim($request->input('dp_fecha_conclusion_detencion'));
                    $data1['recinto_carcelario_id']         = trim($request->input('recinto_carcelario_id'));

                    $data1['dp_etapa_gestacion_estado'] = trim($request->input('dp_etapa_gestacion_estado'));
                    $data1['dp_etapa_gestacion_semana'] = trim($request->input('dp_etapa_gestacion_semana'));

                    $data1['dp_enfermo_terminal_estado'] = trim($request->input('dp_enfermo_terminal_estado'));
                    $data1['dp_enfermo_terminal_tipo']   = strtoupper($util->getNoAcentoNoComilla(trim($request->input('dp_enfermo_terminal_tipo'))));

                    $data1['dp_madre_lactante_1']                        = trim($request->input('dp_madre_lactante_1'));
                    $data1['dp_madre_lactante_1_fecha_nacimiento_menor'] = trim($request->input('dp_madre_lactante_1_fecha_nacimiento_menor'));

                    $data1['dp_custodia_menor_6']                        = trim($request->input('dp_custodia_menor_6'));
                    $data1['dp_custodia_menor_6_fecha_nacimiento_menor'] = trim($request->input('dp_custodia_menor_6_fecha_nacimiento_menor'));

                    $data1['reincidencia'] = trim($request->input('reincidencia'));

                // === CONVERTIR VALORES VACIOS A NULL ===
                    foreach ($data1 as $llave => $valor)
                    {
                        if ($valor == '')
                            $data1[$llave] = NULL;
                    }

                // === REGISTRAR MODIFICAR VALORES ===
                    if($opcion == 'n')
                    {
                    }
                    else
                    {

                        $iu             = Caso::find($data1['caso_id']);
                        $iu->CodCasoJuz = $data1['CodCasoJuz'];
                        $iu->save();

                        $dp_semaforo = 1;

                        $persona_mayor_65 = $i4->getPersonaMayor65(["FechaNac" => $data1['FechaNac']]);

                        $iu           = Persona::find($id);
                        $iu->NumDocId = $data1['NumDocId'];
                        $iu->FechaNac = $data1['FechaNac'];
                        $iu->ApPat    = $data1['ApPat'];
                        $iu->ApMat    = $data1['ApMat'];
                        $iu->ApEsp    = $data1['ApEsp'];
                        $iu->Nombres  = $data1['Nombres'];
                        $iu->Sexo     = $data1['sexo_id'];

                        $iu->dp_fecha_detencion_preventiva = $data1['dp_fecha_detencion_preventiva'];
                        $iu->dp_fecha_conclusion_detencion = $data1['dp_fecha_conclusion_detencion'];
                        $iu->recinto_carcelario_id         = $data1['recinto_carcelario_id'];

                        if($data1['dp_etapa_gestacion_estado'] != NULL && $data1['sexo_id'] == '2')
                        {
                            $iu->dp_etapa_gestacion_estado = $data1['dp_etapa_gestacion_estado'];
                            $iu->dp_etapa_gestacion_semana = $data1['dp_etapa_gestacion_semana'];
                            $dp_semaforo                   = 2;
                        }
                        else
                        {
                            $iu->dp_etapa_gestacion_estado = 1;
                            $iu->dp_etapa_gestacion_semana = NULL;
                        }

                        if($data1['dp_enfermo_terminal_estado'] != NULL)
                        {
                            $iu->dp_enfermo_terminal_estado = $data1['dp_enfermo_terminal_estado'];
                            $iu->dp_enfermo_terminal_tipo   = $data1['dp_enfermo_terminal_tipo'];
                            $dp_semaforo                    = 2;
                        }
                        else
                        {
                            $iu->dp_enfermo_terminal_estado = 1;
                            $iu->dp_enfermo_terminal_tipo   = NULL;
                        }

                        if($data1['dp_madre_lactante_1'] != NULL && $data1['sexo_id'] == '2')
                        {
                            $iu->dp_madre_lactante_1                        = $data1['dp_madre_lactante_1'];
                            $iu->dp_madre_lactante_1_fecha_nacimiento_menor = $data1['dp_madre_lactante_1_fecha_nacimiento_menor'];
                            $dp_semaforo                                    = 2;
                        }
                        else
                        {
                            $iu->dp_madre_lactante_1                        = 1;
                            $iu->dp_madre_lactante_1_fecha_nacimiento_menor = NULL;
                        }

                        if($data1['dp_custodia_menor_6'] != NULL)
                        {
                            $iu->dp_custodia_menor_6                        = $data1['dp_custodia_menor_6'];
                            $iu->dp_custodia_menor_6_fecha_nacimiento_menor = $data1['dp_custodia_menor_6_fecha_nacimiento_menor'];
                            $dp_semaforo                                    = 2;
                        }
                        else
                        {
                            $iu->dp_custodia_menor_6                        = 1;
                            $iu->dp_custodia_menor_6_fecha_nacimiento_menor = NULL;
                        }

                        if($data1['reincidencia'] != NULL)
                        {
                            $iu->reincidencia = $data1['reincidencia'];
                        }

                        if($persona_mayor_65["edad_sw"])
                        {
                            $iu->dp_persona_mayor_65 = 2;
                            $dp_semaforo             = 2;
                        }
                        else
                        {
                            $iu->dp_persona_mayor_65 = 1;
                        }
                        $iu->Edad = $persona_mayor_65["edad"];

                        $iu->dp_semaforo = $dp_semaforo;

                        $iu->save();

                        PersonaPeligroProcesal::where('persona_id', '=', $id)->delete();

                        if($data1['peligro_procesal_id'] != NULL)
                        {
                            $peligro_procesal_id_array = explode(",", $data1['peligro_procesal_id']);
                            foreach ($peligro_procesal_id_array as $row1)
                            {
                                $iu                      = new PersonaPeligroProcesal;
                                $iu->persona_id          = $id;
                                $iu->peligro_procesal_id = $row1;
                                $iu->save();
                            }
                        }

                        $respuesta['respuesta'] .= "Las CARACTERISTICAS DEL DETENIDO se modificaron con éxito.";
                        $respuesta['sw']         = 1;
                        $respuesta['iu']         = 2;
                    }
                return json_encode($respuesta);
                break;
            // === CONSULTA SEGIP ===
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
                        'titulo'     => '<div class="text-center"><strong>VALIDACION SEGIP</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'pdf'        => ""
                    );
                    $error  = FALSE;

                // === LIBRERIAS ===
                    $util = new UtilClass();

                // === PERMISOS ===
                    if(!in_array(['codigo' => '2003'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para la CONSULTA DEL SEGIP.";
                        return json_encode($respuesta);
                    }

                // === REQUEST ===
                    $id = trim($request->input('persona_id'));
                    if($id == '')
                    {
                        $respuesta['respuesta'] .= "Seleccione una persona.";
                        return json_encode($respuesta);
                    }

                    $data1['NumDocId']     = strtoupper($util->getNoAcentoNoComilla(trim($request->input('NumDocId'))));
                    $data1['FechaNac']     = trim($request->input('FechaNac'));
                    $data1['ApPat']        = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ApPat'))));
                    $data1['ApMat']        = strtoupper($util->getNoAcentoNoComilla(trim($request->input('ApMat'))));
                    $data1['Nombres']      = strtoupper($util->getNoAcentoNoComilla(trim($request->input('Nombres'))));
                    $data1['estado_segip'] = trim($request->input('estado_segip'));
                    $data1['sexo']         = trim($request->input('sexo'));

                // === OPERACION ===
                    $consulta1 = RrhhPersona::where('n_documento', '=', $data1['NumDocId'])->first();
                    if(count($consulta1) > 0)
                    {
                        if($consulta1->estado_segip == 1)
                        {
                            $n_documento_array = explode('-', $consulta1['n_documento']);
                            if(isset($n_documento_array[1]))
                            {
                                $complemento =  $n_documento_array[1];
                            }
                            else
                            {
                                $complemento = "";
                            }

                            $data2 = [
                                "n_documento"  => $n_documento_array[0],
                                "complemento"  => $complemento,
                                "nombre"       => $data1['Nombres'],
                                "ap_paterno"   => $data1['ApPat'],
                                "ap_materno"   => $data1['ApMat'],
                                "f_nacimiento" => $data1['FechaNac']
                            ];

                            $segip = new SegipClass();

                            $segip_certificacion = $segip->getCertificacionSegip($data2);

                            if($segip_certificacion['sw'] == '1')
                            {
                                $respuesta['respuesta'] .= $segip_certificacion['respuesta']['ConsultaDatoPersonaCertificacionResult']['Mensaje'];
                                $respuesta['respuesta'] .= "<br>" . $segip_certificacion['respuesta']['ConsultaDatoPersonaCertificacionResult']['DescripcionRespuesta'];

                                if($segip_certificacion['respuesta']['ConsultaDatoPersonaCertificacionResult']['CodigoRespuesta'] == '2')
                                {
                                    $file_name = uniqid('certificacion_segip_', true) . ".pdf";
                                    $file      = public_path($this->public_dir) . "/" . $file_name;
                                    file_put_contents($file, base64_decode($segip_certificacion['respuesta']['ConsultaDatoPersonaCertificacionResult']['ReporteCertificacion']));

                                    $iu                           = RrhhPersona::find($consulta1->id);
                                    $iu->estado_segip             = 2;
                                    $iu->certificacion_segip      = $segip_certificacion['respuesta']['ConsultaDatoPersonaCertificacionResult']['ReporteCertificacion'];
                                    $iu->certificacion_file_segip = $file_name;
                                    $iu->save();

                                    $iu                     = Persona::find($id);
                                    $iu->estado_segip       = 2;
                                    $iu->CertificacionSegip = $segip_certificacion['respuesta']['ConsultaDatoPersonaCertificacionResult']['ReporteCertificacion'];
                                    $iu->save();

                                    $respuesta['pdf'] .= $segip_certificacion['respuesta']['ConsultaDatoPersonaCertificacionResult']['ReporteCertificacion'];
                                    $respuesta['respuesta'] .= "<br>Se VALIDO POR EL SEGIP.";
                                    $respuesta['sw']         = 1;
                                }
                                else
                                {
                                    $respuesta['respuesta'] .= "<br>No se VALIDO POR EL SEGIP.";
                                }
                            }
                            else
                            {
                                $respuesta['respuesta'] .= $segip_certificacion['respuesta'];
                                return json_encode($respuesta);
                            }
                        }
                        else
                        {
                            $my_bytea  = stream_get_contents($consulta1->certificacion_segip);
                            $my_string = pg_unescape_bytea($my_bytea);
                            $html_data = htmlspecialchars($my_string);

                            $respuesta['pdf'] .= $html_data;

                            $iu                     = Persona::find($id);
                            $iu->estado_segip       = 2;
                            $iu->CertificacionSegip = $html_data;
                            $iu->save();

                            $respuesta['respuesta'] .= "<br>Se VALIDO POR EL SEGIP.";
                            $respuesta['sw']         = 1;
                        }
                    }
                    else
                    {
                        $n_documento_array = explode('-', $data1['NumDocId']);
                        if(isset($n_documento_array[1]))
                        {
                            $complemento =  $n_documento_array[1];
                        }
                        else
                        {
                            $complemento = "";
                        }

                        $data2 = [
                            "n_documento"  => $n_documento_array[0],
                            "complemento"  => $complemento,
                            "nombre"       => $data1['Nombres'],
                            "ap_paterno"   => $data1['ApPat'],
                            "ap_materno"   => $data1['ApMat'],
                            "f_nacimiento" => $data1['FechaNac']
                        ];

                        $segip = new SegipClass();

                        $segip_certificacion = $segip->getCertificacionSegip($data2);

                        if($segip_certificacion['sw'] == '1')
                        {
                            $respuesta['respuesta'] .= $segip_certificacion['respuesta']['ConsultaDatoPersonaCertificacionResult']['Mensaje'];
                            $respuesta['respuesta'] .= "<br>" . $segip_certificacion['respuesta']['ConsultaDatoPersonaCertificacionResult']['DescripcionRespuesta'];

                            if($segip_certificacion['respuesta']['ConsultaDatoPersonaCertificacionResult']['CodigoRespuesta'] == '2')
                            {
                                $file_name = uniqid('certificacion_segip_', true) . ".pdf";
                                $file      = public_path($this->public_dir) . "/" . $file_name;
                                file_put_contents($file, base64_decode($segip_certificacion['respuesta']['ConsultaDatoPersonaCertificacionResult']['ReporteCertificacion']));

                                if($data1['sexo'] == '1')
                                {
                                    $data1['sexo'] = 'M';
                                }
                                else
                                {
                                    $data1['sexo'] = 'F';
                                }

                                $iu                           = new RrhhPersona;
                                $iu->n_documento              = $data1['NumDocId'];
                                $iu->nombre                   = $data1['Nombres'];
                                $iu->ap_paterno               = $data1['ApPat'];
                                $iu->ap_materno               = $data1['ApMat'];
                                $iu->f_nacimiento             = $data1['FechaNac'];
                                $iu->sexo                     = $data1['sexo'];
                                $iu->estado_segip             = 2;
                                $iu->certificacion_segip      = $segip_certificacion['respuesta']['ConsultaDatoPersonaCertificacionResult']['ReporteCertificacion'];
                                $iu->certificacion_file_segip = $file_name;
                                $iu->save();

                                $iu                     = Persona::find($id);
                                $iu->estado_segip       = 2;
                                $iu->CertificacionSegip = $segip_certificacion['respuesta']['ConsultaDatoPersonaCertificacionResult']['ReporteCertificacion'];
                                $iu->save();

                                $respuesta['pdf']       .= $segip_certificacion['respuesta']['ConsultaDatoPersonaCertificacionResult']['ReporteCertificacion'];
                                $respuesta['respuesta'] .= "<br>Se VALIDO POR EL SEGIP.";
                                $respuesta['sw']         = 1;
                            }
                            else
                            {
                                $respuesta['respuesta'] .= "<br>No se VALIDO POR EL SEGIP.";
                            }
                        }
                        else
                        {
                            $respuesta['respuesta'] .= $segip_certificacion['respuesta'];
                            return json_encode($respuesta);
                        }
                    }

                //=== RESPUESTA ===
                    return json_encode($respuesta);
                break;
            // === CONSULTA SEGIP ===
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
                        'titulo'     => '<div class="text-center"><strong>VALIDACION SEGIP</strong></div>',
                        'respuesta'  => '',
                        'tipo'       => $tipo,
                        'pdf'        => ""
                    );
                    $error  = FALSE;

                // === LIBRERIAS ===
                    $util = new UtilClass();

                // === PERMISOS ===
                    if(!in_array(['codigo' => '2003'], $this->permisos))
                    {
                        $respuesta['respuesta'] .= "No tiene permiso para la CONSULTA DEL SEGIP.";
                        return json_encode($respuesta);
                    }

                // === REQUEST ===
                    $data1['n_documento'] = strtoupper($util->getNoAcentoNoComilla(trim($request->input('n_documento'))));

                // === OPERACION ===
                    $consulta1 = RrhhPersona::where('n_documento', '=', $data1['n_documento'])->first();
                    if(count($consulta1) > 0)
                    {
                        $my_bytea  = stream_get_contents($consulta1->certificacion_segip);
                        $my_string = pg_unescape_bytea($my_bytea);
                        $html_data = htmlspecialchars($my_string);

                        $respuesta['pdf'] .= $html_data;
                        $respuesta['respuesta'] .= "<br>Se VALIDO POR EL SEGIP.";
                        $respuesta['sw']         = 1;
                    }
                //=== RESPUESTA ===
                    return json_encode($respuesta);
                break;
            // === SELECT2 DEPARTAMENTO, MUNICIPIO, RECINTO CARCELARIO  ===
            case '101':
                if($request->has('q'))
                {
                    $nombre     = $request->input('q');
                    $estado     = trim($request->input('estado'));
                    $page_limit = trim($request->input('page_limit'));

                    $query = RecintoCarcelario::leftJoin("Muni", "Muni.id", "=", "RecintosCarcelarios.Muni_id")
                                ->leftJoin("Dep", "Dep.id", "=", "Muni.Dep")
                                ->whereRaw("CONCAT_WS(', ', Dep.Dep, Muni.Muni, RecintosCarcelarios.nombre) LIKE '%$nombre%'")
                                ->where("RecintosCarcelarios.estado", "=", $estado)
                                ->select(DB::raw("RecintosCarcelarios.id, UPPER(CONCAT_WS(', ', Dep.Dep, Muni.Muni, RecintosCarcelarios.nombre)) AS text"))
                                ->orderByRaw("Dep.Dep ASC, Muni.Muni ASC, RecintosCarcelarios.nombre ASC")
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
            case '10':
                // === SEGURIDAD ===
                    $this->rol_id   = Auth::user()->rol_id;
                    $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                                        ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                                        ->select("seg_permisos.codigo")
                                        ->get()
                                        ->toArray();

                // === INICIALIZACION DE VARIABLES ===
                    $data1 = array();

                // === PERMISOS ===
                    if(!in_array(['codigo' => '2004'], $this->permisos))
                    {
                        return "No tiene permiso para GENERAR REPORTES.";
                    }

                //=== CONSULTA BASE DE DATOS ===
                    $user_id           = Auth::user()->id;
                    $grupo_id          = Auth::user()->grupo_id;
                    $i4_funcionario_id = Auth::user()->i4_funcionario_id;
                    if($grupo_id == 2 && $i4_funcionario_id != "")
                    {
                        $tabla1  = "Caso";
                        $tabla2  = "Persona";
                        $tabla3  = "Delito";
                        $tabla4  = "EtapaCaso";
                        $tabla5  = "CasoFuncionario";
                        $tabla6  = "Funcionario";
                        $tabla7  = "RecintosCarcelarios";
                        $tabla8  = "PersonaPeligrosProcesales";
                        $tabla9  = "PeligrosProcesales";
                        $tabla10 = "CasoDelito";
                        $tabla11 = "Division";
                        $tabla12 = "Oficina";
                        $tabla13 = "Muni";
                        $tabla14 = "Dep";

                        $select = "
                            $tabla1.id,
                            $tabla1.Caso,
                            $tabla1.CodCasoJuz,
                            $tabla1.FechaDenuncia,
                            $tabla1.EtapaCaso,
                            $tabla1.DelitoPrincipal,
                            $tabla1.triton_modificado,
                            $tabla1.n_detenidos,
                            $tabla1.DivisionFis AS division_id,

                            a2.id AS persona_id,
                            UPPER(a2.Nombres) AS Nombres,
                            UPPER(a2.ApPat) AS ApPat,
                            UPPER(a2.ApMat) AS ApMat,
                            UPPER(a2.ApEsp) AS ApEsp,
                            a2.NumDocId,
                            a2.FechaNac,
                            a2.Sexo,
                            a2.Edad,

                            a2.triton_modificado AS triton_modificado_persona,
                            a2.recinto_carcelario_id,
                            a2.dp_estado,
                            a2.dp_semaforo,
                            a2.dp_semaforo_delito,
                            a2.dp_fecha_detencion_preventiva,
                            a2.dp_fecha_conclusion_detencion,
                            a2.dp_etapa_gestacion_estado,
                            a2.dp_etapa_gestacion_semana,
                            a2.dp_enfermo_terminal_estado,
                            a2.dp_enfermo_terminal_tipo,
                            a2.dp_persona_mayor_65,
                            a2.dp_madre_lactante_1,
                            a2.dp_madre_lactante_1_fecha_nacimiento_menor,
                            a2.dp_custodia_menor_6,
                            a2.dp_custodia_menor_6_fecha_nacimiento_menor,
                            a2.dp_mayor_3,
                            a2.dp_minimo_previsto_delito,
                            a2.dp_pena_menor_4,
                            a2.dp_delito_pena_menor_4,
                            a2.dp_delito_patrimonial_menor_6,
                            a2.dp_etapa_preparatoria_dias_transcurridos_estado,
                            a2.dp_etapa_preparatoria_dias_transcurridos_numero,

                            UPPER(a3.Delito) AS delito_principal,

                            UPPER(a4.EtapaCaso) AS etapa_caso,

                            UPPER(a7.nombre) AS recinto_carcelario,

                            a12.Oficina AS oficina_id,
                            UPPER(a12.Division) AS division,

                            a13.Muni AS municipio_id,
                            UPPER(a13.Oficina) AS oficina,

                            a14.Dep AS departamento_id,
                            UPPER(a14.Muni) AS municipio,

                            UPPER(a15.Dep) AS departamento,

                            UPPER(GROUP_CONCAT(DISTINCT a6.Funcionario ORDER BY a6.Funcionario ASC SEPARATOR '::')) AS funcionario,

                            UPPER(GROUP_CONCAT(DISTINCT a9.nombre ORDER BY a9.nombre ASC SEPARATOR '::')) AS peligro_procesal,
                            UPPER(GROUP_CONCAT(DISTINCT a9.id ORDER BY a9.id ASC SEPARATOR '::')) AS peligro_procesal_id,

                            UPPER(GROUP_CONCAT(DISTINCT a11.Delito ORDER BY a11.Delito ASC SEPARATOR '::')) AS delitos
                        ";

                        $group_by = "
                            $tabla1.id,
                            $tabla1.Caso,
                            $tabla1.CodCasoJuz,
                            $tabla1.FechaDenuncia,
                            $tabla1.EtapaCaso,
                            $tabla1.DelitoPrincipal,
                            $tabla1.triton_modificado,
                            $tabla1.n_detenidos,
                            $tabla1.DivisionFis,

                            a2.id,
                            a2.Nombres,
                            a2.ApPat,
                            a2.ApMat,
                            a2.ApEsp,
                            a2.NumDocId,
                            a2.FechaNac,
                            a2.Sexo,
                            a2.Edad,

                            a2.triton_modificado,
                            a2.recinto_carcelario_id,
                            a2.dp_estado,
                            a2.dp_semaforo,
                            a2.dp_semaforo_delito,
                            a2.dp_fecha_detencion_preventiva,
                            a2.dp_fecha_conclusion_detencion,
                            a2.dp_etapa_gestacion_estado,
                            a2.dp_etapa_gestacion_semana,
                            a2.dp_enfermo_terminal_estado,
                            a2.dp_enfermo_terminal_tipo,
                            a2.dp_persona_mayor_65,
                            a2.dp_madre_lactante_1,
                            a2.dp_madre_lactante_1_fecha_nacimiento_menor,
                            a2.dp_custodia_menor_6,
                            a2.dp_custodia_menor_6_fecha_nacimiento_menor,
                            a2.dp_mayor_3,
                            a2.dp_minimo_previsto_delito,
                            a2.dp_pena_menor_4,
                            a2.dp_delito_pena_menor_4,
                            a2.dp_delito_patrimonial_menor_6,
                            a2.dp_etapa_preparatoria_dias_transcurridos_estado,
                            a2.dp_etapa_preparatoria_dias_transcurridos_numero,

                            a3.Delito,

                            a4.EtapaCaso,

                            a7.nombre,

                            a12.Oficina,
                            a12.Division,

                            a13.Muni,
                            a13.Oficina,

                            a14.Dep,
                            a14.Muni,

                            a15.Dep
                        ";

                        $array_where = "$tabla1.EstadoCaso=1 AND a2.EstadoLibertad=4 AND a5.FechaBaja IS NULL";
                        $array_where .= " AND a5.funcionario=" . $i4_funcionario_id;

                        $consulta1 = Caso::leftJoin("$tabla2 AS a2", "a2.Caso", "=", "$tabla1.id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.DelitoPrincipal")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "$tabla1.EtapaCaso")
                            ->leftJoin("$tabla5 AS a5", "a5.Caso", "=", "$tabla1.id")
                            ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.Funcionario")
                            ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a2.recinto_carcelario_id")
                            ->leftJoin("$tabla8 AS a8", "a8.persona_id", "=", "a2.id")
                            ->leftJoin("$tabla9 AS a9", "a9.id", "=", "a8.peligro_procesal_id")
                            ->leftJoin("$tabla10 AS a10", "a10.Caso", "=", "$tabla1.id")
                            ->leftJoin("$tabla3 AS a11", "a11.id", "=", "a10.Delito")
                            ->leftJoin("$tabla11 AS a12", "a12.id", "=", "$tabla1.DivisionFis")
                            ->leftJoin("$tabla12 AS a13", "a13.id", "=", "a12.Oficina")
                            ->leftJoin("$tabla13 AS a14", "a14.id", "=", "a13.Muni")
                            ->leftJoin("$tabla14 AS a15", "a15.id", "=", "a14.Dep")
                            ->whereRaw($array_where)
                            ->select(DB::raw($select))
                            ->orderBy("$tabla1.FechaDenuncia", "ASC")
                            ->groupBy(DB::raw($group_by))
                            ->get()
                            ->toArray();

                    }
                //=== EXCEL ===
                    if(count($consulta1) > 0)
                    {
                        set_time_limit(3600);
                        ini_set('memory_limit','-1');
                        Excel::create('personas_detenidas_' . date('Y-m-d_H-i-s'), function($excel) use($consulta1){
                            $excel->sheet('Personas detenidas', function($sheet) use($consulta1){
                                $sheet->row(1, [
                                    'SEMAFORO',
                                    'SEMAFORO DELITO',
                                    'NUMERO DE DETENIDOS',
                                    'NUMERO DE CASO',
                                    'IANUS / NUREJ',
                                    'DEPARTAMENTO',

                                    'DOCUMENTO DE IDENTIDAD',
                                    'APELLIDO PATERNO',
                                    'APELLIDO MATERNO',
                                    'APELLIDO ESPOSO',
                                    'NOMBRE(S)',
                                    'FECHA DE NACIMIENTO',
                                    'EDAD',
                                    'SEXO',

                                    'FECHA DENUNCIA',
                                    'DELITO PRINCIPAL',
                                    'DELITOS',

                                    'FECHA DE LA DETENCION',
                                    'FECHA DE LA CONCLUSION DE LA DETENCION',
                                    'ETAPA',
                                    'PELIGRO PROCESAL',

                                    'RECINTO CARCELARIO',

                                    'FISCAL RESPONSABLE',

                                    'MUNICIPIO',
                                    'OFICINA',
                                    'DIVISION'

                                    // '¿MUJER GESTANTE?',
                                    // 'SEMANAS DE GESTACION',

                                    // '¿CON ENFERMEDAD TERMINAL?',
                                    // 'TIPO DE ENFERMEDAD TERMINAL',

                                    // '¿MAYOR A 65 AÑOS?',
                                    // 'EDAD',

                                    // '¿MADRE DE MENOR LACTANTE A UN AÑO?',
                                    // 'FECHA DE NACIMIENTO DEL MENOR',

                                    // 'CUSTODIA A MENOR DE SEIS AÑOS?',
                                    // 'FECHA DE NACIMIENTO DEL MENOR',

                                    // 'CUSTODIA A MENOR DE SEIS AÑOS?',
                                    // 'FECHA DE NACIMIENTO DEL MENOR',

                                    // 'CUSTODIA A MENOR DE SEIS AÑOS?',
                                    // 'FECHA DE NACIMIENTO DEL MENOR',

                                    // 'CUSTODIA A MENOR DE SEIS AÑOS?',
                                    // 'FECHA DE NACIMIENTO DEL MENOR',

                                    // 'CUSTODIA A MENOR DE SEIS AÑOS?',
                                    // 'FECHA DE NACIMIENTO DEL MENOR'
                                ]);

                                $sheet->row(1, function($row){
                                    $row->setBackground('#CCCCCC');
                                    $row->setFontWeight('bold');
                                    $row->setAlignment('center');
                                });

                                $sheet->freezeFirstRow();
                                $sheet->setAutoFilter();

                                $sw = FALSE;
                                $c  = 1;

                                foreach($consulta1 as $index1 => $row1)
                                {
                                    $sheet->row($c+1, [
                                        $this->dp_semaforo[$row1["dp_semaforo"]],
                                        $this->dp_semaforo[$row1["dp_semaforo_delito"]],
                                        $row1["n_detenidos"],
                                        $row1["Caso"],
                                        $row1["CodCasoJuz"],
                                        $row1["departamento"],

                                        $row1["NumDocId"],
                                        $row1["ApPat"],
                                        $row1["ApMat"],
                                        $row1["ApEsp"],
                                        $row1["Nombres"],
                                        $row1["FechaNac"],
                                        $row1["Edad"],
                                        ($row1["Sexo"] =="") ? "" : $this->sexo[$row1["Sexo"]],

                                        $row1["FechaDenuncia"],
                                        $row1["delito_principal"],
                                        $row1["delitos"],

                                        $row1["dp_fecha_detencion_preventiva"],
                                        $row1["dp_fecha_conclusion_detencion"],
                                        $row1["etapa_caso"],
                                        $row1["peligro_procesal"],

                                        $row1["recinto_carcelario"],

                                        $row1["funcionario"],

                                        $row1["municipio"],
                                        $row1["oficina"],
                                        $row1["division"]
                                    ]);

                                    $c++;

                                    // if($row1["estado_segip"] == 2)
                                    // {
                                    //     $sheet->getCell('C' . $c)
                                    //         ->getHyperlink()
                                    //         ->setUrl(url($this->public_url . $row1['certificacion_file_segip']))
                                    //         ->setTooltip('Haga clic aquí para acceder al PDF.');
                                    // }

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

                                $sheet->cells('A2:Z' . ($c), function($cells){
                                    $cells->setAlignment('center');
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

            case '100':
                // === SEGURIDAD ===
                    $this->rol_id   = Auth::user()->rol_id;
                    $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
                                        ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
                                        ->select("seg_permisos.codigo")
                                        ->get()
                                        ->toArray();

                // === INICIALIZACION DE VARIABLES ===
                    $data1 = array();

                // === PERMISOS ===
                    // if(!in_array(['codigo' => '2004'], $this->permisos))
                    // {
                    //     return "No tiene permiso para GENERAR REPORTES.";
                    // }

                //=== CONSULTA BASE DE DATOS ===
                    // $user_id           = Auth::user()->id;

                    set_time_limit(3600);
                    ini_set('memory_limit','-1');

                    $tabla1 = "Caso";
                    $tabla2 = "Delito";
                    $tabla3 = "EtapaCaso";
                    $tabla4 = "Division";
                    $tabla5 = "Oficina";
                    $tabla6 = "Muni";
                    $tabla7 = "Dep";

                    $select = "
                        $tabla1.id,
                        $tabla1.Caso,
                        $tabla1.CodCasoJuz,
                        $tabla1.FechaDenuncia,
                        $tabla1.EtapaCaso,
                        $tabla1.DelitoPrincipal,
                        $tabla1.triton_modificado,
                        $tabla1.n_detenidos,
                        $tabla1.DivisionFis AS division_id,

                        UPPER(a2.Delito) AS delito_principal,

                        UPPER(a3.EtapaCaso) AS etapa_caso,

                        a6.Dep AS departamento_id,
                        UPPER(a6.Muni) AS municipio,

                        UPPER(a7.Dep) AS departamento
                    ";

                    $array_where = "$tabla1.Tentativa <> 1 AND a2.id = 9028";

                    $consulta1 = Caso::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.DelitoPrincipal")
                        ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.EtapaCaso")
                        ->leftJoin("$tabla4 AS a4", "a4.id", "=", "$tabla1.DivisionFis")
                        ->leftJoin("$tabla5 AS a5", "a5.id", "=", "a4.Oficina")
                        ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.Muni")
                        ->leftJoin("$tabla7 AS a7", "a7.id", "=", "a6.Dep")
                        ->whereRaw($array_where)
                        ->select(DB::raw($select))
                        ->orderBy("$tabla1.FechaDenuncia", "ASC")
                        ->get()
                        ->toArray();


                //=== EXCEL ===
                    if(count($consulta1) > 0)
                    {
                        set_time_limit(3600);
                        ini_set('memory_limit','-1');
                        Excel::create('feminicidio_' . date('Y-m-d_H-i-s'), function($excel) use($consulta1){
                            $excel->sheet('Feminicidios', function($sheet) use($consulta1){
                                $sheet->row(1, [
                                    'NUMERO DE CASO',
                                    'DEPARTAMENTO',
                                    'MUNICIPIO',
                                    'ETAPA DEL CASO',
                                    'GESTION DE LA DENUNCIA',
                                    'FECHA DE LA DENUNCIA',
                                    'DELITO',
                                    'VICTIMA',
                                    'FISCAL A CARGO'
                                ]);

                                $sheet->row(1, function($row){
                                    $row->setBackground('#CCCCCC');
                                    $row->setFontWeight('bold');
                                    $row->setAlignment('center');
                                });

                                $sheet->freezeFirstRow();
                                $sheet->setAutoFilter();

                                $sw = FALSE;
                                $c  = 1;

                                foreach($consulta1 as $index1 => $row1)
                                {
                                    $sheet->row($c+1, [
                                        $row1["Caso"],
                                        $row1["departamento"],
                                        $row1["municipio"],
                                        $row1["etapa_caso"],
                                        ($row1["FechaDenuncia"] =="") ? "" : date("Y", strtotime($row1["FechaDenuncia"])),
                                        $row1["FechaDenuncia"],
                                        $row1["delito_principal"],
                                        $this->utilitarios(["tipo" => "100", "valor1" => $row1["id"]]),
                                        $this->utilitarios(["tipo" => "101", "valor1" => $row1["id"]])
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

                                $sheet->cells('A2:G' . ($c), function($cells){
                                    $cells->setAlignment('center');
                                });

                                $sheet->cells('H2:I' . ($c), function($cells){
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
                        $respuesta = '<span class="label label-primary font-sm">' . $this->dp_semaforo[$valor['valor']] . '</span>';
                        return($respuesta);
                        break;
                    case '2':
                        $respuesta = '<span class="label label-warning font-sm">' . $this->dp_semaforo[$valor['valor']] . '</span>';
                        return($respuesta);
                        break;
                    case '3':
                        $respuesta = '<span class="label label-danger font-sm">' . $this->dp_semaforo[$valor['valor']] . '</span>';
                        return($respuesta);
                        break;
                    default:
                        $respuesta = '<span class="label label-default font-sm">SIN ESTADO</span>';
                        return($respuesta);
                        break;
                }
                break;

            case '3':
                $respuesta = "";
                if($valor['valor1'] == 1)
                {
                    $respuesta = '<span class="label label-danger font-sm">' . $this->no_si[$valor['valor1']] . '</span>';
                }
                elseif($valor['valor1'] == 2)
                {
                    $consulta1 = RrhhPersona::where('n_documento', '=', $valor['valor2'])->first();
                    if(count($consulta1) > 0)
                    {
                        $respuesta = '<a href="' . asset($this->public_url) . '/' . $consulta1->certificacion_file_segip . '" target="_blank" class="btn btn-xs btn-success" title="Clic para ver la CERTIFICACION SEGIP" style="color: #FFFFFF;">
                            <strong>' . $this->no_si[$valor['valor1']] . '</strong>
                        </a>';
                    }
                }
                return $respuesta;
                break;
            case '51':
                $resultado = "";
                if(trim($valor['valor']) != "")
                {
                    $valor_array = explode("::", $valor['valor']);
                    $sw          = TRUE;
                    foreach($valor_array AS $valor1)
                    {
                        if($sw)
                        {
                            $resultado .= $valor1;
                            $sw         = FALSE;
                        }
                        else
                        {
                            $resultado .= "<br>" . $valor1;
                        }
                    }
                }
                return $resultado;
                break;

            case '100':
                $resultado = "";

                //=== CONSULTA VICTIMA ===
                    $tabla1 = "Persona";

                    $select = "
                        $tabla1.id,
                        UPPER($tabla1.Persona) AS Persona
                    ";

                    $array_where = "$tabla1.EsVictima = 1 AND $tabla1.Caso = " . $valor['valor1'];

                    $consulta1 = Persona::whereRaw($array_where)
                        ->select(DB::raw($select))
                        ->orderBy("$tabla1.Persona", "ASC")
                        ->get()
                        ->toArray();

                    $sw = TRUE;
                    if(count($consulta1) > 0)
                    {
                        foreach($consulta1 as $row1)
                        {
                            if($sw)
                            {
                                $resultado .= $row1["Persona"];
                                $sw        = FALSE;
                            }
                            else
                            {
                                $resultado .= " :: " . $row1["Persona"];
                            }
                        }
                    }

                return $resultado;
                break;
            case '101':
                $resultado = "";

                //=== CONSULTA VICTIMA ===
                    $tabla1 = "CasoFuncionario";
                    $tabla2 = "Funcionario";

                    $select = "
                        $tabla1.id,
                        UPPER(a2.Funcionario) AS Funcionario
                    ";

                    $array_where = "$tabla1.Caso = " . $valor['valor1'] . " AND $tabla1.FechaBaja IS NULL";

                    $consulta1 = CasoFuncionario::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.Funcionario")
                        ->whereRaw($array_where)
                        ->select(DB::raw($select))
                        ->orderBy("$tabla1.Funcionario", "ASC")
                        ->get()
                        ->toArray();

                    $sw = TRUE;
                    if(count($consulta1) > 0)
                    {
                        foreach($consulta1 as $row1)
                        {
                            if($sw)
                            {
                                $resultado .= $row1["Funcionario"];
                                $sw        = FALSE;
                            }
                            else
                            {
                                $resultado .= " :: " . $row1["Funcionario"];
                            }
                        }
                    }

                return $resultado;
                break;
            default:
                break;
        }
    }
}