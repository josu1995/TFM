<?php

use Illuminate\Database\Seeder;

class TipoAlertaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('tipo_alertas')->insert([
            'id' => 1,
            'nombre' => 'Puntual',
        ]);
        DB::table('tipo_alertas')->insert([
            'id' => 2,
            'nombre' => 'Habitual',
        ]);
    }
}
