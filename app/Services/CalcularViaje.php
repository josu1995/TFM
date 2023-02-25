<?php

namespace App\Services;

use Session;

// Modelos
use App\Models\Envio;
use App\Models\Punto;

// Repositorios
use App\Repositories\EnvioRepository;
use App\Repositories\OpcionRepository;

class CalcularViaje
{
    private $opcionRepository;

    public function __construct(OpcionRepository $opcionRepository)
    {
        $this->opcionRepository = $opcionRepository;
    }

    // Devuelve importe que se llevará el transportista
    // Recibe envío y punto de inicio de viaje & punto fin de viaje
    public function calcularEnvio(Envio $envio, Punto $inicio, Punto $fin)
    {
        $precioOriginal = floatval($envio->precio);

        if(($envio->puntoEntrega->id != $inicio->id || $envio->puntoRecogida->id != $fin->id) && ($envio->puntoEntrega->localidad->id != $inicio->localidad->id || $envio->puntoRecogida->localidad->id != $fin->localidad->id)) {
//            $iva = (($precioOriginal / 2) * $this->opcionRepository->getPrecioViaje()) * ($this->opcionRepository->getImpuestos() / 100);
//            return number_format($precioOriginal/2 - (($precioOriginal / 2) * $this->opcionRepository->getPrecioViaje() + $iva), 2);
            return number_format($precioOriginal/2, 2);
        } else {
//            $iva = (($precioOriginal) * $this->opcionRepository->getPrecioViaje()) * ($this->opcionRepository->getImpuestos() / 100);
//            return number_format($precioOriginal - ($precioOriginal * $this->opcionRepository->getPrecioViaje() + $iva), 2);
            return number_format($precioOriginal, 2);
        }
    }

    public function calcularEnvioPorLocalidad(Envio $envio, $inicio, $fin)
    {
        $precioOriginal = floatval($envio->precio);

        if(($envio->puntoEntrega->localidad->id != $inicio || $envio->puntoRecogida->localidad->id != $fin)) {
            return number_format($precioOriginal/2, 2);
        } else {
            return number_format($precioOriginal, 2);
        }
    }

    public function calcularGastosGestionEnvio(Envio $envio, Punto $inicio, Punto $fin)
    {
        $precioOriginal = floatval($envio->precio);

        if($envio->puntoEntrega->id != $inicio->id || $envio->puntoRecogida->id != $fin->id) {
            return number_format((($precioOriginal / 2) * $this->opcionRepository->getPrecioViaje()) + (($precioOriginal / 2) * $this->opcionRepository->getPrecioViaje()) * ($this->opcionRepository->getImpuestos() / 100), 2);
        } else {
            return number_format((($precioOriginal) * $this->opcionRepository->getPrecioViaje()) + (($precioOriginal) * $this->opcionRepository->getPrecioViaje()) * ($this->opcionRepository->getImpuestos() / 100), 2);
        }
    }

    public function calcularEnvioSinIva(Envio $envio, Punto $inicio, Punto $fin)
    {
        $precioOriginal = floatval($envio->precio);

        if($envio->puntoEntrega->id != $inicio->id || $envio->puntoRecogida->id != $fin->id) {
            return number_format($precioOriginal/2 - (($precioOriginal / 2) * $this->opcionRepository->getPrecioViaje()), 2);
        } else {
            return number_format($precioOriginal - ($precioOriginal * $this->opcionRepository->getPrecioViaje()), 2);
        }
    }

    public function calcularViaje($envios = null, $localidadOrigen = null, $localidadDestino = null)
    {
        $total = 0;
        if($envios) {
            foreach ($envios as $envio) {
                $total += $this->calcularEnvioPorLocalidad($envio, $localidadOrigen, $localidadDestino);
            }
        } else if(Session::has('envios_seleccionados')) {
            foreach (Session::get('envios_seleccionados') as $envio) {
                $total += $this->calcularEnvio($envio['envio'], $envio['inicio'], $envio['fin']);
            }
        }

//        return number_format($total - $this->calcularGastosGestion(), 2);
        return number_format($total, 2);
    }

    public function calcularGastosGestion($envios = null, $localidadOrigen = null, $localidadDestino = null) {
        $total = 0;
        if($envios) {
            foreach ($envios as $envio) {
                $total += $this->calcularEnvioPorLocalidad($envio, $localidadOrigen, $localidadDestino);
            }
        } else if(Session::has('envios_seleccionados')) {
            foreach (Session::get('envios_seleccionados') as $envio) {
                $total += $this->calcularEnvio($envio['envio'], $envio['inicio'], $envio['fin']);
            }
        }
        $iva = ($total * $this->opcionRepository->getPrecioViaje()) * ($this->opcionRepository->getImpuestos() / 100);

        $gastosGestion = $total * $this->opcionRepository->getPrecioViaje() + $iva;

        return number_format($gastosGestion, 2);
    }

    public function calcularIVA($envios = null, $localidadOrigen = null, $localidadDestino = null) {
        $total = 0;
        if($envios) {
            foreach ($envios as $envio) {
                $total += $this->calcularEnvioPorLocalidad($envio, $localidadOrigen, $localidadDestino);
            }
        } else if(Session::has('envios_seleccionados')) {
            foreach (Session::get('envios_seleccionados') as $envio) {
                $total += $this->calcularEnvio($envio['envio'], $envio['inicio'], $envio['fin']);
            }
        }
        $iva = ($total * $this->opcionRepository->getPrecioViaje()) * ($this->opcionRepository->getImpuestos() / 100);

        return number_format($iva, 2);
    }

}
