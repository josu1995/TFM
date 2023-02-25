<?php

use Illuminate\Database\Seeder;

class LocalidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('localidades')->insert([
            'id' => 3,
            'provincia_id' => '44',
            'nombre' => 'Bilbao',
            'nombre_seo' => 'Bilbao',
            'codigo_postal' => '48001',
            'latitud' =>'43.268650',
            'longitud' => '-2.946119',
        ]);
        DB::table('localidades')->insert([
            'id' => 4,
            'provincia_id' => '15',
            'nombre' => 'Madrid',
            'nombre_seo' => 'Madrid',
            'codigo_postal' => '28001',
            'latitud' =>'40.420300',
            'longitud' => '-3.705774',
        ]);
        DB::table('localidades')->insert([
            'id' => 5,
            'provincia_id' => '36',
            'nombre' => 'Valencia',
            'nombre_seo' => 'Valencia',
            'codigo_postal' => '46002',
            'latitud' =>'39.470490',
            'longitud' => '-0.378084',
        ]);
        DB::table('localidades')->insert([
            'id' => 6,
            'provincia_id' => '12',
            'nombre' => 'Sevilla',
            'nombre_seo' => 'Sevilla',
            'codigo_postal' => '41001',
            'latitud' =>'37.387697',
            'longitud' => '-6.001813',
        ]);
        DB::table('localidades')->insert([
            'id' => 7,
            'provincia_id' => '19',
            'nombre' => 'Málaga',
            'nombre_seo' => 'Malaga',
            'codigo_postal' => '29001',
            'latitud' =>'36.718319',
            'longitud' => '-4.420160',
        ]);
        DB::table('localidades')->insert([
            'id' => 8,
            'provincia_id' => '33',
            'nombre' => 'Barcelona',
            'nombre_seo' => 'Barcelona',
            'codigo_postal' => '08001',
            'latitud' =>'41.405699',
            'longitud' => '2.158947',
        ]);
        DB::table('localidades')->insert([
            'id' => 9,
            'provincia_id' => '52',
            'nombre' => 'Zaragoza',
            'nombre_seo' => 'Zaragoza',
            'codigo_postal' => '50001',
            'latitud' =>'41.652751',
            'longitud' => '-0.891142',
        ]);
        DB::table('localidades')->insert([
            'id' => 10,
            'provincia_id' => '27',
            'nombre' => 'Murcia',
            'nombre_seo' => 'Murcia',
            'codigo_postal' => '30001',
            'latitud' =>'37.992753',
            'longitud' => '-1.129511',
        ]);
        DB::table('localidades')->insert([
            'id' => 11,
            'provincia_id' => '9',
            'nombre' => 'Alicante',
            'nombre_seo' => 'Alicante',
            'codigo_postal' => '03001',
            'latitud' =>'38.352979',
            'longitud' => '-0.489121',
        ]);
        DB::table('localidades')->insert([
            'id' => 12,
            'provincia_id' => '14',
            'nombre' => 'Córdoba',
            'nombre_seo' => 'Cordoba',
            'codigo_postal' => '14001',
            'latitud' =>'37.892817',
            'longitud' => '-4.777831',
        ]);
        DB::table('localidades')->insert([
            'id' => 15,
            'provincia_id' => '30',
            'nombre' => 'Granada',
            'nombre_seo' => 'Granada',
            'codigo_postal' => '18001',
            'latitud' =>'37.174513',
            'longitud' => '-3.600542',
        ]);
    }
}
