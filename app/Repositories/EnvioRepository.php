<?php

namespace App\Repositories;

// Helpers
use App\Helpers\ModelHelper;

use DB;
// Servicios
use App\Services\CalcularPrecio;

// Modelos
use App\Models\Usuario;
use App\Models\Envio;
use App\Models\Persona;
use App\Models\Paquete;
use App\Models\Punto;
use App\Models\Cobertura;
use App\Models\Estado;
use App\Models\Metodo;
use App\Models\Ruta;

class EnvioRepository
{
    // Reglas de validación
    public $reglas = [
        // Envio
        'descripcion' => 'required|max:255',
        'cobertura' => 'required:exists:coberturas',
        'valorDeclarado' => 'required_unless:cobertura,1,""|numeric',
        'embalaje' => 'required:exists:embalajes',
        // Paquete
        'peso' => 'required|numeric|max:20|min:0',
        'alto' => 'required|numeric',
        'ancho' => 'required|numeric',
        'largo' => 'required|numeric',
        // Destinatario
        'nombre' => 'required|regex:/^[\pL\s]+$/u',
        'apellidos' => 'required|regex:/^[\pL\s]+$/u',
        'email' => 'required|email',
        'telefono' => 'phone:ES,mobile',
        // Puntos
        'origen' => 'required|exists:puntos,id',
        'destino' => 'required|exists:puntos,id',

        'dimensiones' => 'required|numeric|max:150'
    ];

    // Mensajes de devolución de validación
    public $mensajes = [
        'required:exists' => 'El campo :attribute es obligatorio',
        'required' => 'El campo :attribute es obligatorio',
        'exists' => 'El :attribute no existe',
        'dni.regex' => 'El formato de :attribute no es correcto',
        'peso.max' => 'El peso no debe ser superior a 20kg',
        'descripcion.max' => 'La ::attribute es demasiado larga (máximo :max)',
        'descripcion.required' => 'El campo contenido es obligatorio',
        'dimensiones.max' => 'Las dimensiones totales no deben superar los 150cm',
        'telefono.phone' => 'El campo teléfono no tiene un formato correcto',
        'telefono.movil' => 'El campo teléfono no tiene un formato correcto',
        'nombre.regex' => 'El campo nombre sólo puede contener letras',
        'apellidos.regex' => 'El campo apellido sólo puede contener letras',
        'apellidos.required' => 'El campo apellido es obligatorio',
        'valorDeclarado.required_unless' => 'El campo valor declarado es obligatorio con la cobertura seleccionada',
        'valorDeclarado.numeric' => 'El campo :attribute debe ser numérico'

    ];

    /**
     * Get envío creados (sin pagar) por Usuario
     *
     * @return Collection
     */
    public function enviosPendientesPorUsuario(Usuario $usuario)
    {
        $envios = $usuario->envios;
        $enviosPendientes = [];

        foreach ($envios as $envio) {
            if ($envio->estado_id < 3) {
                $enviosPendientes[] = $envio;
            }
        }

        return $enviosPendientes;
    }


    public function eliminarEnvio($seguimiento)
    {
        $envio = Envio::where('codigo_seguimiento', $seguimiento)->first();

        if($envio->estado_id < 3) {
            $envio->delete();
        }

        return $envio;
    }

    public function getEnviosEnPunto(Punto $puntoOrigen, Punto $puntoDestino) {

        $envios =
            // Envíos concordantes con puntos
            Envio::where(function($query) use($puntoOrigen, $puntoDestino) {
                $query->where('punto_entrega_id', $puntoOrigen->id)
                    ->where('punto_recogida_id', $puntoDestino->id)
                    ->where('estado_id', Estado::ENTREGA);
            })
            ->orWhere(function($query) use($puntoOrigen, $puntoDestino) {
                $query->where('punto_recogida_id', $puntoDestino->id)
                    ->whereHas('posiciones', function ($query) use($puntoOrigen) {
                        $query->where('punto_destino_id', $puntoOrigen->id);
                    })
                    ->where('estado_id', Estado::INTERMEDIO);
            })
            ->with('paquete')
            ->orderBy('envios.fecha_almacen')
            ->get();

        return $envios;
    }

    public function getIntermediosEnOrigen(Punto $puntoOrigen, Punto $puntoDestino) {

        $localidades = Ruta::where('localidad_inicio_id', $puntoOrigen->localidad->id)
            ->where('localidad_intermedia_id', $puntoDestino->localidad->id)
            ->get(['localidad_fin_id'])->pluck('localidad_fin_id')->toArray();

        $puntos = Punto::whereIn('localidad_id', $localidades)->pluck('id')->toArray();

        $envios =
            // Envíos concordantes con puntos
            Envio::
            // Envios entre punto origen y localidad intermedia
            where(function($query) use($puntoOrigen, $puntoDestino, $puntos) {
//                if($puntoDestino->completo == 0) {
                    $query->where('punto_entrega_id', $puntoOrigen->id)
                        ->whereIn('punto_recogida_id', $puntos)
                        ->where('estado_id', Estado::ENTREGA);
//                }
            })
            ->with('paquete')
            ->orderBy('envios.fecha_almacen')
            ->get();

        return $envios;
    }

    public function getIntermediosEnPuntoIntermedio(Punto $puntoOrigen, Punto $puntoDestino) {

        $localidades = Ruta::where('localidad_inicio_id', $puntoOrigen->localidad->id)
            ->where('localidad_intermedia_id', $puntoDestino->localidad->id)
            ->get(['localidad_fin_id'])->pluck('localidad_fin_id')->toArray();

        $puntos = Punto::whereIn('localidad_id', $localidades)->pluck('id')->toArray();

        $envios =
            // Envíos concordantes con puntos
            Envio::
                // Envio que se encuentra en punto intermedio
                // La posición tiene como punto intermedio el punto que se recibe como origen de viaje
                // El punto intermedio es el último que se ha creado
                where(function($query) use($puntoOrigen, $puntoDestino) {
                    $query->where('punto_recogida_id', $puntoDestino->id)
                        ->whereHas('posiciones', function ($query) use($puntoOrigen) {
                            $query->where('punto_destino_id', $puntoOrigen->id);
                        })
                        ->where('estado_id', Estado::INTERMEDIO);
                })
                ->with('paquete')
                ->orderBy('envios.fecha_almacen')
                ->get();

        return $envios;
    }

    // Consultas de envíos para crear viajes
    public function getEnviosPorPunto(Punto $puntoOrigen, Punto $puntoDestino)
    {
        // Consulta SQL equivalente
        // "select * from `envios` inner join `puntos` on `envios`.`punto_entrega_id` = `puntos`.`id`
        // where ((`envios`.`punto_entrega_id` = ? and `envios`.`punto_recogida_id` = ? and `envios`.`estado_id` = ?)
        // or (`envios`.`punto_entrega_id` = ? and 0 = 1 and `estado_id` = ?)
        // or (`envios`.`punto_recogida_id` = ?
        // and exists (select * from `posiciones` where `posiciones`.`envio_id` = `envios`.`id` and `punto_id` = ? and `posiciones`.`deleted_at` is null)
        // and `estado_id` = ?)) and `envios`.`deleted_at` is null order by `envios`.`created_at` asc"

        // Cálculo de ciudad intermedias para puntos origen y destino
        $localidades = Ruta::where('localidad_inicio_id', $puntoOrigen->localidad->id)
                    ->where('localidad_intermedia_id', $puntoDestino->localidad->id)
                    ->get(['localidad_fin_id'])->pluck('localidad_fin_id')->toArray();

        $puntos = Punto::whereIn('localidad_id', $localidades)->pluck('id')->toArray();

        $envios =
                // Envíos concordantes con puntos
                Envio::where(function($query) use($puntoOrigen, $puntoDestino) {
                    $query->where('punto_entrega_id', $puntoOrigen->id)
                    ->where('punto_recogida_id', $puntoDestino->id)
                    ->where('estado_id', Estado::ENTREGA);
                })
                // Envios entre punto origen y localidad intermedia
                ->orWhere(function($query) use($puntoOrigen, $puntoDestino, $puntos) {
//                    if($puntoDestino->completo == 0) {
                        $query->where('punto_entrega_id', $puntoOrigen->id)
                        ->whereIn('punto_recogida_id', $puntos)
                        ->where('estado_id', Estado::ENTREGA);
//                    }
                })
                // Envio que se encuentra en punto intermedio
                // La posición tiene como punto intermedio el punto que se recibe como origen de viaje
                // El punto intermedio es el último que se ha creado
                ->orWhere(function($query) use($puntoOrigen, $puntoDestino) {
                    $query->where('punto_recogida_id', $puntoDestino->id)
                    ->whereHas('posiciones', function ($query) use($puntoOrigen) {
                        $query->where('punto_destino_id', $puntoOrigen->id);
                    })
                    ->where('estado_id', Estado::INTERMEDIO);
                })
                ->with('paquete')
                ->orderBy('envios.fecha_almacen')
                ->get();

                /*
                No hay búsqueda entre pto. intermedio y pto. intermedio para evitar círculos cerrados de transporte
                Sólo origen-destino, origen-punto intermedio, punto intermedio-destino
                 */

        return $envios;
    }
}
