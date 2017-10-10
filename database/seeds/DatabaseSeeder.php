<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UbgeDepartamentosTableSeeder::class);
        $this->call(UbgeProvinciasTableSeeder::class);
        $this->call(UbgeMunicipiosTableSeeder::class);

        $this->call(InstLugaresDependenciaTableSeeder::class);
        $this->call(InstUnidadesDesconcentradasTableSeeder::class);

        $this->call(SegRolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(SegUdUsersTableSeeder::class);
    }
}
