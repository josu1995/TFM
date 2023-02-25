<?php

namespace App\Services;

// Modelos

use App\Models\Categoria;

class AyudaService
{

    public function getSubCategorias($padreId) {
        $result = Categoria::where('categoria_padre_id', $padreId)->pluck('id')->toArray();
        $hijos = $result;
        foreach ($hijos as $id) {
            if(count($hijos)) {
                $subcategorias = $this->getSubCategorias($id);
                if ($subcategorias) {
                    $result = array_merge($result, $subcategorias);
                } else {
                    $result = $hijos;
                }
            }
        }
        return $result;
    }
}
