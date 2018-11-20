<?php

namespace App\Http\Controllers\I4;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;
use App\Libraries\JqgridMysqlClass;
use App\Libraries\UtilClass;

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
use App\Models\I4\PersonaRecintoCarcelario;
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

                        'oficina_id'      => $row["oficina_id"],
                        'municipio_id'    => $row["municipio_id"],
                        'departamento_id' => $row["departamento_id"],

                        'peligro_procesal_id' => $row["peligro_procesal_id"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["persona_id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',
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
            default:
                break;
        }
    }
}