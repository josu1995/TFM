<?php

use Illuminate\Database\Seeder;

class RangoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('rangos')->insert([
            'id' => 1,
            'minimo' => 0.00,
            'maximo' => 2.00,
            'valor' => 3.00,
            'opcion_id' => 2
        ]);
        DB::table('rangos')->insert([
            'id' => 2,
            'minimo' => 2.00,
            'maximo' => 5.00,
            'valor' => 4.00,
            'opcion_id' => 2
        ]);
        DB::table('rangos')->insert([
            'id' => 3,
            'minimo' => 5.00,
            'maximo' => 8.00,
            'valor' => 5.00,
            'opcion_id' => 2
        ]);
        DB::table('rangos')->insert([
            'id' => 4,
            'minimo' => 8.00,
            'maximo' => 10.00,
            'valor' => 6.00,
            'opcion_id' => 2
        ]);
        DB::table('rangos')->insert([
            'id' => 5,
            'minimo' => 10.00,
            'maximo' => 15.00,
            'valor' => 9.00,
            'opcion_id' => 2
        ]);
        DB::table('rangos')->insert([
            'id' => 6,
            'minimo' => 15.00,
            'maximo' => 20.00,
            'valor' => 12.00,
            'opcion_id' => 2
        ]);
    }
}
