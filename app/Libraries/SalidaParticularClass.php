<?php
namespace App\Libraries;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Seguridad\SegPermisoRol;
use App\Models\Seguridad\SegLdUser;

use App\Models\Institucion\InstLugarDependencia;
use App\Models\Institucion\InstUnidadDesconcentrada;
use App\Models\Institucion\InstAuo;
use App\Models\Institucion\InstTipoCargo;
use App\Models\Institucion\InstCargo;

use App\Models\Rrhh\RrhhPersona;
use App\Models\Rrhh\RrhhFuncionario;
use App\Models\Rrhh\RrhhFthc;
use App\Models\Rrhh\RrhhHorario;
use App\Models\Rrhh\RrhhTipoSalida;
use App\Models\Rrhh\RrhhSalida;
use App\Models\Rrhh\RrhhAsistencia;
use App\Models\Rrhh\RrhhLogMarcacion;

class SalidaParticularClass
{
    function __construct()
    {

    }

    public function getSincronizar($data1)
    {
        // === INICIALIZACION DE VARIABLES ===
            $respuesta = array(
                'sw'         => 0,
                'titulo'     => '<div class="text-center"><strong>Sincronizar Salida Particular</strong></div>',
                'respuesta'  => '',
                'tipo'       => $data1['tipo'],
                'error_sw'   => 1
            );

        // === ANALISIS ===
            if( ! ($data1['fecha_del'] <= $data1['fecha_al']))
            {
                $respuesta['respuesta'] .= "La FECHA DEL es mayor que la FECHA AL.";
                return $respuesta;
            }

            $tabla1 = "rrhh_salidas";
            $tabla2 = "inst_unidades_desconcentradas";
            $tabla3 = "rrhh_tipos_salida";

            $select = "
                $tabla1.id,
                $tabla1.persona_id,
                $tabla1.tipo_salida_id,
                $tabla1.persona_id_superior,
                $tabla1.persona_id_rrhh,

                $tabla1.cargo_id,
                $tabla1.unidad_desconcentrada_id,

                $tabla1.estado,
                $tabla1.codigo,
                $tabla1.destino,
                $tabla1.motivo,
                $tabla1.f_salida,
                $tabla1.f_retorno,
                $tabla1.h_salida,
                $tabla1.h_retorno,

                $tabla1.n_horas,
                $tabla1.con_sin_retorno,

                $tabla1.validar_superior,
                $tabla1.f_validar_superior,

                $tabla1.validar_rrhh,
                $tabla1.f_validar_rrhh,

                $tabla1.pdf,
                $tabla1.papeleta_pdf,

                $tabla1.log_marcaciones_id_s,
                $tabla1.log_marcaciones_id_r,

                $tabla1.salida_s,
                $tabla1.salida_r,
                $tabla1.min_retrasos,

                a2.lugar_dependencia_id AS lugar_dependencia_id_funcionario,
                a2.nombre AS ud_funcionario,

                a3.nombre AS papeleta_salida,
                a3.tipo_cronograma,
                a3.tipo_salida
            ";

            $array_where = "a3.tipo_cronograma=1 AND a3.tipo_salida=2 AND $tabla1.validar_superior=2 AND $tabla1.validar_rrhh=2 AND $tabla1.f_salida <= '" . $data1['fecha_al'] . "' AND $tabla1.f_salida >= '"  . $data1['fecha_del'] . "'";

            if($data1['lugar_dependencia_id_funcionario'] != '')
            {
                $array_where .= " AND a2.lugar_dependencia_id=" . $data1['lugar_dependencia_id_funcionario'];
            }

            if($data1['persona_id'] != '')
            {
                $array_where .= " AND $tabla1.persona_id=" . $data1['persona_id'];
            }

            $consulta1 = RrhhSalida::leftJoin("$tabla2 AS a2", "a2.id", "=", "$tabla1.unidad_desconcentrada_id")
                ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.tipo_salida_id")
                ->whereRaw($array_where)
                ->select(DB::raw($select))
                ->get()
                ->toArray();

            $c_sincronizar = 0;

            if(count($consulta1) > 0)
            {
                set_time_limit(3600);
                ini_set('memory_limit','-1');

                foreach ($consulta1 as $row1)
                {
                    $fh_salida  = $row1['f_salida'] . " " . $row1['h_salida'];
                    $fh_retorno = $row1['f_salida'] . " " . $row1['h_retorno'];

                    $tabla1 = "rrhh_asistencias";

                    $select = "
                        $tabla1.id,
                        $tabla1.persona_id,

                        $tabla1.horario_id_1,
                        $tabla1.horario_id_2
                    ";

                    $array_where = "$tabla1.fecha = '" . $row1['f_salida'] . "' AND $tabla1.persona_id = " . $row1['persona_id'];

                    $consulta2 = RrhhAsistencia::whereRaw($array_where)
                        ->select(DB::raw($select))
                        ->first();

                    if(count($consulta2) > 0)
                    {
                        $consulta3 = RrhhHorario::where("id", "=", $consulta2['horario_id_1'])
                            ->first();

                        $consulta4 = RrhhHorario::where("id", "=", $consulta2['horario_id_2'])
                            ->first();

                        $fh_ingreso_1 = '';
                        $fh_salida_1  = '';
                        if(count($consulta3) > 0)
                        {
                            $fh_ingreso_1 = $row1['f_salida'] . " " . $consulta3['h_ingreso'];
                            $fh_salida_1  = $row1['f_salida'] . " " . $consulta3['h_salida'];
                        }

                        $fh_ingreso_2 = '';
                        $fh_salida_2  = '';
                        if(count($consulta4) > 0)
                        {
                            $fh_ingreso_2 = $row1['f_salida'] . " " . $consulta4['h_ingreso'];
                            $fh_salida_2  = $row1['f_salida'] . " " . $consulta4['h_salida'];
                        }

                        // === SALIDA ===
                            $salida_sw_1 = TRUE;
                            $salida_sw_2 = FALSE; //Si se modifica los valores $log_marcaciones_id_s y $log_marcaciones_s
                            if($fh_salida == $fh_ingreso_1)
                            {
                                $salida_sw_1 = FALSE;
                            }

                            if(($fh_salida == $fh_ingreso_2) && $salida_sw_1)
                            {
                                $salida_sw_1 = FALSE;
                            }

                            $log_marcaciones_id_s = '';
                            $log_marcaciones_s    = '';

                            if($salida_sw_1)
                            {
                                $consulta3 = RrhhLogMarcacion::where("persona_id", "=", $row1['persona_id'])
                                    ->whereBetween('f_marcacion', [$fh_salida, $fh_retorno])
                                    ->select('id', 'f_marcacion')
                                    ->orderBy('f_marcacion', 'asc')
                                    ->first();

                                if(count($consulta3) > 0)
                                {
                                    $log_marcaciones_id_s = $consulta3['id'];
                                    // $log_marcaciones_s    = $consulta3['f_marcacion'];
                                    $log_marcaciones_s = date("H:i:s", strtotime($consulta3['f_marcacion']));
                                    $salida_sw_2 = TRUE;
                                }
                            }
                            else
                            {
                                $log_marcaciones_s = $data1['sp_estado']['2'];
                                $salida_sw_2 = TRUE;
                            }

                            $c_sincronizar++;

                        // === RETORNO ===
                            $retorno_sw_1 = TRUE;
                            $retorno_sw_2 = FALSE; //Si se modifica los valores $log_marcaciones_id_r y $log_marcaciones_r
                            if($fh_retorno == $fh_salida_1)
                            {
                                $retorno_sw_1 = FALSE;
                            }

                            if(($fh_retorno == $fh_salida_2) && $retorno_sw_1)
                            {
                                $retorno_sw_1 = FALSE;
                            }

                            $log_marcaciones_id_r = '';
                            $log_marcaciones_r    = '';
                            $min_retrasos         = 0;

                            if($retorno_sw_1)
                            {
                                $fh_retorno_21 = strtotime('+20 minute', strtotime($fh_retorno));
                                $fh_retorno_21 = strtotime('+59 second', $fh_retorno_21);
                                $fh_retorno_21 = date("Y-m-d H:i:s", $fh_retorno_21);

                                $consulta4 = RrhhLogMarcacion::where("persona_id", "=", $row1['persona_id'])
                                    ->whereBetween('f_marcacion', [$fh_salida, $fh_retorno_21])
                                    ->select('id', 'f_marcacion')
                                    ->orderBy('f_marcacion', 'asc')
                                    ->get()
                                    ->toArray();

                                if(count($consulta4) > 0)
                                {
                                    foreach($consulta4 as $row4)
                                    {
                                        if($log_marcaciones_id_s != $row4['id'])
                                        {
                                            $log_marcaciones_id_r = $row4['id'];
                                            // $log_marcaciones_r    = $row4['f_marcacion'];
                                            $log_marcaciones_r = date("H:i:s", strtotime($row4['f_marcacion']));

                                            if($fh_retorno < $row4['f_marcacion'])
                                            {
                                                $min_retrasos = floor((strtotime($row4['f_marcacion']) - strtotime($fh_retorno)) / 60);
                                            }

                                            $retorno_sw_2 = TRUE;
                                            break;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $log_marcaciones_r = $data1['sp_estado']['3'];
                                $retorno_sw_2 = TRUE;
                            }

                        if($salida_sw_2 || $retorno_sw_2)
                        {
                            // === MODIFICACION DE LA SALIDA ===
                                $iu = RrhhSalida::find($row1['id']);

                                if($salida_sw_2)
                                {
                                    $iu->salida_s = $log_marcaciones_s;
                                    if($log_marcaciones_id_s != '')
                                    {
                                        $iu->log_marcaciones_id_s = $log_marcaciones_id_s;
                                    }
                                }

                                if($retorno_sw_2)
                                {
                                    $iu->salida_r = $log_marcaciones_r;
                                    if($log_marcaciones_id_r != '')
                                    {
                                        $iu->log_marcaciones_id_r = $log_marcaciones_id_r;
                                    }
                                }

                                $iu->min_retrasos = $min_retrasos;

                                $iu->save();

                            // === MODIFICACION A LOG DE MARCACIONES ===
                                if($log_marcaciones_id_s != '')
                                {
                                    $iu = RrhhLogMarcacion::find($log_marcaciones_id_s);

                                    $iu->estado = '2';

                                    $iu->save();
                                }

                                if($log_marcaciones_id_r != '')
                                {
                                    $iu = RrhhLogMarcacion::find($log_marcaciones_id_r);

                                    $iu->estado = '2';

                                    $iu->save();
                                }
                        }
                    }
                }

                $respuesta['respuesta'] .= "Se sincronizaron " . $c_sincronizar . ".";
                $respuesta['sw'] = 1;
            }
            else
            {
                $respuesta['respuesta'] .= "No existe SALIDAS PARTICULARES.";
            }

        return $respuesta;
    }
}