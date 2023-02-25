<?php

use Illuminate\Database\Seeder;

class MetodosPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('metodos')->insert([
            'id' => 1,
            'nombre' => 'paypal',
            'descripcion' => 'Paypal',
        ]);
        DB::table('metodos')->insert([
            'id' => 2,
            'nombre' => 'tarjeta',
            'descripcion' => 'Tarjeta de crédito / débito',
        ]);
        DB::table('metodos')->insert([
            'id' => 3,
            'nombre' => 'transferencia',
            'descripcion' => 'Transferencia bancaria',
        ]);
        DB::table('metodos')->insert([
            'id' => 4,
            'nombre' => 'gratuito',
            'descripcion' => 'Pedido gratuito',
        ]);
    }
}
