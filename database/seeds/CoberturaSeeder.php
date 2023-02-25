<?php

use Illuminate\Database\Seeder;

class CoberturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('coberturas')->insert([
            'id' => 1,
            'nombre' => 'básica',
            'descripcion' => 'Cobertura básica de envío',
            'valor' => '0'
        ]);
        DB::table('coberturas')->insert([
            'id' => 2,
            'nombre' => 'media',
            'descripcion' => 'Cobertura media de envío',
            'valor' => '5'
        ]);
        DB::table('coberturas')->insert([
            'id' => 3,
            'nombre' => 'completa',
            'descripcion' => 'Cobertura completa de viaje',
            'valor' => '10'
        ]);
        DB::table('coberturas')->insert([
            'id' => 4,
            'nombre' => 'extra',
            'descripcion' => 'Cobertura extra de viaje',
            'valor' => '20'
        ]);
    }
}
