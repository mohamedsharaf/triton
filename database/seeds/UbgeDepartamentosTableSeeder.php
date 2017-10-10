<?php

use Illuminate\Database\Seeder;

class UbgeDepartamentosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ubge_departamentos')->insert([
            [
                "codigo"   => "02",
                "codigo_2" => "LP",
                "nombre"   => "LA PAZ"
            ],
            [
                "codigo"   => "04",
                "codigo_2" => "OR",
                "nombre"   => "ORURO"
            ],
            [
                "codigo"   => "05",
                "codigo_2" => "PT",
                "nombre"   => "POTOSI"
            ],
            [
                "codigo"   => "03",
                "codigo_2" => "CO",
                "nombre"   => "COCHABAMBA"
            ],
            [
                "codigo"   => "01",
                "codigo_2" => "CH",
                "nombre"   => "CHUQUISACA"
            ],
            [
                "codigo"   => "06",
                "codigo_2" => "TJ",
                "nombre"   => "TARIJA"
            ],
            [
                "codigo"   => "09",
                "codigo_2" => "PD",
                "nombre"   => "PANDO"
            ],
            [
                "codigo"   => "08",
                "codigo_2" => "BE",
                "nombre"   => "BENI"
            ],
            [
                "codigo"   => "07",
                "codigo_2" => "SC",
                "nombre"   => "SANTA CRUZ"
            ]
        ]);
    }
}
