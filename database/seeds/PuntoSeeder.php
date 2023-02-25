<?php

use Illuminate\Database\Seeder;

class PuntoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('puntos')->insert([
            'autonomo' => 0,
            'nombre' => 'Punto de Bilbao',
            'direccion' => 'Dirección de punto de Bilbao',
            'telefono' => '912345678',
            'codigo_postal' => '48009',
            'horario' => '',
            'latitud' =>'43.265447',
            'longitud' => '-2.935245',
            'usuario_id' => 2,
            'localidad_id' => 3,
            'cerrado' => 0
        ]);
        DB::table('puntos')->insert([
            'autonomo' => 0,
            'nombre' => 'Punto de Alicante',
            'direccion' => 'Dirección de punto de Alicante',
            'telefono' => '912345678',
            'codigo_postal' => '03004',
            'horario' => '',
            'latitud' =>'38.347187',
            'longitud' => '-0.490593',
            'usuario_id' => 3,
            'localidad_id' => 11,
            'cerrado' => 0
        ]);
        DB::table('puntos')->insert([
            'autonomo' => 0,
            'nombre' => 'Punto de Madrid 1',
            'direccion' => 'Dirección de punto de Madrid',
            'telefono' => '912345678',
            'codigo_postal' => '28001',
            'horario' => '',
            'latitud' =>'40.414817',
            'longitud' => '-3.705700',
            'usuario_id' => 4,
            'localidad_id' => 4,
            'cerrado' => 0
        ]);
//        DB::table('puntos')->insert([
//            'localidad_id' => '3',
//            'nombre' => 'Punto de Bilbao',
//            'direccion' => 'Dirección de punto de bilbao',
//            'codigo_postal' => 48001,
//            'latitud' =>'43.268650',
//            'longitud' => '-2.946119',
//        ]);
//        DB::table('puntos')->insert([
//            'localidad_id' => '4',
//            'nombre' => 'Punto de Madrid',
//            'direccion' => 'Dirección de punto de madrid',
//            'codigo_postal' => 28001,
//            'latitud' =>'40.420300',
//            'longitud' => '-3.705774',
//        ]);
//        DB::table('puntos')->insert([
//            'localidad_id' => '5',
//            'nombre' => 'Punto de Valencia',
//            'direccion' => 'Dirección de punto de valencia',
//            'codigo_postal' => 46002,
//            'latitud' =>'39.470490',
//            'longitud' => '-0.378084',
//        ]);
//        DB::table('puntos')->insert([
//            'localidad_id' => '6',
//            'nombre' => 'Punto de Sevilla',
//            'direccion' => 'Dirección de punto de sevilla',
//            'codigo_postal' => 41001,
//            'latitud' =>'37.387697',
//            'longitud' => '-6.001813',
//        ]);
//        DB::table('puntos')->insert([
//            'localidad_id' => '7',
//            'nombre' => 'Punto de Málaga',
//            'direccion' => 'Dirección de punto de malaga',
//            'codigo_postal' => 29001,
//            'latitud' =>'36.718319',
//            'longitud' => '-4.420160',
//        ]);
    }
}
