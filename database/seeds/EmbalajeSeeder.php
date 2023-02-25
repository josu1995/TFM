<?php

use Illuminate\Database\Seeder;

class EmbalajeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('embalajes')->insert([
            'id' => 0,
            'nombre' => 'Sin embalaje',
            'descripcion' => '¡Ya tengo embalaje!',
            'precio' => 0.00,
            'activo' => 1
        ]);
        DB::table('embalajes')->insert([
            'id' => 1,
            'nombre' => 'Bolsa',
            'descripcion' => 'Bolsa de seguridad (0.49€)',
            'descripcion_factura' => 'Bolsa plástico opaco de seguridad',
            'texto' => '40cm x 50cm',
            'precio' => 0.59,
            'activo' => 1
        ]);
    }
}
