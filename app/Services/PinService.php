<?php

namespace App\Services;

// Modelos
use App\Models\Comision;

// Repositorios
use App\Models\Envio;
use App\Repositories\OpcionRepository;

class PinService
{

    public function generatePin()
    {
        $pool = '0123456789';
        $pin = null;
        do {
            $pin = substr(str_shuffle(str_repeat($pool, 4)), 0, 4);
        } while (Envio::where('pin_recogida', $pin)->count());

        return $pin;
    }
}
