<?php

use Illuminate\Database\Seeder;

class SegUdUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('seg_ud_users')->insert([
            [
                "unidad_desconcentrada_id" => "1",
                "user_id"                  => "1",
                "created_at"               => date("Y-m-d H:i:s"), 
            ]
        ]);
    }
}
