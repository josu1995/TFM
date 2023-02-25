<?php

namespace App\Services;

// Modelos
use App\Models\Comision;

// Repositorios
use App\Repositories\OpcionRepository;

class ComisionService
{
    private $opcionRepository;

    public function __construct(OpcionRepository $opcionRepository)
    {
        $this->opcionRepository = $opcionRepository;
    }

    public function calcularTotal($comisiones)
    {
        $total = 0;

        foreach ($comisiones as $comision) {
            $total += $comision->comision;
        }

        return floatval($total);
    }
}
