<?php
namespace App\Libraries;

use nusoap_client;

class SegipClass
{
    function __construct()
    {
    }

    public function getCertificacionSegip($data)
    {
        // === INICIALIZACION DE VARIABLES ===
            $respuesta = array(
                'respuesta' => '',
                'data'      => '',
                'sw'        => 0
            );

        // === OPERACION ===
            $cliente = new nusoap_client(env('SEGIP_RUTA'), true);

            $error = $cliente->getError();
            if($error)
            {
                $respuesta['respuesta'] .= $error;
                return $respuesta;
            }

            $parametros = array(
                'pCodigoInstitucion'       => env('SEGIP_CODIGO_INSTITUCION'),
                'pUsuario'                 => env('SEGIP_USUARIO'),
                'pContrasenia'             => env('SEGIP_CONTRASENIA'),
                'pClaveAccesoUsuarioFinal' => env('SEGIP_CLAVE_ACCESO_USUARIO_FINAL'),
                'pNumeroAutorizacion'      => '',
                'pNumeroDocumento'         => $data['n_documento'],
                'pComplemento'             => $data['complemento'],
                'pNombre'                  => $data['nombre'],
                'pPrimerApellido'          => $data['ap_paterno'],
                'pSegundoApellido'         => $data['ap_materno'],
                'pFechaNacimiento'         => date("d/m/Y", strtotime($data['f_nacimiento']))
            );

            $cliente->soap_defencoding = 'UTF-8';
            $cliente->decode_utf8      = FALSE;

            $respuesta_soap = $cliente->call('ConsultaDatoPersonaCertificacion', $parametros);

            $error1 = $cliente->getError();
            if($error1)
            {
                $respuesta['respuesta'] .= $error1;
                return $respuesta;
            }
            else
            {
                $respuesta['respuesta'] = $respuesta_soap;
                $respuesta['sw']        = 1;
            }

        return $respuesta;
    }

    private function utilitarios($valor)
    {
        switch($valor['tipo'])
        {
            case 1:
                break;
            default:
                break;
        }
    }
}