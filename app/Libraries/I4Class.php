<?php
namespace App\Libraries;

use App\Models\I4\Caso;
use App\Models\I4\Persona;

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
}