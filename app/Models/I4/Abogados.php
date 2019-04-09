<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class Abogado extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'Abogado';

    protected $fillable = [
        "version",
        "Nombres",
        "ApPat",
        "ApMat",
        "ApEsp",
        "Abogado",
        "NumDocId",
        "DepDocId",
        "NotifDomPersonal",
        "NotifDomTrabajo",
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
        "DirDom",
        "ZonaDom",
        "TelDom",
        "CelularDom",
        "EMailPrivado",
        "EmpresaTrab",
        "Cargo",
        "DirTrab",
        "ZonaTrab",
        "TelTrab",
        "CelularTrab",
        "EMailTrab",
        "JefeDirecto",
        "CargoJefeDirecto",
        "TelJefeDirecto",
        "CelularJefeDirecto",
        "EMailJefeDirecto",
        "TipoDocId",
        "Nacionalidad",
        "Sexo",
        "EstadoCivil",
        "MuniNac",
        "MuniDom",
        "MuniTrab",
        "PWD",
        "MFASecretKey",
        "CuentaActiva",
        "CodigoElectricidadDom",
        "CodigoElectricidadTrab"
    ];

    protected $guarded  = [];
}