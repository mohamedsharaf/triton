<?php

use Illuminate\Database\Seeder;

class SegRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('seg_roles')->insert([
            [
                "nombre"     => "SUPERADMINISTRADOR",
                "created_at" => date("Y-m-d H:i:s"), 
            ]
        ]);
    }
}
