<?php
namespace App\Libraries;

class UtilClass
{

  function __construct()
  {

  }

  public function getNoAcentoNoComilla($valor)
  {
      $tofind     = "ÀÁÂÄÅàáâäÒÓÔÖòóôöÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿ";
      $replac     = "AAAAAaaaaOOOOooooEEEEeeeeCcIIIIiiiiUUUUuuuuy";
      $sin_acento = utf8_encode(strtr(utf8_decode($valor), utf8_decode($tofind), $replac));
      $sin_acento = str_replace("\"", "", $sin_acento);
      $sin_acento = str_replace("'", "", $sin_acento);
      return str_replace("ñ", "Ñ", $sin_acento);
  }

  public function getNoEne($valor)
  {
      $tofind     = "ñÑ";
      $replac     = "nN";
      $sin_ene = utf8_encode(strtr(utf8_decode($valor), utf8_decode($tofind), $replac));
      return $sin_ene;
  }

  public function getNoAcentoNoComillaTextoLargo($valor)
  {
      $tofind     = "ÀÁÂÄÅàáâäÒÓÔÖòóôöÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿ";
      $replac     = "AAAAAaaaaOOOOooooEEEEeeeeCcIIIIiiiiUUUUuuuuy";
      $sin_acento = utf8_encode(strtr(utf8_decode($valor), utf8_decode($tofind), $replac));
      $sin_acento = str_replace("\"", "", $sin_acento);
      return str_replace("'", "", $sin_acento);
  }
}
