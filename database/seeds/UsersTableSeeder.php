<?php

use Illuminate\Database\Seeder;

class UsersTableSeede extends Seeder
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
                "name"     => "Administrador",
                "email"    => "informatica@fiscalia.gob.bo",
                "password" => bcrypt('123456'),
            ]
        ]);
    }
}
