<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class ModelHelper {

    /**
     * Rellenar un modelo con datos recibidos por array
     *
     * @return Model
     */
    public static function rellenarModelo(Model $modelo, $datos)
    {
        foreach ($datos as $campo => $valor) {
            $modelo->$campo = $valor;
        }

        return $modelo;
    }
}
