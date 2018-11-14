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
            '2' => 'CARCELETA'
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
        $this->rol_id   = Auth::user()->rol_id;
        $this->permisos = SegPermisoRol::join("seg_permisos", "seg_permisos.id", "=", "seg_permisos_roles.permiso_id")
            ->where("seg_permisos_roles.rol_id", "=", $this->rol_id)
            ->select("seg_permisos.codigo")
            ->get()
            ->toArray();

        if(in_array(['codigo' => '2001'], $this->permisos))
        {
            $data = [
                'rol_id'                 => $this->rol_id,
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
                                                ->select(DB::raw("id, UPPER(CONVERT(CAST(EtapaCaso AS BINARY) USING utf8)) AS nombre"))
                                                ->orderBy("EtapaCaso")
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

                $tabla1 = "Caso";
                $tabla2 = "Persona";
                $tabla3 = "Delito";
                $tabla4 = "EtapaCaso";
                $tabla5 = "CasoFuncionario";
                $tabla6 = "Funcionario";

                $select = "
                    $tabla1.id,
                    $tabla1.Caso,
                    $tabla1.CodCasoJuz,
                    $tabla1.FechaDenuncia,
                    $tabla1.EtapaCaso,
                    $tabla1.DelitoPrincipal,
                    $tabla1.triton_modificado,
                    $tabla1.n_detenidos,

                    a2.id AS persona_id,
                    UPPER(a2.Nombres) AS Nombres,
                    UPPER(a2.ApPat) AS ApPat,
                    UPPER(a2.ApMat) AS ApMat,
                    UPPER(a2.ApEsp) AS ApEsp,
                    a2.NumDocId,
                    a2.FechaNac,
                    a2.Sexo,

                    a2.triton_modificado,
                    a2.recinto_carcelario_id,
                    a2.dp_estado,
                    a2.dp_semaforo,
                    a2.dp_fecha_detencion_preventiva,
                    a2.dp_fecha_conclusion_detencion,
                    a2.dp_etapa_gestacion_estado,
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

                    UPPER(GROUP_CONCAT(a6.Funcionario)) AS funcionario
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
                    a2.dp_fecha_detencion_preventiva,
                    a2.dp_fecha_conclusion_detencion,
                    a2.dp_etapa_gestacion_estado,
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

                    a4.EtapaCaso
                ";

                $array_where = '';

                $user_id = Auth::user()->id;

                $array_where .= "$tabla1.EstadoCaso=1 AND a2.EstadoLibertad=4 AND a5.FechaBaja IS NULL";
                $array_where .= $jqgrid->getWhere();

                // $count = Caso::leftJoin("$tabla2 AS a2", "a2.Caso", "=", "$tabla1.id")
                //             ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.DelitoPrincipal")
                //             ->leftJoin("$tabla4 AS a4", "a4.id", "=", "$tabla1.EtapaCaso")
                //             ->leftJoin("$tabla5 AS a5", "a5.Caso", "=", "$tabla1.id")
                //             ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.Funcionario")
                //             ->whereRaw($array_where)
                //             ->count();

                $query1 = Caso::leftJoin("$tabla2 AS a2", "a2.Caso", "=", "$tabla1.id")
                            ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.DelitoPrincipal")
                            ->leftJoin("$tabla4 AS a4", "a4.id", "=", "$tabla1.EtapaCaso")
                            ->leftJoin("$tabla5 AS a5", "a5.Caso", "=", "$tabla1.id")
                            ->leftJoin("$tabla6 AS a6", "a6.id", "=", "a5.Funcionario")
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
                        'persona_id' => $row["persona_id"]
                    );

                    $respuesta['rows'][$i]['id'] = $row["id"];
                    $respuesta['rows'][$i]['cell'] = array(
                        '',
                        $this->utilitarios(array('tipo' => '1', 'valor' => $row["dp_semaforo"])),
                        $row["n_detenidos"],
                        $this->dp_estado[$row["dp_estado"]],
                        utf8_encode($row["Caso"]),
                        utf8_encode($row["CodCasoJuz"]),

                        utf8_encode($row["NumDocId"]),
                        utf8_encode($row["ApPat"]),
                        utf8_encode($row["ApMat"]),
                        utf8_encode($row["ApEsp"]),
                        utf8_encode($row["Nombres"]),
                        $row["FechaNac"],
                        ($row["Sexo"] =="") ? "" : $this->sexo[$row["Sexo"]],

                        $row["FechaDenuncia"],
                        utf8_encode($row["delito_principal"]),
                        "",

                        $row["dp_fecha_detencion_preventiva"],
                        $row["dp_fecha_conclusion_detencion"],
                        utf8_encode($row["etapa_caso"]),
                        "",

                        "",
                        utf8_encode($row["funcionario"]),
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
            default:
                break;
        }
    }
}