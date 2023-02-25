<?php

namespace App\Repositories;

// Modelos
use App\Models\Opcion;

class OpcionRepository
{
    // Precio por KG para envío
    public function getPrecioPorPeso()
    {
        return floatval(Opcion::where('nombre', 'precio_kg')->first(['valor'])->valor);
    }

    // Valor de comisión de la plataforma por envío en euros
    public function getComisionPlataforma()
    {
        return floatval(Opcion::where('nombre', 'comision_plataforma')->first(['valor'])->valor);
    }

    // Porcentaje de IVA
    public function getImpuestos()
    {
        return floatval(Opcion::where('nombre', 'iva')->first(['valor'])->valor);
    }

    // Porcentaje de IRPF
    public function getRetencionIRPF()
    {
        return floatval(Opcion::where('nombre', 'irpf')->first(['valor'])->valor);
    }

    // Valor de la comisión de un transportista por viaje en %
    public function getPrecioViaje()
    {
        return floatval(Opcion::where('nombre', 'precio_viaje')->first(['valor'])->valor / 100);
    }

    public function getPrecioViajePercent()
    {
        return floatval(Opcion::where('nombre', 'precio_viaje')->first(['valor'])->valor);
    }

    // Valor en euros de la comisión de un punto por envío
    public function getComisionPunto()
    {
        return floatval(Opcion::where('nombre', 'comision_punto')->first(['valor'])->valor);
    }

    // Valor la fianza cobrada a un transportista en euros
    public function getFianza()
    {
        return floatval(Opcion::where('nombre', 'fianza_transportista')->first(['valor'])->valor);
    }
}
