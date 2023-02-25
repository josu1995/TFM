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

class StoresRepository
{
    // Reglas de validación
    public $reglas = [
        'nombre' => 'required',
        'email' => 'required|email|max:255|min:5',
        'telefono' => 'required|phone:ES',
        'localidad' => 'required',
        'direccion' => 'required',
        'referencia' => 'required',
    ];

    // Mensajes de devolución de validación
    public $mensajes = [
        'required' => 'El campo :attribute es obligatorio',
        'email.min' => 'El email no debe ser inferior a :max caracteres',
        'email.max' => 'El email no debe ser superior a :max caracteres',
        'email.email' => 'El email no tiene un formato correcto',
        'telefono.required' => 'El campo teléfono es obligatorio',
        'telefono.phone' => 'El teléfono no tiene un formato correcto',
        'direccion.required' => 'El campo dirección es obligatorio',
    ];

}
