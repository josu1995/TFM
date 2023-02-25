<?php

namespace App\Repositories;

// Modelos
use App\Models\Localidad;

class LocalidadRepository
{
    /**
     * Todas las localidades.
     *
     * @return Collection
     */
    public function getLocalidades()
    {
        return Localidad::all();
    }

    /**
     * Todas las localidades.
     *
     * @return Collection
     */
    public function localidadPorID($id)
    {
        return Localidad::first($id)->first();
    }

    /**
     * Localidades por nombre.
     *
     * @return Collection
     */
    public function localidadPorNombre($nombre, $numero = 10)
    {
        $localidades = Localidad::where('nombre', 'like', "%$nombre%")
                   ->orderBy('nombre', 'desc')
                   ->take($numero)
                   ->get();

        return $localidades;
    }

    /**
     * Localidades por código postal.
     *
     * @return Collection
     */
    public function localidadPorCodigoPostal($codigo_postal)
    {
        $localidades = Localidad::where('codigo_postal', 'like', "%$codigo_postal%")
                   ->orderBy('nombre', 'desc')
                   ->get();

        return $localidades;
    }

    /**
     * Localidades que tiene por lo menos un punto.
     *
     * @return Collection
     */
    public function localidadConPuntos($numero = 0)
    {
        if($numero) {
            $localidades = Localidad::has('puntos')
                        ->take($numero)
                        ->get();
        } else {
            $localidades = Localidad::has('puntos')->get();
        }

        return $localidades;
    }

    /**
     * Localidades por nombre y que tiene por lo menos un punto.
     *
     * @return Collection
     */
    public function localidadConPuntosPorNombre($nombre, $numero = 10)
    {
        $localidades = Localidad::has('puntos')
                    ->where('nombre', 'like', "%$nombre%")
                    ->orderBy('nombre', 'desc')
                    ->take($numero)
                    ->get();

        return $localidades;
    }
}
