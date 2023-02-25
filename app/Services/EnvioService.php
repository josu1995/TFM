<?php

namespace App\Services;

// Utilidades
use App\Models\Embalaje;
use App\Models\Localidad;
use Uuid;

// Eventos
use Event;
use App\Events\CambiosEstado;

// Servicios
use App\Services\CalcularPrecio;

// Modelos
use App\Models\Usuario;
use App\Models\Envio;
use App\Models\Cobertura;
use App\Models\Persona;
use App\Models\Punto;
use App\Models\Estado;
use App\Models\Paquete;
use App\Models\Ruta;

class EnvioService
{
    // Sistema de cálculo de precio
    protected $calcularPrecio;

    public function __construct(CalcularPrecio $calcularPrecio)
    {
        $this->calcularPrecio = $calcularPrecio;
    }

    // Creación de un envío
    public function crearEnvio(Usuario $usuario, $request)
    {
        // Creación envío
        $envio = new Envio();
        $envio->descripcion = $request['descripcion'];
        $envio->codigo = Uuid::generate();

        // Cobertura
        $cobertura = Cobertura::find($request['cobertura']);
        // Valor declarado
        if ($cobertura->valor > 0) {
            $envio->valor_declarado = $request['valorDeclarado'];
        }
        // Embalaje
        $embalaje = Embalaje::find($request['embalaje']);

        // Paquete (guardamos sólo un paquete por envío)
        $paquete = new Paquete();
        $paquete->peso = $request['peso'];
        $paquete->alto = $request['alto'];
        $paquete->ancho = $request['ancho'];
        $paquete->largo = $request['largo'];

        // Destinatario
        $destinatario = new Persona();
        $destinatario->nombre = $request['nombre'];
        $destinatario->apellidos = $request['apellidos'];
        $destinatario->email = $request['email'];
        $destinatario->telefono = $request['telefono'];
        $destinatario->save();

        // Punto Entrega
        $puntoEntrega = Punto::find($request['origen']);
        // Punto Recogida
        $puntoRecogida = Punto::find($request['destino']);

        // Persistencia y relaciones
        $envio->cobertura()->associate($cobertura);
        $envio->embalaje()->associate($embalaje);
        $envio->destinatario()->associate($destinatario);
        $envio->puntoEntrega()->associate($puntoEntrega);
        $envio->puntoRecogida()->associate($puntoRecogida);
        $envio->usuario()->associate($usuario);
        $envio->save();

        // Cambio de estado
        $this->cambioEstado($envio, Estado::VALIDADO);

        // Guardamos paquete
        $paquete->save();
        $envio->paquete()->save($paquete);

        return $envio;
    }

    public function editarEnvio(Envio $envio, $request, Usuario $usuario)
    {
        $envio->descripcion = $request['descripcion'];

        // Valor declarado
        if ($request['valorDeclarado']) {
            $envio->valor_declarado = $request['valorDeclarado'];
        }

        // Embalaje
        $embalaje = Embalaje::find($request['embalaje']);
        // Cobertura
        $cobertura = Cobertura::find($request['cobertura']);

        // Paquete
        $paquete = Paquete::find($request['paquete_id']);
        $paquete->peso = $request['peso'];
        $paquete->alto = $request['alto'];
        $paquete->ancho = $request['ancho'];
        $paquete->largo = $request['largo'];
        $paquete->envio->embalaje = $request['embalaje'];
        $paquete->save();

        // Destinatario
        $destinatario = new Persona();
        $destinatario->nombre = $request['nombre'];
        $destinatario->apellidos = $request['apellidos'];
        $destinatario->email = $request['email'];
        $destinatario->telefono = $request['telefono'];
        $destinatario->dni = $request['dni'];
        $destinatario->save();

        // Punto Entrega
        $puntoEntrega = Punto::find($request['origen']);
        // Punto Recogida
        $puntoRecogida = Punto::find($request['destino']);

        // Persistencia y relaciones
        if ($embalaje) {
            $envio->embalaje()->associate($embalaje);
        }
        if ($cobertura) {
            $envio->cobertura()->associate($cobertura);
        }
        $envio->destinatario()->associate($destinatario);
        $envio->puntoEntrega()->associate($puntoEntrega);
        $envio->puntoRecogida()->associate($puntoRecogida);
        $envio->usuario()->associate($usuario);

        // Cambio de estado
        $this->cambioEstado($envio, Estado::VALIDADO);

        $envio->save();

        return $envio;
    }

    public function actualizacionPrecio(Envio $envio)
    {
        $envio->precio = $this->calcularPrecio->calcularPrecio($envio);
        $envio->precio_cobertura = $envio->cobertura->valor;
        $envio->save();

        return $envio;
    }

    // Cambiar estado de envío
    public function cambioEstado(Envio $envio, $estadoID)
    {
        $estado = Estado::find($estadoID);
        $envio->estado()->associate($estado);
        // Disparamos evento cambio de estado
        Event::fire(new CambiosEstado($envio, $estado));

        $envio->save();

        return $envio;
    }

    public function getEnviosCountPorLocalidad($localidadOrigen, $localidadDestino)
    {
        return Envio::where('estado_id', Estado::ENTREGA)
            ->whereHas(
                'puntoEntrega', function ($query) use ($localidadOrigen) {
                $query->where('localidad_id', $localidadOrigen);
            })
            ->whereHas(
                'puntoRecogida', function ($query) use ($localidadDestino) {
                $query->where('localidad_id', $localidadDestino);
            })
            // Gestion de intermedios
            ->orWhere(function ($query) use ($localidadDestino, $localidadOrigen) {
                $query->where('estado_id', Estado::ENTREGA)
                    ->whereHas(
                        'puntoRecogida', function ($query) use ($localidadDestino, $localidadOrigen) {
                        $query->whereIn('localidad_id', function ($query2) use ($localidadDestino, $localidadOrigen) {
                            $query2->select('localidad_fin_id')->from(with(new Ruta)->getTable())
                                ->where([['localidad_inicio_id', $localidadOrigen], ['localidad_intermedia_id', $localidadDestino]]);
                        });
                    })
                    ->whereHas(
                        'puntoEntrega', function ($query) use ($localidadOrigen) {
                        $query->where('localidad_id', $localidadOrigen);
                    });
            })
            ->orWhere(function ($query) use ($localidadDestino, $localidadOrigen) {
                $query->where('estado_id', Estado::INTERMEDIO)
                    ->whereHas(
                        'puntoRecogida', function ($query) use ($localidadDestino) {
                        $query->where('localidad_id', $localidadDestino);
                    })
                    ->whereHas(
                        'posiciones', function ($query) use ($localidadDestino, $localidadOrigen) {
                        $query->whereHas(
                            'puntoDestino', function ($query) use ($localidadOrigen) {
                            $query->where('localidad_id', $localidadOrigen);
                        });
                    });
            })->count();
    }

    public function getEnviosIdPorLocalidad($localidadOrigen, $localidadDestino)
    {
        return Envio::where('estado_id', Estado::ENTREGA)
            ->whereHas(
                'puntoEntrega', function ($query) use ($localidadOrigen) {
                $query->where('localidad_id', $localidadOrigen);
            })
            ->whereHas(
                'puntoRecogida', function ($query) use ($localidadDestino) {
                $query->where('localidad_id', $localidadDestino);
            })
            // Gestion de intermedios
            ->orWhere(function ($query) use ($localidadDestino, $localidadOrigen) {
                $query->where('estado_id', Estado::ENTREGA)
                    ->whereHas(
                        'puntoRecogida', function ($query) use ($localidadDestino, $localidadOrigen) {
                        $query->whereIn('localidad_id', function ($query2) use ($localidadDestino, $localidadOrigen) {
                            $query2->select('localidad_fin_id')->from(with(new Ruta)->getTable())
                                ->where([['localidad_inicio_id', $localidadOrigen], ['localidad_intermedia_id', $localidadDestino]]);
                        });
                    })
                    ->whereHas(
                        'puntoEntrega', function ($query) use ($localidadOrigen) {
                        $query->where('localidad_id', $localidadOrigen);
                    });
            })
            ->orWhere(function ($query) use ($localidadDestino, $localidadOrigen) {
                $query->where('estado_id', Estado::INTERMEDIO)
                    ->whereHas(
                        'puntoRecogida', function ($query) use ($localidadDestino) {
                        $query->where('localidad_id', $localidadDestino);
                    })
                    ->whereHas(
                        'posiciones', function ($query) use ($localidadDestino, $localidadOrigen) {
                        $query->whereHas(
                            'puntoDestino', function ($query) use ($localidadOrigen) {
                            $query->where('localidad_id', $localidadOrigen);
                        });
                    });
            })->pluck('id')->toArray();
    }

    public function getEnviosPorPuntos($puntosOrigen, $puntosDestino, $localidadOrigen, $localidadDestino)
    {

        $localidades = Ruta::where('localidad_inicio_id', $localidadOrigen->id)
            ->where('localidad_intermedia_id', $localidadDestino->id)
            ->get(['localidad_fin_id'])->pluck('localidad_fin_id')->toArray();

        $puntos = Punto::whereIn('localidad_id', $localidades)->pluck('id')->toArray();

        return Envio::select('id', 'precio')
            ->where(function ($query) use ($puntosOrigen, $puntosDestino) {
                $query->where('estado_id', Estado::ENTREGA);
                $query->whereIn('punto_entrega_id', $puntosOrigen);
                $query->whereIn('punto_recogida_id', $puntosDestino);
            })
            // Gestion de intermedios
            ->orWhere(function ($query) use ($puntosOrigen, $puntos) {
                $query->where('estado_id', Estado::ENTREGA);
                $query->whereIn('punto_entrega_id', $puntosOrigen);
                $query->whereIn('punto_recogida_id', $puntos);
            })
            ->orWhere(function ($query) use ($puntosDestino, $puntosOrigen) {
                $query->where('estado_id', Estado::INTERMEDIO);
                $query->whereIn('punto_recogida_id', $puntosDestino)
                    ->whereHas('posiciones', function ($query) use ($puntosOrigen) {
                        $query->whereIn('punto_destino_id', $puntosOrigen);
                    });
            })->with(['paquete' => function ($query) {
                $query->select('envio_id', 'peso', 'ancho', 'largo', 'alto');
            }])->get();
    }

    public function getEnviosCountPorPuntos($puntosOrigen, $puntosDestino, $localidadOrigenId, $localidadDestinoId)
    {

        $localidades = Ruta::where('localidad_inicio_id', $localidadOrigenId)
            ->where('localidad_intermedia_id', $localidadDestinoId)
            ->get(['localidad_fin_id'])->pluck('localidad_fin_id')->toArray();

        $puntos = Punto::whereIn('localidad_id', $localidades)->pluck('id')->toArray();

        return Envio::select('id', 'precio')
            ->where(function ($query) use ($puntosOrigen, $puntosDestino) {
                $query->where('estado_id', Estado::ENTREGA);
                $query->whereIn('punto_entrega_id', $puntosOrigen);
                $query->whereIn('punto_recogida_id', $puntosDestino);
            })
            // Gestion de intermedios
            ->orWhere(function ($query) use ($puntosOrigen, $puntos) {
                $query->where('estado_id', Estado::ENTREGA);
                $query->whereIn('punto_entrega_id', $puntosOrigen);
                $query->whereIn('punto_recogida_id', $puntos);
            })
            ->orWhere(function ($query) use ($puntosDestino, $puntosOrigen) {
                $query->where('estado_id', Estado::INTERMEDIO);
                $query->whereIn('punto_recogida_id', $puntosDestino)
                    ->whereHas('posiciones', function ($query) use ($puntosOrigen) {
                        $query->whereIn('punto_destino_id', $puntosOrigen);
                    });
            })->with(['paquete' => function ($query) {
                $query->select('envio_id', 'peso', 'ancho', 'largo', 'alto');
            }])->count();
    }

}
