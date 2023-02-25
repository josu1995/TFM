<?php

use Illuminate\Database\Seeder;

class OpcionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('opciones')->insert([
            'id' => 1,
            'nombre' => 'comision_punto',
            'descripcion' => 'Comisión de punto por paquete',
            'valor' => 0.15
        ]);
        DB::table('opciones')->insert([
            'id' => 2,
            'nombre' => 'precio_kg',
            'descripcion' => 'Precio por kg de envío',
            'valor' => 3
        ]);
        DB::table('opciones')->insert([
            'id' => 3,
            'nombre' => 'precio_m3',
            'descripcion' => 'Precio por m3 de volumen de envío',
            'valor' => 0
        ]);
        DB::table('opciones')->insert([
            'id' => 4,
            'nombre' => 'precio_viaje',
            'descripcion' => 'Comisión de transportista por viaje',
            'valor' => 10
        ]);
        DB::table('opciones')->insert([
            'id' => 5,
            'nombre' => 'iva',
            'descripcion' => 'Porcentaje IVA',
            'valor' => 21
        ]);
        DB::table('opciones')->insert([
            'id' => 6,
            'nombre' => 'comision_plataforma',
            'descripcion' => 'Comisión de la plataforma',
            'valor' => 12
        ]);
        DB::table('opciones')->insert([
            'id' => 7,
            'nombre' => 'fianza_transportista',
            'descripcion' => 'Valor en € de fianza al crear un viaje',
            'valor' => 5
        ]);
        DB::table('opciones')->insert([
            'id' => 8,
            'nombre' => 'irpf',
            'descripcion' => 'Retención IRPF (%)',
            'valor' => 15
        ]);
    }
}
