<?php

use Illuminate\Database\Seeder;

class SegLdUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('seg_ld_users')->insert([
            [
                "lugar_dependencia_id" => "1",
                "user_id"              => "1",
                "created_at"           => date("Y-m-d H:i:s"),
                "updated_at"           => date("Y-m-d H:i:s"),
            ]
        ]);
    }
}