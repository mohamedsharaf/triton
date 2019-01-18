<?php
namespace App\Libraries;

use App\Models\I4\Caso;
use App\Models\I4\Persona;
use App\Models\I4\Delito;

use DateTime;

class I4Class
{
    function __construct()
    {
    }

    public function getNumeroDetenidos()
    {
        set_time_limit(3600);
        ini_set('memory_limit','-1');

        // === INICIALIZACION DE VARIABLES ===
            $respuesta = array(
                'respuesta' => '',
                'sw'        => 0
            );

        // === CONSULTA ===
            $tabla1 = "Caso";
            $tabla2 = "Persona";

            $consulta1 = Caso::leftJoin("$tabla2 AS a2", "a2.Caso", "=", "$tabla1.id")
                ->whereRaw("$tabla1.EstadoCaso=1 AND a2.EstadoLibertad=4")
                ->select("$tabla1.id", "a2.id AS persona_id", "a2.dp_fecha_conclusion_detencion")
                ->orderBy("$tabla1.id")
                ->get()
                ->toArray();

        // === OPERACION ===
            if(count($consulta1) > 0)
            {
                $acumulador = "";
                $contador   = 1;
                $sw         = TRUE;
                foreach($consulta1 AS $row1)
                {
                    if($acumulador == $row1["id"])
                    {
                        $contador++;
                    }
                    else
                    {
                        if($sw)
                        {
                            $sw = FALSE;
                        }
                        else
                        {
                            $this->utilitarios(['tipo' => 1, 'valor' => $acumulador, 'n_detenidos' => $contador]);
                        }
                        $acumulador = $row1["id"];
                        $contador   = 1;
                    }

                    $iu = Persona::find($row1["persona_id"]);
                    if($row1["dp_fecha_conclusion_detencion"] == NULL)
                    {
                        $iu->dp_estado = 2;
                    }
                    else
                    {
                        $iu->dp_estado = 3;
                    }
                    $iu->save();
                }

                $this->utilitarios(['tipo' => 1, 'valor' => $acumulador, 'n_detenidos' => $contador]);

                $respuesta['respuesta'] .= 'Se logró modificar los número de detenidos.';
                $respuesta['sw']         = 1;
            }
            else
            {
                $respuesta['respuesta'] .= 'No se logró modificar el campo número de detenidos.';
            }

        return $respuesta;
    }

    public function getPersonaMayor65($data)
    {
        set_time_limit(3600);
        ini_set('memory_limit','-1');

        $respuesta = [
            "edad_sw" => FALSE,
            "edad"    => 0
        ];

        $fecha_nacimiento = new DateTime($data["FechaNac"]);
        $fecha_hoy        = new DateTime();
        $anios            = $fecha_hoy->diff($fecha_nacimiento);

        if($anios->y >= 65)
        {
            $respuesta["edad_sw"] = TRUE;
        }
        $respuesta["edad"] = $anios->y;

        return $respuesta;
    }

    public function getAnioTranscurrido($data)
    {
        set_time_limit(3600);
        ini_set('memory_limit','-1');

        $respuesta = [
            "anio"    => 0
        ];

        $fecha     = new DateTime($data["fecha"]);
        $fecha_hoy = new DateTime();
        $anios     = $fecha_hoy->diff($fecha);

        $respuesta["anio"] = $anios->y;

        return $respuesta;
    }

    public function getFechaTranscurrido($data)
    {
        set_time_limit(3600);
        ini_set('memory_limit','-1');

        $respuesta = [
            "f_transcurrido"    => 0
        ];

        $fecha          = new DateTime($data["fecha"]);
        $fecha_hoy      = new DateTime();
        $f_transcurrido = $fecha_hoy->diff($fecha);

        $respuesta["f_transcurrido"] = $f_transcurrido;

        return $respuesta;
    }

    private function utilitarios($valor)
    {
        switch($valor['tipo'])
        {
            case 1:
                $iu              = Caso::find($valor["valor"]);
                $iu->n_detenidos = $valor["n_detenidos"];
                $iu->save();
                break;
            default:
                break;
        }
    }

    public function getSemaforoDelitos()
    {
        set_time_limit(3600);
        ini_set('memory_limit','-1');

        // === INICIALIZACION DE VARIABLES ===
            $respuesta = array(
                'respuesta' => '',
                'sw'        => 0
            );

        // === CONSULTA ===
            $tabla1 = "Caso";
            $tabla2 = "Persona";
            $tabla3 = "Delito";

            $consulta1 = Caso::leftJoin("$tabla2 AS a2", "a2.Caso", "=", "$tabla1.id")
                ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.DelitoPrincipal")
                ->whereRaw("$tabla1.EstadoCaso=1 AND a2.EstadoLibertad=4 AND a3.PenaMaxima >= 10")
                ->select("$tabla1.id", "a2.id AS persona_id", "a3.PenaMaxima")
                ->orderBy("$tabla1.id")
                ->get();

            $consulta2 = Caso::leftJoin("$tabla2 AS a2", "a2.Caso", "=", "$tabla1.id")
                ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.DelitoPrincipal")
                ->whereRaw("$tabla1.EstadoCaso=1 AND a2.EstadoLibertad=4 AND a3.PenaMaxima >= 6 AND a3.PenaMaxima < 10")
                ->select("$tabla1.id", "a2.id AS persona_id", "a3.PenaMaxima")
                ->orderBy("$tabla1.id")
                ->get();

            // $consulta3 = Caso::leftJoin("$tabla2 AS a2", "a2.Caso", "=", "$tabla1.id")
            //     ->leftJoin("$tabla3 AS a3", "a3.id", "=", "$tabla1.DelitoPrincipal")
            //     ->whereRaw("$tabla1.EstadoCaso=1 AND a2.EstadoLibertad=4 AND a3.PenaMaxima < 6")
            //     ->select("$tabla1.id", "a2.id AS persona_id", "a3.PenaMaxima")
            //     ->orderBy("$tabla1.id")
            //     ->get();

        // === OPERACION ===
            if($consulta1->count() > 0)
            {
                foreach($consulta1->toArray() AS $row1)
                {
                    $iu                     = Persona::find($row1["persona_id"]);
                    $iu->dp_semaforo_delito = 3;
                    $iu->save();
                }
            }

            if($consulta2->count() > 0)
            {
                foreach($consulta2->toArray() AS $row2)
                {
                    $iu                     = Persona::find($row2["persona_id"]);
                    $iu->dp_semaforo_delito = 2;
                    $iu->save();
                }
            }

            $respuesta['respuesta'] .= 'Se logró modificar los semaforos de los delitos.';
            $respuesta['sw']         = 1;

        return $respuesta;
    }
}