<?php

use Illuminate\Database\Seeder;

class NuevoEnvioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {

        $envios = factory(\App\Models\Envio::class, 2)->create([
            'precio' => 3.00,
            'precio_cobertura' => 0.00,
            'fecha_almacen' => Carbon::now(),
            'estado_id' => 5,
            'punto_recogida_id' => 34,
            'punto_entrega_id' => 40,
            'cobertura_id' => 1,
            'embalaje_id' => 0,
            'usuario_id' => 1,
            'pedido_id' => factory(\App\Models\Pedido::class)->create([
                'base' => 3,
                'embalajes' => 0,
                'coberturas' => 0,
                'gestion' => 0.44,
                'descuento' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'estado_pago_id' => 4,
                'metodo_id' => 2,
                'usuario_id' => 1
            ])->id,
            'destinatario_id' => factory(\App\Models\Persona::class)->create()->id
        ])->each(function($envio) {
            $envio->paquete()->save(factory(\App\Models\Paquete::class)->make([
                'peso' => 2.00,
                'envio_id' => $envio->id
            ]));
        });

    }
}
