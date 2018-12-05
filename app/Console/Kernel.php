<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

use App\Models\Rrhh\RrhhBiometrico;
use App\Models\Rrhh\RrhhLogAlerta;
use App\Models\Rrhh\RrhhLogMarcacion;
use App\Models\Rrhh\RrhhPersonaBiometrico;

use App\Models\I4\Caso;
use App\Models\I4\Delito;
use App\Models\I4\Actividad;

use TADPHP\TADFactory;
use TADPHP\TAD;

use App\Libraries\I4Class;

use Exception;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        set_time_limit(3600);
        ini_set('memory_limit','-1');

        //=== SINCRONIZAR HORA AUTOMATICAMENTE ===
            $schedule->call(function(){
                $consulta1 = RrhhBiometrico::where('estado', '=', 1)
                    ->get()
                    ->toArray();

                foreach ($consulta1 AS $row1)
                {
                    $fs_conexion = date("Y-m-d H:i:s");

                    $data_conexion = [
                        'ip'            => $row1['ip'],
                        'internal_id'   => $row1['internal_id'],
                        'com_key'       => $row1['com_key'],
                        'soap_port'     => $row1['soap_port'],
                        'udp_port'      => $row1['udp_port'],
                        'encoding'      => $row1['encoding']
                    ];

                    $tad_factory = new TADFactory($data_conexion);
                    $tad         = $tad_factory->get_instance();

                    try
                    {
                        $tad->set_date(['date' => date("Y-m-d", strtotime($fs_conexion)), 'time' => date("H:i:s", strtotime($fs_conexion))]);

                        $fb_conexion_array = $tad->get_date()->to_array();

                        $fb_conexion       = $fb_conexion_array['Row']['Date'] . ' ' . $fb_conexion_array['Row']['Time'];
                        $e_conexion        = 1;
                    }
                    catch (Exception $e)
                    {
                        $e_conexion             = 2;
                        $fb_conexion            = null;

                        $error = '' . $e;
                        $error_array = explode("Stack trace:", $error);

                        $iu                = new RrhhLogAlerta;
                        $iu->biometrico_id = $row1['id'];
                        $iu->tipo_emisor   = 1;
                        $iu->tipo_alerta   = 1;
                        $iu->f_alerta      = $fs_conexion;
                        $iu->mensaje       = $error_array[0];
                        $iu->save();
                    }

                    $iu              = RrhhBiometrico::find($row1['id']);
                    $iu->e_conexion  = $e_conexion;
                    $iu->fs_conexion = $fs_conexion;
                    $iu->fb_conexion = $fb_conexion;
                    $iu->save();
                }
            })->dailyAt('06:00');

        //=== OBTENER REGISTRO DE ASISTENCIA ===
            $schedule->call(function(){
                $f_actual   = date("Y-m-d");
                $f_ayer = date("Y-m-d", strtotime('-1 day'));

                $consulta1 = RrhhBiometrico::where('estado', '=', 1)
                    ->get()
                    ->toArray();

                foreach ($consulta1 AS $row1)
                {
                    $fs_conexion = date("Y-m-d H:i:s");

                    $data_conexion = [
                        'ip'            => $row1['ip'],
                        'internal_id'   => $row1['internal_id'],
                        'com_key'       => $row1['com_key'],
                        'soap_port'     => $row1['soap_port'],
                        'udp_port'      => $row1['udp_port'],
                        'encoding'      => $row1['encoding']
                    ];

                    $tad_factory = new TADFactory($data_conexion);
                    $tad         = $tad_factory->get_instance();

                    $f_log_asistencia_sw = TRUE;
                    try
                    {
                        $fb_conexion_array = $tad->get_date()->to_array();
                        $fb_conexion       = $fb_conexion_array['Row']['Date'] . ' ' . $fb_conexion_array['Row']['Time'];

                        $att_logs = $tad->get_att_log();

                        $log_marcacion = $att_logs->filter_by_date([
                            'start' => $f_ayer,
                            'end'   => $f_actual
                        ])->to_array();

                        if(count($log_marcacion))
                        {
                            $data1     = [];
                            $sw_insert = FALSE;

                            foreach($log_marcacion as $row)
                            {
                                if(isset($row['PIN']))
                                {
                                    $consulta1 = RrhhPersonaBiometrico::where("biometrico_id", "=", $row1['id'])
                                        ->where("n_documento_biometrico", "=",  $row['PIN'])
                                        ->select('persona_id')
                                        ->first();

                                    if(count($consulta1) > 0)
                                    {
                                        $consulta2 = RrhhLogMarcacion::where("biometrico_id", "=", $row1['id'])
                                            ->where("persona_id", "=", $consulta1['persona_id'])
                                            ->where("f_marcacion", "=", $row['DateTime'])
                                            ->select('persona_id')
                                            ->first();

                                        if(count($consulta2) < 1)
                                        {
                                            $data1[] = [
                                                'biometrico_id'          => $row1['id'],
                                                'persona_id'             => $consulta1['persona_id'],
                                                'tipo_marcacion'         => 2,
                                                'n_documento_biometrico' => $row['PIN'],
                                                'f_marcacion'            => $row['DateTime']
                                            ];

                                            $sw_insert = TRUE;
                                        }
                                    }
                                }
                                else
                                {
                                    foreach($row as $valor1)
                                    {
                                        $consulta1 = RrhhPersonaBiometrico::where("biometrico_id", "=", $row1['id'])
                                            ->where("n_documento_biometrico", "=",  $valor1['PIN'])
                                            ->select('persona_id')
                                            ->first();

                                        if(count($consulta1) > 0)
                                        {
                                            $consulta2 = RrhhLogMarcacion::where("biometrico_id", "=", $row1['id'])
                                                ->where("persona_id", "=", $consulta1['persona_id'])
                                                ->where("f_marcacion", "=", $valor1['DateTime'])
                                                ->select('persona_id')
                                                ->first();

                                            if(count($consulta2) < 1)
                                            {
                                                $data1[] = [
                                                    'biometrico_id'          => $row1['id'],
                                                    'persona_id'             => $consulta1['persona_id'],
                                                    'tipo_marcacion'         => 2,
                                                    'n_documento_biometrico' => $valor1['PIN'],
                                                    'f_marcacion'            => $valor1['DateTime']
                                                ];

                                                $sw_insert = TRUE;
                                            }
                                        }
                                    }
                                }
                            }

                            if($sw_insert)
                            {
                                RrhhLogMarcacion::insert($data1);
                            }
                            else
                            {
                                $f_log_asistencia_sw = FALSE;
                            }

                            // foreach($log_marcacion as $row)
                            // {
                            //     if(isset($row['PIN']))
                            //     {
                            //         $consulta2 = RrhhPersonaBiometrico::where("biometrico_id", "=", $row1['id'])
                            //             ->where("n_documento_biometrico", "=",  $row['PIN'])
                            //             ->select('persona_id')
                            //             ->first();

                            //         if(count($consulta2) > 0)
                            //         {
                            //             $data1[] = [
                            //                 'biometrico_id'          => $row1['id'],
                            //                 'persona_id'             => $consulta2['persona_id'],
                            //                 'tipo_marcacion'         => 1,
                            //                 'n_documento_biometrico' => $row['PIN'],
                            //                 'f_marcacion'            => $row['DateTime']
                            //             ];
                            //         }
                            //     }
                            //     else
                            //     {
                            //         foreach($row as $valor1)
                            //         {
                            //             $consulta2 = RrhhPersonaBiometrico::where("biometrico_id", "=", $row1['id'])
                            //                 ->where("n_documento_biometrico", "=",  $valor1['PIN'])
                            //                 ->select('persona_id')
                            //                 ->first();

                            //             if(count($consulta2) > 0)
                            //             {
                            //                 $data1[] = [
                            //                     'biometrico_id'          => $row1['id'],
                            //                     'persona_id'             => $consulta2['persona_id'],
                            //                     'tipo_marcacion'         => 2,
                            //                     'n_documento_biometrico' => $valor1['PIN'],
                            //                     'f_marcacion'            => $valor1['DateTime']
                            //                 ];
                            //             }
                            //         }
                            //     }
                            // }

                            // RrhhLogMarcacion::insert($data1);

                            // $tad->delete_data(['value' => 3]);
                        }
                        else
                        {
                            $f_log_asistencia_sw = FALSE;
                        }

                        $e_conexion        = 1;
                    }
                    catch (Exception $e)
                    {
                        $e_conexion             = 2;
                        $fb_conexion            = null;

                        $error       = '' . $e;
                        $error_array = explode("Stack trace:", $error);

                        $iu                = new RrhhLogAlerta;
                        $iu->biometrico_id = $row1['id'];
                        $iu->tipo_emisor   = 1;
                        $iu->tipo_alerta   = 1;
                        $iu->f_alerta      = $fs_conexion;
                        $iu->mensaje       = $error_array[0];
                        $iu->save();

                        $f_log_asistencia_sw = FALSE;
                    }

                    if($f_log_asistencia_sw)
                    {
                        $iu                   = RrhhBiometrico::find($row1['id']);
                        $iu->e_conexion       = $e_conexion;
                        $iu->fs_conexion      = $fs_conexion;
                        $iu->fb_conexion      = $fb_conexion;
                        $iu->f_log_asistencia = $fs_conexion;
                        $iu->save();
                    }
                    else
                    {
                        $iu              = RrhhBiometrico::find($row1['id']);
                        $iu->e_conexion  = $e_conexion;
                        $iu->fs_conexion = $fs_conexion;
                        $iu->fb_conexion = $fb_conexion;
                        $iu->save();
                    }
                }
            })->hourlyAt(17);

        //=== NUMERO DE DETENIDOS ===
            $schedule->call(function(){
                $i4 = new I4Class();
                $i4->getNumeroDetenidos();
            })->hourly();

        //=== OPERACION DIARIA DEL DETENIDO PREVENTIVO ===
            $schedule->call(function(){
                $i4 = new I4Class();

                $tabla1  = "Caso";
                $tabla2  = "Persona";

                $select = "
                    $tabla1.id,
                    $tabla1.DelitoPrincipal,
                    $tabla1.EtapaCaso,

                    a2.id AS persona_id,
                    a2.FechaNac,
                    a2.dp_fecha_detencion_preventiva,
                    a2.dp_fecha_conclusion_detencion,

                    a2.dp_semaforo,
                    a2.dp_etapa_gestacion_estado,
                    a2.dp_enfermo_terminal_estado,
                    a2.dp_persona_mayor_65,
                    a2.dp_madre_lactante_1,
                    a2.dp_custodia_menor_6,
                    a2.dp_mayor_3,
                    a2.dp_minimo_previsto_delito,
                    a2.dp_pena_menor_4,
                    a2.dp_delito_pena_menor_4,
                    a2.dp_delito_patrimonial_menor_6,
                    a2.dp_etapa_preparatoria_dias_transcurridos_estado
                ";

                $where = "$tabla1.EstadoCaso=1 AND a2.EstadoLibertad=4";

                $consulta1 = Caso::leftJoin("$tabla2 AS a2", "a2.Caso", "=", "$tabla1.id")
                                ->whereRaw($where)
                                ->select(DB::raw($select))
                                ->get()
                                ->toArray();

                if(count($consulta1) > 0)
                {
                    foreach ($consulta1 as $row1)
                    {
                        $consulta2 = Delito::where('id', $data1['delito_principal_id'])
                                        ->select("Delito", "PenaMinima", "PenaMaxima", "ClaseDelito")
                                        ->first();

                        $dp_semaforo = 1;

                        $iu = Persona::find($row1['persona_id']);

                        // === AMARILLO ===
                            if($row1['dp_etapa_gestacion_estado'] == 2)
                            {
                                $dp_semaforo = 2;
                            }

                            if($row1['dp_enfermo_terminal_estado'] == 2)
                            {
                                $dp_semaforo = 2;
                            }

                            if($row1['dp_madre_lactante_1'] == 2)
                            {
                                $dp_semaforo = 2;
                            }

                            if($row1['dp_custodia_menor_6'] == 2)
                            {
                                $dp_semaforo = 2;
                            }

                            $iu->dp_persona_mayor_65 = 1;
                            $iu->Edad                = NULL;
                            if($row1['FechaNac'] != "")
                            {
                                $persona_mayor_65 = $i4->getPersonaMayor65(["FechaNac" => $row1['FechaNac']]);
                                if($persona_mayor_65["edad_sw"])
                                {
                                    $iu->dp_persona_mayor_65 = 2;
                                    $dp_semaforo             = 2;
                                }
                                $iu->Edad = $persona_mayor_65["edad"];
                            }

                            // === DELITOS CON PENAS HASTA 4 AÑOS ===
                                $iu->dp_delito_pena_menor_4 = 1;
                                if(count($consulta2) > 0)
                                {
                                    if($consulta2->PenaMaxima != NULL)
                                    {
                                        if($consulta2->PenaMaxima <= 4)
                                        {
                                            $iu->dp_delito_pena_menor_4 = 2;
                                            $dp_semaforo                = 2;
                                        }
                                    }
                                }

                            // === DELITOS DE CONTENIDO PATRIMONIAL CON PENA HASTA 6 AÑOS ===
                                $iu->dp_delito_patrimonial_menor_6 = 1;
                                if(count($consulta2) > 0)
                                {
                                    if($consulta2->PenaMaxima != NULL)
                                    {
                                        if(($consulta2->ClaseDelito == 7) || ($consulta2->ClaseDelito == 9))
                                        {
                                            if($consulta2->PenaMaxima <= 6)
                                            {
                                                $iu->dp_delito_patrimonial_menor_6 = 2;
                                                $dp_semaforo                       = 2;
                                            }
                                        }
                                    }
                                }

                            // === DETENCIONES PREVENTIVAS EN ETAPA PREPARATORIA 5 MESES Y 6 MESES ===
                                $iu->dp_etapa_preparatoria_dias_transcurridos_estado = 1;
                                $iu->dp_etapa_preparatoria_dias_transcurridos_numero = NULL;
                                if($row1["EtapaCaso"] == 2)
                                {
                                    $consulta3 = Actividad::where('Caso', $row1['id'])
                                                    ->where('ActividadActualizaEstadoCaso', 1)
                                                    ->where('TipoActividad', 26)
                                                    ->select("id", "Fecha")
                                                    ->first();

                                    if(count($consulta3) > 0)
                                    {
                                        $f_transcurrido      = $i4->getFechaTranscurrido(["fecha" => $consulta3->Fecha]);
                                        $meses_transcurridos = ($f_transcurrido["f_transcurrido"]->y * 12) + $f_transcurrido["f_transcurrido"]->m;

                                        $iu->dp_etapa_preparatoria_dias_transcurridos_numero = $meses_transcurridos;
                                        if(($meses_transcurridos >=5) && ($meses_transcurridos < 6))
                                        {
                                            $iu->dp_etapa_preparatoria_dias_transcurridos_estado = 2;
                                            $dp_semaforo                                         = 2;
                                        }
                                        elseif($meses_transcurridos >= 6)
                                        {
                                            $iu->dp_etapa_preparatoria_dias_transcurridos_estado = 2;
                                            $dp_semaforo                                         = 3;
                                        }
                                    }
                                }

                        // === ROJO ===
                            $iu->dp_mayor_3                = 1;
                            $iu->dp_minimo_previsto_delito = 1;
                            if($row1['dp_fecha_detencion_preventiva'] != "")
                            {
                                $anios_transcurridos = $i4->getAnioTranscurrido(["fecha" => $row1['dp_fecha_detencion_preventiva']]);

                                // === FECHA DE DETENCION ===
                                    if($anios_transcurridos["anio"] >= 3)
                                    {
                                        $iu->dp_mayor_3 = 2;
                                        $dp_semaforo    = 3;
                                    }

                                // === LOS QUE PASARON EL MINIMO DE LA PENA PREVISTA ===
                                    if(count($consulta2) > 0)
                                    {
                                        if($consulta2->PenaMinima != NULL)
                                        {
                                            if($consulta2->PenaMinima <= $anios_transcurridos["anio"])
                                            {
                                                $iu->dp_minimo_previsto_delito = 2;
                                                $dp_semaforo                   = 3;
                                            }
                                        }
                                    }
                            }

                        $iu->dp_semaforo = $dp_semaforo;

                        $iu->save();
                    }
                }

            })->dailyAt('08:15');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
