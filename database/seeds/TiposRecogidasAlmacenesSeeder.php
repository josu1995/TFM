<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposRecogidasAlmacenesSeeder extends Seeder
{
    public function run()
    {
        DB::table('tipos_recogidas_e_commerce')->insert([
            'nombre' => 'Petición',
        ]);
        DB::table('tipos_recogidas_e_commerce')->insert([
            'nombre' => 'Automatizada',
        ]);
    }
}
