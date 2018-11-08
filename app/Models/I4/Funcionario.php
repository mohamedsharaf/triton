<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'Funcionario';

    protected $fillable = [
        "version",
        "Funcionario",
        "CodEmp",
        "Cargo",
        "FechaAlta",
        "FechaBaja",
        "EsNotificador",
        "EMailTrabajo",
        "CelularTrabajo",
        "TelefonoTrabajo",
        "InternoTrabajo",
        "Institucion",
        "Division",
        "Nombres",
        "ApPat",
        "ApMat",
        "ApEsp",
        "NumDocId",
        "DepDocId",
        "NivelReserva",
        "UserId",
        "PWD",
        "MFASecretKey",
        "CuentaActiva",
        "LabelSet",
        "ClaveAccesoSEGIP",
        "Foto",
        "CreatorUser",
        "CreatorFullName",
        "CreationDate",
        "CreationIP",
        "UpdaterUser",
        "UpdaterFullName",
        "UpdaterDate",
        "UpdaterIP",
        "FechaNac",
        "Profesion",
        "Dir",
        "Zona",
        "Tel",
        "Celular",
        "EMailPrivado",
        "LinkedIn",
        "Facebook",
        "GooglePlus",
        "Twitter",
        "Skype",
        "TipoDocId",
        "Pais",
        "Sexo",
        "EstadoCivil",
        "Rol",
        "Module",
        "NetAccessProfile",
        "MuniNac",
        "MuniDom",
        "NivelEducacion"
    ];

    protected $guarded  = [];
}