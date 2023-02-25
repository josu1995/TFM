<?php

use Illuminate\Database\Seeder;

class RutaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Desde Bilbao
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 3,
            'localidad_fin_id' => 5,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 3,
            'localidad_fin_id' => 6,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 3,
            'localidad_fin_id' => 7,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 3,
            'localidad_fin_id' => 8,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 3,
            'localidad_fin_id' => 9,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 3,
            'localidad_fin_id' => 10,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 3,
            'localidad_fin_id' => 11,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 3,
            'localidad_fin_id' => 12,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 3,
            'localidad_fin_id' => 15,
            'localidad_intermedia_id' => 4
        ]);

        // Desde Valencia
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 5,
            'localidad_fin_id' => 3,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 5,
            'localidad_fin_id' => 6,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 5,
            'localidad_fin_id' => 7,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 5,
            'localidad_fin_id' => 9,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 5,
            'localidad_fin_id' => 12,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 5,
            'localidad_fin_id' => 15,
            'localidad_intermedia_id' => 4
        ]);

        // Desde Sevilla
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 6,
            'localidad_fin_id' => 3,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 6,
            'localidad_fin_id' => 5,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 6,
            'localidad_fin_id' => 8,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 6,
            'localidad_fin_id' => 9,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 6,
            'localidad_fin_id' => 10,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 6,
            'localidad_fin_id' => 10,
            'localidad_intermedia_id' => 15
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 6,
            'localidad_fin_id' => 11,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 6,
            'localidad_fin_id' => 11,
            'localidad_intermedia_id' => 15
        ]);

        // Desde Málaga
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 7,
            'localidad_fin_id' => 3,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 7,
            'localidad_fin_id' => 5,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 7,
            'localidad_fin_id' => 8,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 7,
            'localidad_fin_id' => 9,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 7,
            'localidad_fin_id' => 10,
            'localidad_intermedia_id' => 15
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 7,
            'localidad_fin_id' => 11,
            'localidad_intermedia_id' => 15
        ]);

        // Desde Barcelona
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 8,
            'localidad_fin_id' => 3,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 8,
            'localidad_fin_id' => 6,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 8,
            'localidad_fin_id' => 7,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 8,
            'localidad_fin_id' => 10,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 8,
            'localidad_fin_id' => 10,
            'localidad_intermedia_id' => 5
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 8,
            'localidad_fin_id' => 11,
            'localidad_intermedia_id' => 5
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 8,
            'localidad_fin_id' => 12,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 8,
            'localidad_fin_id' => 15,
            'localidad_intermedia_id' => 4
        ]);

        // Desde Zaragoza
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 9,
            'localidad_fin_id' => 3,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 9,
            'localidad_fin_id' => 6,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 9,
            'localidad_fin_id' => 7,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 9,
            'localidad_fin_id' => 10,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 9,
            'localidad_fin_id' => 11,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 9,
            'localidad_fin_id' => 11,
            'localidad_intermedia_id' => 5
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 9,
            'localidad_fin_id' => 12,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 9,
            'localidad_fin_id' => 15,
            'localidad_intermedia_id' => 4
        ]);

        // Desde Murcia
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 10,
            'localidad_fin_id' => 3,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 10,
            'localidad_fin_id' => 6,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 10,
            'localidad_fin_id' => 7,
            'localidad_intermedia_id' => 15
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 10,
            'localidad_fin_id' => 8,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 10,
            'localidad_fin_id' => 9,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 10,
            'localidad_fin_id' => 12,
            'localidad_intermedia_id' => 4
        ]);

        // Desde Alicante
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 11,
            'localidad_fin_id' => 3,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 11,
            'localidad_fin_id' => 6,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 11,
            'localidad_fin_id' => 7,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 11,
            'localidad_fin_id' => 7,
            'localidad_intermedia_id' => 15
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 11,
            'localidad_fin_id' => 8,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 11,
            'localidad_fin_id' => 9,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 11,
            'localidad_fin_id' => 12,
            'localidad_intermedia_id' => 4
        ]);

        // Desde Córdoba
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 12,
            'localidad_fin_id' => 3,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 12,
            'localidad_fin_id' => 5,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 12,
            'localidad_fin_id' => 8,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 12,
            'localidad_fin_id' => 9,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 12,
            'localidad_fin_id' => 10,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 12,
            'localidad_fin_id' => 11,
            'localidad_intermedia_id' => 4
        ]);

        // Desde Granada
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 15,
            'localidad_fin_id' => 3,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 15,
            'localidad_fin_id' => 5,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 15,
            'localidad_fin_id' => 8,
            'localidad_intermedia_id' => 4
        ]);
        DB::table('rutas')->insert([
            'localidad_inicio_id' => 15,
            'localidad_fin_id' => 9,
            'localidad_intermedia_id' => 4
        ]);

    }
}
