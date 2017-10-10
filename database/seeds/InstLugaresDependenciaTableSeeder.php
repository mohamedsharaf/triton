<?php

use Illuminate\Database\Seeder;

class InstLugaresDependenciaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('inst_lugares_dependencia')->insert([
            [
                "nombre" => "FISCALIA GENERAL DEL ESTADO"
            ],
            [
                "nombre" => "INSTITUTO DE INVESTIGACION FORENSE DE CHUQUISACA"
            ],
            [
                "nombre" => "INSTITUTO DE INVESTIGACION FORENSE DE LA PAZ"
            ],
            [
                "nombre" => "FISCALIA DEPARTAMENTAL DE CHUQUISACA"
            ],
            [
                "nombre" => "FISCALIA DEPARTAMENTAL DE COCHABAMBA"
            ],
            [
                "nombre" => "FISCALIA DEPARTAMENTAL DE LA PAZ"
            ],
            [
                "nombre" => "FISCALIA DEPARTAMENTAL DE ORURO"
            ],
            [
                "nombre" => "FISCALIA DEPARTAMENTAL DE POTOSI"
            ],
            [
                "nombre" => "FISCALIA DEPARTAMENTAL DE PANDO"
            ],
            [
                "nombre" => "FISCALIA DEPARTAMENTAL DE SANTA CRUZ"
            ],
            [
                "nombre" => "FISCALIA DEPARTAMENTAL DE TARIJA"
            ],
            [
                "nombre" => "FISCALIA DEPARTAMENTAL DE BENI"
            ],
            [
                "nombre" => "INSTITUTO DE INVESTIGACION FORENSE DE COCHABAMBA"
            ],
            [
                "nombre" => "INSTITUTO DE INVESTIGACION FORENSE DE SANTA CRUZ"
            ]
        ]);
    }
}
