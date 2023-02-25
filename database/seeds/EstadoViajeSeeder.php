<?php

use Illuminate\Database\Seeder;

class EstadoViajeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('estados_viaje')->insert([
            'id' => 1,
            'nombre' => 'reserva',
            'descripcion' => 'Viaje reservado'
        ]);

        DB::table('estados_viaje')->insert([
            'id' => 2,
            'nombre' => 'ruta',
            'descripcion' => 'Viaje en ruta'
        ]);
        DB::table('estados_viaje')->insert([
            'id' => 3,
            'nombre' => 'finalizado',
            'descripcion' => 'Viaje finalizado'
        ]);
        DB::table('estados_viaje')->insert([
            'id' => 4,
            'nombre' => 'cancelado',
            'descripcion' => 'Viaje cancelado por el usuario'
        ]);
    }
}
