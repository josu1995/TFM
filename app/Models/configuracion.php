<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class configuracion extends Model
{
    use HasFactory;

    public function usuario(){
        return $this->belongsTo('App\Models\Usuario','usuario_id');
    }

    public function idioma(){
        return $this->belongsTo('App\Models\idioma','idioma_id');
    }

    public function dificultad(){
        return $this->belongsTo('App\Models\dificultad','dificultad_id');
    }
    
}
