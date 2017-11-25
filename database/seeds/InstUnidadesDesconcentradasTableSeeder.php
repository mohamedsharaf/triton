<?php

use Illuminate\Database\Seeder;

class InstUnidadesDesconcentradasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('inst_unidades_desconcentradas')->insert([
            [
                "lugar_dependencia_id" => "1",
                "municipio_id"         => "1",
                "nombre"               => "FISCALIA GENERAL DEL ESTADO - CALLE ESPAÑA N° 79 ESQUINA SAN ALBERTO",
                "direccion"            => "CALLE ESPAÑA N° 79 ESQUINA SAN ALBERTO",
                "created_at"           => date("Y-m-d H:i:s"),                
            ]
        ]);
    }
}
