<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                "rol_id"      => "1",
                "name"        => "INFORMATICA",
                "email"       => "informatica@fiscalia.gob.bo",
                "password"    => bcrypt('123456'),
                "created_at"  => date("Y-m-d H:i:s"),
            ]
        ]);
    }
}
