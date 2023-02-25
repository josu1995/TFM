<?php

use Illuminate\Database\Seeder;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('estados')->insert([
            'id' => 1,
            'nombre' => 'error',
            'descripcion' => 'Envío',
        ]);
        DB::table('estados')->insert([
            'id' => 2,
            'nombre' => 'creado',
            'descripcion' => 'Envío creado y pendiente validación',
        ]);
        DB::table('estados')->insert([
            'id' => 3,
            'nombre' => 'validado',
            'descripcion' => 'Envío validado y pendiente de pago',
        ]);
        DB::table('estados')->insert([
            'id' => 4,
            'nombre' => 'pagado',
            'descripcion' => 'Envío con pago procesado, pendiente de llevar al punto de entrega',
        ]);
        DB::table('estados')->insert([
            'id' => 5,
            'nombre' => 'entrega',
            'descripcion' => 'Envío en punto de entrega',
        ]);
        DB::table('estados')->insert([
            'id' => 6,
            'nombre' => 'ruta',
            'descripcion' => 'Envío en ruta',
        ]);
        DB::table('estados')->insert([
            'id' => 7,
            'nombre' => 'intermedio',
            'descripcion' => 'Envío en punto intermedio',
        ]);
        DB::table('estados')->insert([
            'id' => 8,
            'nombre' => 'recogida',
            'descripcion' => 'Envío en punto de recogida',
        ]);
        DB::table('estados')->insert([
            'id' => 9,
            'nombre' => 'finalizado',
            'descripcion' => 'Envío finalizado',
        ]);
        DB::table('estados')->insert([
            'id' => 10,
            'nombre' => 'seleccionado',
            'descripcion' => 'Envío seleccionado para ser transportado',
        ]);
        DB::table('estados')->insert([
            'id' => 11,
            'nombre' => 'devuelto',
            'descripcion' => 'Envío devuelto a origen',
        ]);
    }
}
