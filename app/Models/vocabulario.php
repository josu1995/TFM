<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vocabulario extends Model
{
    use HasFactory;

    public function recursos(){
        return $this->hasMany('App\Models\recurso','idioma_id');
    }

    public function familia(){
        return $this->belongsTo('App\Models\familiaRecurso','familia_id');
    }
}
