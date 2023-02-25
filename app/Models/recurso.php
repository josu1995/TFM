<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recurso extends Model
{
    use HasFactory;

    public function idioma(){
        return $this->belongsTo('App\Models\idioma','idioma_id');
    }

    public function vocabulario(){
        return $this->belongsTo('App\Models\vocabulario','vocabulario_id');
    }
    
    public function dificultad(){
        return $this->hasMany('App\Models\dificultadRecurso','recurso_id');
    }
}