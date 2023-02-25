<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dificultad extends Model
{
    use HasFactory;

    public function recurso(){
        return $this->hasMany('App\Models\dificultadRecurso','dificultad_id');
    }
    
}
