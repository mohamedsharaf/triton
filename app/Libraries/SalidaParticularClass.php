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

    public function getSincronizar($data1, $tipo='1')
    {
        // === INICIALIZACION DE VARIABLES ===
            $respuesta = array(
                'sw'         => 0,
                'titulo'     => '<div class="text-center"><strong>Sincronizar Salida Particular</strong></div>',
                'respuesta'  => '',
                'tipo'       => $tipo,
                'iu'         => 1,
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

            if(count($consulta1) > 0)
            {
                $sw_cerrado = TRUE;
                set_time_limit(3600);
                ini_set('memory_limit','-1');
            }
            else
            {
                $respuesta['respuesta'] .= "No existe SALIDAS PARTICULARES.";
            }

        return $respuesta;
    }
}