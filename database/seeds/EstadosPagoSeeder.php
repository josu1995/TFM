<?php

use Illuminate\Database\Seeder;

class EstadosPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('estados_pago')->insert([
            'id' => 1,
            'nombre' => 'error',
            'descripcion' => 'Error en el pago',
        ]);
        DB::table('estados_pago')->insert([
            'id' => 2,
            'nombre' => 'iniciado',
            'descripcion' => 'Pago iniciado',
        ]);
        DB::table('estados_pago')->insert([
            'id' => 3,
            'nombre' => 'proceso',
            'descripcion' => 'Pago en proceso',
        ]);
        DB::table('estados_pago')->insert([
            'id' => 4,
            'nombre' => 'pagado',
            'descripcion' => 'Pagado',
        ]);
    }
}
