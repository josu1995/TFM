<?php

namespace App\Repositories;

// Modelos
use App\Models\usuario;
use App\Models\Punto;
use App\Models\Localidad;

class PuntoRepository
{
    /* Crear punto */
    public function crearPunto(Usuario $usuario, Localidad $localidad, $datos)
    {
        $punto = new Punto();
        $punto->nombre = $datos['nombre'];
        $punto->direccion = $datos['direccion'];
        $punto->telefono = $datos['telefono'];
        $punto->horario = $datos['horario'];
        $punto->usuario()->associate($usuario);
        $punto->localidad()->associate($localidad);
        $punto->save();

        return $punto;
    }

    /* Update de punto */
    public function actualizarPunto(Punto $punto, Localidad $localidad, $datos)
    {
        $punto->nombre = $datos['nombre'];
        $punto->direccion = $datos['direccion'];
        $punto->telefono = $datos['telefono'];
        $punto->horario = $datos['horario'];
        $punto->localidad()->associate($localidad);
        $punto->save();

        return $punto;
    }

    /**
     * Get de punto por nombre
     *
     * @return Collection
     */
    public function puntoPorNombre($nombre)
    {
        $puntos = Punto::where('nombre', 'like', "%$nombre%")
                   ->orderBy('nombre', 'desc')
                   ->get();

        return $puntos;
    }

    /**
     * Get de punto por localidad ID
     *
     * @return Collection
     */
    public function puntoPorLocalidad($idLocalidad)
    {
        $localidad = Localidad::find($idLocalidad);

        return $localidad->puntos()->get();
    }

    /**
     * Puntos por código postal de localidad.
     *
     * @return Array
     */
    public function puntosPorCodigoPostal($codigo_postal)
    {
        $puntos = [];
        $localidades = Localidad::where('codigo_postal', 'like', "%$codigo_postal%")
                   ->orderBy('nombre', 'desc')
                   ->get();

        foreach ($localidades as $key => $value) {
            $puntos[] = $value->puntos()->get();
        }

        return $puntos;
    }

}
