<?php

namespace App\Services;

// Modelos
use App\Models\Alerta;

class AlertaService
{

    public function validateDiasFormat($dias)
    {
        $diasArray = explode(',', $dias);
        foreach ($diasArray as $dia) {
            if(!is_numeric($dia) || floor($dia) != $dia || ($dia > 7 || $dia < 1)) {
                return false;
            }
        }
        return true;
    }
}
