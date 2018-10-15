<?php
namespace App\Libraries;

use App\Models\Rrhh\RrhhLogMarcacion;
use App\Models\Rrhh\RrhhLogMarcacionBackup;
use App\Models\Rrhh\RrhhBiometrico;
use App\Models\Rrhh\RrhhLogAlerta;
use App\Models\Rrhh\RrhhPersonaBiometrico;

use TADPHP\TADFactory;
use TADPHP\TAD;

use Exception;

class BiometricoClass
{

    function __construct()
    {

    }

    public function getRegistroAsistencia($data1)
    {
        set_time_limit(3600);
        ini_set('memory_limit','-1');

        // === INICIALIZACION DE VARIABLES ===
            $respuesta = array(
                'respuesta' => '',
                'sw'        => 0
            );

            $fs_conexion = date("Y-m-d H:i:s");

        // === CONSULTA ===
            $biometrico = RrhhBiometrico::where('id', '=', $data1['id'])
                ->first()
                ->toArray();

        // === VERIFICANDO CONEXION ===
            if($biometrico['estado'] == '1')
            {
                $data_conexion = [
                    'ip'          => $biometrico['ip'],
                    'internal_id' => $biometrico['internal_id'],
                    'com_key'     => $biometrico['com_key'],
                    'soap_port'   => $biometrico['soap_port'],
                    'udp_port'    => $biometrico['udp_port'],
                    'encoding'    => $biometrico['encoding']
                ];

                $tad_factory = new TADFactory($data_conexion);
                $tad         = $tad_factory->get_instance();

                $f_log_asistencia_sw = TRUE;
                try
                {
                    $fb_conexion_array = $tad->get_date()->to_array();
                    $fb_conexion       = $fb_conexion_array['Row']['Date'] . ' ' . $fb_conexion_array['Row']['Time'];

                    $e_conexion = 1;

                    $att_logs = $tad->get_att_log();

                    $log_marcacion = $att_logs->filter_by_date([
                        'start' => $data1['fecha_del'],
                        'end'   => $data1['fecha_al']
                    ])->to_array();

                    if(count($log_marcacion))
                    {
                        $data2     = [];
                        $sw_insert = FALSE;
                        foreach($log_marcacion as $row)
                        {
                            if(isset($row['PIN']))
                            {
                                $consulta1 = RrhhPersonaBiometrico::where("biometrico_id", "=", $data1['id'])
                                    ->where("n_documento_biometrico", "=",  $row['PIN'])
                                    ->select('persona_id')
                                    ->first();

                                if(count($consulta1) > 0)
                                {
                                    $consulta2 = RrhhLogMarcacion::where("biometrico_id", "=", $data1['id'])
                                        ->where("persona_id", "=", $consulta1['persona_id'])
                                        ->where("f_marcacion", "=", $row['DateTime'])
                                        ->select('persona_id')
                                        ->first();

                                    if(count($consulta2) < 1)
                                    {
                                        $data2[] = [
                                            'biometrico_id'          => $data1['id'],
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
                                    $consulta1 = RrhhPersonaBiometrico::where("biometrico_id", "=", $data1['id'])
                                        ->where("n_documento_biometrico", "=",  $valor1['PIN'])
                                        ->select('persona_id')
                                        ->first();

                                    if(count($consulta1) > 0)
                                    {
                                        $consulta2 = RrhhLogMarcacion::where("biometrico_id", "=", $data1['id'])
                                            ->where("persona_id", "=", $consulta1['persona_id'])
                                            ->where("f_marcacion", "=", $valor1['DateTime'])
                                            ->select('persona_id')
                                            ->first();

                                        if(count($consulta2) < 1)
                                        {
                                            $data2[] = [
                                                'biometrico_id'          => $data1['id'],
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
                            RrhhLogMarcacion::insert($data2);
                            $respuesta['respuesta'] .= "Se obtuvo los registros de asistencia de la siguiente dirección " . $biometrico['ip'] . ".";
                            $respuesta['sw']        = 1;
                        }
                        else
                        {
                            $respuesta['respuesta'] .= "No existe registros de asistencia en la siguiente dirección " . $biometrico['ip'] . ".";
                            $f_log_asistencia_sw = FALSE;
                        }
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "No existe registros de asistencia en la siguiente dirección " . $biometrico['ip'] . ".";
                        $f_log_asistencia_sw = FALSE;
                    }
                }
                catch (Exception $e)
                {
                    $respuesta['respuesta'] .= "No se logró obtener los registros de asistencia de la siguiente dirección " . $biometrico['ip'] . "<br>Verifique la conexión.<br>";
                    $e_conexion             = 2;
                    $fb_conexion            = null;

                    $error       = '' . $e;
                    $error_array = explode("Stack trace:", $error);

                    $iu                = new RrhhLogAlerta;
                    $iu->biometrico_id = $data1['id'];
                    $iu->tipo_emisor   = 2;
                    $iu->tipo_alerta   = 1;
                    $iu->f_alerta      = $fs_conexion;
                    $iu->mensaje       = $error_array[0];
                    $iu->save();

                    $f_log_asistencia_sw = FALSE;
                }

                if($f_log_asistencia_sw)
                {
                    $iu                   = RrhhBiometrico::find($data1['id']);
                    $iu->e_conexion       = $e_conexion;
                    $iu->fs_conexion      = $fs_conexion;
                    $iu->fb_conexion      = $fb_conexion;
                    $iu->f_log_asistencia = $fs_conexion;
                    $iu->save();
                }
                else
                {
                    $iu              = RrhhBiometrico::find($data1['id']);
                    $iu->e_conexion  = $e_conexion;
                    $iu->fs_conexion = $fs_conexion;
                    $iu->fb_conexion = $fb_conexion;
                    $iu->save();
                }
            }

        return $respuesta;
    }

    public function getBackupLog($data1)
    {
        set_time_limit(3600);
        ini_set('memory_limit','-1');

        // === INICIALIZACION DE VARIABLES ===
            $respuesta = array(
                'respuesta' => '',
                'sw'        => 0
            );

            $fs_conexion = date("Y-m-d H:i:s");

        // === CONSULTA ===
            $biometrico = RrhhBiometrico::where('id', '=', $data1['id'])
                ->first()
                ->toArray();

        // === VERIFICANDO CONEXION ===
            if($biometrico['estado'] == '1')
            {
                $data_conexion = [
                    'ip'          => $biometrico['ip'],
                    'internal_id' => $biometrico['internal_id'],
                    'com_key'     => $biometrico['com_key'],
                    'soap_port'   => $biometrico['soap_port'],
                    'udp_port'    => $biometrico['udp_port'],
                    'encoding'    => $biometrico['encoding']
                ];

                $tad_factory = new TADFactory($data_conexion);
                $tad         = $tad_factory->get_instance();

                $f_log_asistencia_sw = TRUE;
                try
                {
                    $fb_conexion_array = $tad->get_date()->to_array();
                    $fb_conexion       = $fb_conexion_array['Row']['Date'] . ' ' . $fb_conexion_array['Row']['Time'];

                    $e_conexion = 1;

                    $log_marcacion = $tad->get_att_log()->to_array();

                    if(count($log_marcacion))
                    {
                        $data2  = [];
                        foreach($log_marcacion as $row)
                        {
                            if(isset($row['PIN']))
                            {
                                $iu                         = new RrhhLogMarcacionBackup;
                                $iu->biometrico_id          = $data1['id'];
                                $iu->tipo_marcacion         = 2;
                                $iu->n_documento_biometrico = $valor1['PIN'];
                                $iu->f_marcacion            = $valor1['DateTime'];
                                $iu->save();
                            }
                            else
                            {
                                foreach($row as $valor1)
                                {
                                    $iu                         = new RrhhLogMarcacionBackup;
                                    $iu->biometrico_id          = $data1['id'];
                                    $iu->tipo_marcacion         = 2;
                                    $iu->n_documento_biometrico = $valor1['PIN'];
                                    $iu->f_marcacion            = $valor1['DateTime'];
                                    $iu->save();
                                }
                            }
                        }

                        $tad->delete_data(['value' => 3]);

                        $respuesta['respuesta'] .= "Se obtuvo los registros de asistencia de la siguiente dirección " . $biometrico['ip'] . ".";
                        $respuesta['sw']        = 1;
                    }
                    else
                    {
                        $respuesta['respuesta'] .= "No existe registros de asistencia en la siguiente dirección " . $biometrico['ip'] . ".";
                        $f_log_asistencia_sw = FALSE;
                    }
                }
                catch (Exception $e)
                {
                    $respuesta['respuesta'] .= "No se logró obtener los registros de asistencia de la siguiente dirección " . $biometrico['ip'] . "<br>Verifique la conexión.<br>";
                    $e_conexion             = 2;
                    $fb_conexion            = null;

                    $error       = '' . $e;
                    $error_array = explode("Stack trace:", $error);

                    $iu                = new RrhhLogAlerta;
                    $iu->biometrico_id = $data1['id'];
                    $iu->tipo_emisor   = 2;
                    $iu->tipo_alerta   = 1;
                    $iu->f_alerta      = $fs_conexion;
                    $iu->mensaje       = $error_array[0];
                    $iu->save();

                    $f_log_asistencia_sw = FALSE;
                }

                if($f_log_asistencia_sw)
                {
                    $iu                   = RrhhBiometrico::find($data1['id']);
                    $iu->e_conexion       = $e_conexion;
                    $iu->fs_conexion      = $fs_conexion;
                    $iu->fb_conexion      = $fb_conexion;
                    $iu->f_log_asistencia = $fs_conexion;
                    $iu->save();
                }
                else
                {
                    $iu              = RrhhBiometrico::find($data1['id']);
                    $iu->e_conexion  = $e_conexion;
                    $iu->fs_conexion = $fs_conexion;
                    $iu->fb_conexion = $fb_conexion;
                    $iu->save();
                }
            }

        return $respuesta;
    }
}