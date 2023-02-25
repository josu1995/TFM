<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dificultadRecurso extends Model
{
    use HasFactory;

    public function recurso(){
        return $this->belongsTo('App\Models\recurso','recurso_id');
    }

    public function dificultad(){
        return $this->belongsTo('App\Models\dificultad','dificultad_id');
    }
}
