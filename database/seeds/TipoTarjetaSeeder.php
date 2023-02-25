<?php

use Illuminate\Database\Seeder;

class TipoTarjetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('tipos_tarjeta')->insert([
            'id' => 1,
            'nombre' => 'VISA',
        ]);
        DB::table('tipos_tarjeta')->insert([
            'id' => 2,
            'nombre' => 'Master Card',
        ]);
        DB::table('tipos_tarjeta')->insert([
            'id' => 6,
            'nombre' => 'DINERS',
        ]);
        DB::table('tipos_tarjeta')->insert([
            'id' => 7,
            'nombre' => 'PRIVADA',
        ]);
        DB::table('tipos_tarjeta')->insert([
            'id' => 8,
            'nombre' => 'AMEX',
        ]);
        DB::table('tipos_tarjeta')->insert([
            'id' => 9,
            'nombre' => 'JCB',
        ]);
        DB::table('tipos_tarjeta')->insert([
            'id' => 22,
            'nombre' => 'UPI',
        ]);
    }
}
