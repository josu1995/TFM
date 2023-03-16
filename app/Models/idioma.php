<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class idioma extends Model
{
    use HasFactory;

    public function recursos(){
        return $this->hasMany('App\Models\recurso','idioma_id');
    }

    public function configuraciones(){
        return $this->hasMany('App\Models\configuracion','idioma_id');
    }

    public function redacciones(){
        return $this->hasMany('App\Models\redaccion','idioma_id');
    }
}
