<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Models\Rrhh\RrhhBiometrico;
use App\Models\Rrhh\RrhhLogAlerta;
use App\Models\Rrhh\RrhhLogMarcacion;
use App\Models\Rrhh\RrhhPersonaBiometrico;

use App\Models\I4\Caso;

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
        // $schedule->call(function(){
        //     $i4 = new I4Class();

        //     $tabla1  = "Caso";
        //     $tabla2  = "Persona";

        //     $select = "
        //         $tabla1.id,

        //         a2.FechaNac,
        //         a2.dp_fecha_detencion_preventiva,
        //         a2.dp_fecha_conclusion_detencion,

        //         a2.dp_semaforo,
        //         a2.dp_etapa_gestacion_estado,
        //         a2.dp_enfermo_terminal_estado,
        //         a2.dp_persona_mayor_65,
        //         a2.dp_madre_lactante_1,
        //         a2.dp_custodia_menor_6,
        //         a2.dp_mayor_3,
        //         a2.dp_minimo_previsto_delito,
        //         a2.dp_pena_menor_4,
        //         a2.dp_delito_pena_menor_4,
        //         a2.dp_delito_patrimonial_menor_6,
        //         a2.dp_etapa_preparatoria_dias_transcurridos_estado
        //     ";

        //     $where = "$tabla1.EstadoCaso=1 AND a2.EstadoLibertad=4 AND a2.FechaNac IS NOT NULL";

        //     $consulta1 = Caso::leftJoin("$tabla2 AS a2", "a2.Caso", "=", "$tabla1.id")
        //                     ->whereRaw($where)
        //                     ->select(DB::raw($select))
        //                     ->get()
        //                     ->toArray();

        //     if(count($consulta1) > 0)
        //     {
        //         foreach ($consulta1 as $row1)
        //         {
        //             $persona_mayor_65 = $i4->getPersonaMayor65(["FechaNac" => $row1['FechaNac']]);

        //             $iu           = Persona::find($id);
        //         }
        //     }

        // })->daily();
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
